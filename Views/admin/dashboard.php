<?php
require_once __DIR__ . '/../../config.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect(BASE_PATH . 'index.php');
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
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tìm Trọ Sinh Viên</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
        }

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
            text-decoration: none;
        }

        .admin-menu li a:hover,
        .admin-menu li a.active {
            background: rgba(59, 130, 246, 0.1);
            color: #60a5fa;
            border-left: 3px solid #3b82f6;
            padding-left: calc(1.5rem - 3px);
        }

        .admin-main {
            background: #f3f4f6;
        }

        .admin-header {
            background: white;
            padding: 1.5rem 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
        }

        .admin-header h1 {
            color: #1f2937;
            font-size: 1.875rem;
            margin: 0;
        }

        .admin-header span {
            color: #6b7280;
        }

        .admin-content {
            padding: 2rem;
            overflow-y: auto;
            max-height: calc(100vh - 130px);
        }

        /* Custom scrollbar */
        .admin-content::-webkit-scrollbar {
            width: 6px;
        }

        .admin-content::-webkit-scrollbar-track {
            background: transparent;
        }

        .admin-content::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        .admin-content::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
        }

        .stat-card:nth-child(2)::before {
            background: linear-gradient(90deg, #10b981, #34d399);
        }

        .stat-card:nth-child(3)::before {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }

        .stat-card:nth-child(4)::before {
            background: linear-gradient(90deg, #ef4444, #f87171);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-icon.blue {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(96, 165, 250, 0.1));
            color: #3b82f6;
        }

        .stat-icon.green {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(52, 211, 153, 0.1));
            color: #10b981;
        }

        .stat-icon.yellow {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(251, 191, 36, 0.1));
            color: #f59e0b;
        }

        .stat-icon.red {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(248, 113, 113, 0.1));
            color: #ef4444;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .price-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .price-stat {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
        }

        .price-stat::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
        }

        .price-stat:nth-child(2)::before {
            background: linear-gradient(90deg, #10b981, #34d399);
        }

        .price-stat:nth-child(3)::before {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }

        .price-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .price-stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #3b82f6;
            margin-bottom: 0.5rem;
        }

        .price-stat:nth-child(2) .price-stat-value {
            color: #10b981;
        }

        .price-stat:nth-child(3) .price-stat-value {
            color: #f59e0b;
        }

        .price-stat-label {
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .chart-card h3 {
            margin: 0 0 1rem 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-card h3 i {
            color: #3b82f6;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .chart-card:nth-child(3) .chart-container {
            height: 350px;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .chart-card:nth-child(3) {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 768px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                display: none;
            }

            .admin-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .price-stats {
                grid-template-columns: 1fr;
            }

            .charts-grid {
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
