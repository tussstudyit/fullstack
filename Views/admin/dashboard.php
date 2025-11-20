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
} catch (PDOException $e) {
    $total_posts = 0;
    $total_users = 0;
    $pending_posts = 0;
    $pending_reports = 0;
}

// Lấy danh sách bài đăng chờ duyệt
$pending_list = [];
try {
    $stmt = $conn->prepare("
        SELECT p.id, p.title, p.created_at, u.full_name
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.status = 'pending'
        ORDER BY p.created_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    $pending_list = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching pending posts: " . $e->getMessage());
}

// Xử lý phê duyệt/từ chối bài đăng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['post_id'])) {
        $post_id = intval($_POST['post_id']);
        $action = $_POST['action'];
        
        try {
            if ($action === 'approve') {
                $stmt = $conn->prepare("UPDATE posts SET status = 'approved', updated_at = NOW() WHERE id = ?");
                $stmt->execute([$post_id]);
                
                // Tạo notification cho user
                $stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
                $stmt->execute([$post_id]);
                $post = $stmt->fetch();
                
                if ($post) {
                    $stmt = $conn->prepare("
                        INSERT INTO notifications (recipient_id, sender_id, type, title, message, related_post_id, is_read)
                        VALUES (?, 1, 'post_approved', 'Bài viết được phê duyệt', 'Bài viết của bạn đã được phê duyệt', ?, 0)
                    ");
                    $stmt->execute([$post['user_id'], $post_id]);
                }
                
                $message = "Đã phê duyệt bài đăng";
            } elseif ($action === 'reject') {
                $stmt = $conn->prepare("UPDATE posts SET status = 'rejected', updated_at = NOW() WHERE id = ?");
                $stmt->execute([$post_id]);
                
                // Tạo notification cho user
                $stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
                $stmt->execute([$post_id]);
                $post = $stmt->fetch();
                
                if ($post) {
                    $stmt = $conn->prepare("
                        INSERT INTO notifications (recipient_id, sender_id, type, title, message, related_post_id, is_read)
                        VALUES (?, 1, 'post_rejected', 'Bài viết bị từ chối', 'Bài viết của bạn bị từ chối vì không tuân thủ quy tắc', ?, 0)
                    ");
                    $stmt->execute([$post['user_id'], $post_id]);
                }
                
                $message = "Đã từ chối bài đăng";
            }
            
            // Reload pending list
            $stmt = $conn->prepare("
                SELECT p.id, p.title, p.created_at, u.full_name
                FROM posts p
                JOIN users u ON p.user_id = u.id
                WHERE p.status = 'pending'
                ORDER BY p.created_at DESC
                LIMIT 5
            ");
            $stmt->execute();
            $pending_list = $stmt->fetchAll();
            
            // Reload pending count
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM posts WHERE status = 'pending'");
            $stmt->execute();
            $pending_posts = $stmt->fetch()['count'];
        } catch (PDOException $e) {
            error_log("Error updating post status: " . $e->getMessage());
        }
    }
}
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

        @media (max-width: 1024px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                display: none;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .stats-grid {
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
                <li><a href="<?php echo '/fullstack/Controllers/AuthController.php?action=logout'; ?>"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
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

                <div class="data-table">
                    <div class="table-header">
                        <h3>Bài đăng chờ phê duyệt</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tiêu đề</th>
                                <th>Người đăng</th>
                                <th>Ngày đăng</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pending_list)): ?>
                                <?php foreach ($pending_list as $post): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($post['id']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($post['title'], 0, 50)); ?></td>
                                    <td><?php echo htmlspecialchars($post['full_name']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></td>
                                    <td><span class="badge badge-warning">Chờ duyệt</span></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                            <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Duyệt
                                            </button>
                                            <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">
                                                <i class="fas fa-times"></i> Từ chối
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; color: var(--text-secondary);">
                                        Không có bài đăng chờ phê duyệt
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
