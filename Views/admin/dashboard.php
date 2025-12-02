<?php
require_once __DIR__ . '/../../config.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('/fullstack/index.php');
    exit;
}

require_once __DIR__ . '/../../Models/Post.php';
require_once __DIR__ . '/../../Models/User.php';

$postModel = new Post();
$userModel = new User();
$conn = getDB();

// Lấy thống kê
try {
    // Tổng bài đăng
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM posts");
    $stmt->execute();
    $total_posts = $stmt->fetch()['count'];
    
    // Tổng người dùng
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $total_users = $stmt->fetch()['count'];
    
    // Bài đăng chờ phê duyệt
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM posts WHERE status = 'pending'");
    $stmt->execute();
    $pending_posts = $stmt->fetch()['count'];
    
    // Báo cáo (từ bảng reports nếu có)
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM reports WHERE status = 'pending'");
    $stmt->execute();
    $pending_reports = $stmt->fetch()['count'];
    
    // Bài đăng theo trạng thái
    $stmt = $conn->prepare("
        SELECT status, COUNT(*) as count 
        FROM posts 
        GROUP BY status
    ");
    $stmt->execute();
    $posts_by_status = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Người dùng theo vai trò
    $stmt = $conn->prepare("
        SELECT role, COUNT(*) as count 
        FROM users 
        GROUP BY role
    ");
    $stmt->execute();
    $users_by_role = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Bài đăng trong 7 ngày gần nhất
    $stmt = $conn->prepare("
        SELECT DATE(created_at) as date, COUNT(*) as count 
        FROM posts 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    $stmt->execute();
    $posts_7days = $stmt->fetchAll();
    
    // Giá thuê trung bình
    $stmt = $conn->prepare("SELECT AVG(price) as avg_price, MIN(price) as min_price, MAX(price) as max_price FROM posts");
    $stmt->execute();
    $price_stats = $stmt->fetch();
    
} catch (PDOException $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    $total_posts = 0;
    $total_users = 0;
    $pending_posts = 0;
    $pending_reports = 0;
    $posts_by_status = [];
    $users_by_role = [];
    $posts_7days = [];
    $price_stats = ['avg_price' => 0, 'min_price' => 0, 'max_price' => 0];
}
// Chuẩn bị dữ liệu cho biểu đồ
$chart_posts_status = [
    'pending' => $posts_by_status['pending'] ?? 0,
    'approved' => $posts_by_status['approved'] ?? 0,
    'rejected' => $posts_by_status['rejected'] ?? 0
];

$chart_users_role = [
    'admin' => $users_by_role['admin'] ?? 0,
    'landlord' => $users_by_role['landlord'] ?? 0,
    'tenant' => $users_by_role['tenant'] ?? 0
];

// Chuẩn bị dữ liệu bài đăng 7 ngày
$dates_7days = [];
$counts_7days = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dates_7days[] = date('d/m', strtotime($date));
    
    $count = 0;
    foreach ($posts_7days as $data) {
        if ($data['date'] === $date) {
            $count = $data['count'];
            break;
        }
    }
    $counts_7days[] = $count;
}

$avg_price = number_format($price_stats['avg_price'] ?? 0, 0, ',', '.');
$min_price = number_format($price_stats['min_price'] ?? 0, 0, ',', '.');
$max_price = number_format($price_stats['max_price'] ?? 0, 0, ',', '.');
?>
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tìm Trọ Sinh Viên</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
        }

        .admin-sidebar {
            background: #1f2937;
            color: white;
            padding: 2rem 0;
        }

        .admin-logo {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 2rem;
        }

        .admin-logo h2 {
            color: white;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .admin-menu {
            list-style: none;
        }

        .admin-menu li a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .admin-menu li a:hover,
        .admin-menu li a.active {
            background: rgba(59, 130, 246, 0.1);
            color: #60a5fa;
            border-left: 3px solid #3b82f6;
        }

        .admin-main {
            background: #f3f4f6;
        }

        .admin-header {
            background: white;
            padding: 1.5rem 2rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-content {
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-icon.blue {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .stat-icon.green {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .stat-icon.yellow {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .stat-icon.red {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-secondary);
        }

        .data-table {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .table-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .table-header h3 {
            margin-bottom: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: var(--light-color);
        }

        th, td {
            padding: 1rem 1.5rem;
            text-align: left;
        }

        tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: background 0.3s ease;
        }

        tbody tr:hover {
            background: var(--light-color);
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }

        .chart-card h3 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            color: var(--text-dark);
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .price-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .price-stat {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            text-align: center;
        }

        .price-stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .price-stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .price-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <h2><i class="fas fa-shield-alt"></i> Admin Panel</h2>
            </div>

            <ul class="admin-menu">
                <li><a href="dashboard.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="posts.php"><i class="fas fa-home"></i> Quản lý bài đăng</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Quản lý người dùng</a></li>
                <li><a href="reports.php"><i class="fas fa-flag"></i> Báo cáo vi phạm</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Cài đặt</a></li>
                <li><a href="../../Controllers/AuthController.php?action=logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <h1>Dashboard</h1>
                    <p style="color: var(--text-secondary); margin-top: 0.25rem;">Tổng quan hệ thống</p>
                </div>
                <div>
                    <span>Xin chào, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></strong></span>
                </div>
            </div>

            <div class="admin-content">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($total_posts); ?></div>
                        <div class="stat-label">Tổng bài đăng</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($total_users); ?></div>
                        <div class="stat-label">Người dùng</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon yellow">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-value"><?php echo $pending_posts; ?></div>
                        <div class="stat-label">Chờ phê duyệt</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon red">
                            <i class="fas fa-flag"></i>
                        </div>
                        <div class="stat-value"><?php echo $pending_reports; ?></div>
                        <div class="stat-label">Báo cáo</div>
                    </div>
                </div>

                <!-- Price Statistics -->
                <div class="price-stats">
                    <div class="price-stat">
                        <div class="price-stat-value">đ<?php echo $avg_price; ?></div>
                        <div class="price-stat-label">Giá trung bình</div>
                    </div>
                    <div class="price-stat">
                        <div class="price-stat-value">đ<?php echo $min_price; ?></div>
                        <div class="price-stat-label">Giá thấp nhất</div>
                    </div>
                    <div class="price-stat">
                        <div class="price-stat-value">đ<?php echo $max_price; ?></div>
                        <div class="price-stat-label">Giá cao nhất</div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="charts-grid">
                    <!-- Posts Status Chart -->
                    <div class="chart-card">
                        <h3><i class="fas fa-pie-chart"></i> Trạng thái bài đăng</h3>
                        <div class="chart-container">
                            <canvas id="postsStatusChart"></canvas>
                        </div>
                    </div>

                    <!-- Users Role Chart -->
                    <div class="chart-card">
                        <h3><i class="fas fa-doughnut-chart"></i> Người dùng theo vai trò</h3>
                        <div class="chart-container">
                            <canvas id="usersRoleChart"></canvas>
                        </div>
                    </div>

                    <!-- Posts 7 Days Chart -->
                    <div class="chart-card" style="grid-column: 1 / -1;">
                        <h3><i class="fas fa-chart-line"></i> Bài đăng 7 ngày gần nhất</h3>
                        <div class="chart-container" style="height: 350px;">
                            <canvas id="posts7DaysChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script>
        // Posts Status Chart
        const postsStatusCtx = document.getElementById('postsStatusChart').getContext('2d');
        new Chart(postsStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Chờ duyệt', 'Đã duyệt', 'Từ chối'],
                datasets: [{
                    data: [<?php echo $chart_posts_status['pending']; ?>, <?php echo $chart_posts_status['approved']; ?>, <?php echo $chart_posts_status['rejected']; ?>],
                    backgroundColor: [
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        'rgba(245, 158, 11, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Users Role Chart
        const usersRoleCtx = document.getElementById('usersRoleChart').getContext('2d');
        new Chart(usersRoleCtx, {
            type: 'pie',
            data: {
                labels: ['Admin', 'Chủ trọ', 'Người thuê'],
                datasets: [{
                    data: [<?php echo $chart_users_role['admin']; ?>, <?php echo $chart_users_role['landlord']; ?>, <?php echo $chart_users_role['tenant']; ?>],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)'
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(236, 72, 153, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Posts 7 Days Chart
        const posts7DaysCtx = document.getElementById('posts7DaysChart').getContext('2d');
        new Chart(posts7DaysCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates_7days); ?>,
                datasets: [{
                    label: 'Bài đăng',
                    data: <?php echo json_encode($counts_7days); ?>,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
