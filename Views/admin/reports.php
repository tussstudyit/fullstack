<?php
require_once __DIR__ . '/../../config.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('/index.php');
    exit;
}

$conn = getDB();
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 10;
$reports = [];
$total_reports = 0;
$total_pages = 0;

try {
    // Count total
    $count_stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM reports");
    $count_stmt->execute();
    $count_result = $count_stmt->fetch(PDO::FETCH_ASSOC);
    $total_reports = $count_result['cnt'] ?? 0;
    $total_pages = ceil($total_reports / $per_page);
    
    $stmt = $conn->prepare("
        SELECT r.id, r.reason, r.status, r.created_at, 
               u.full_name as reporter_name,
               p.title as post_title
        FROM reports r
        LEFT JOIN users u ON r.user_id = u.id
        LEFT JOIN posts p ON r.post_id = p.id
        ORDER BY r.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$per_page, ($page - 1) * $per_page]);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
}

// Xử lý cập nhật trạng thái báo cáo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $report_id = intval($_POST['report_id']);
    $action = $_POST['action'];
    
    try {
        if ($action === 'resolve') {
            $stmt = $conn->prepare("UPDATE reports SET status = 'resolved' WHERE id = ?");
            $stmt->execute([$report_id]);
        } elseif ($action === 'dismiss') {
            $stmt = $conn->prepare("UPDATE reports SET status = 'dismissed' WHERE id = ?");
            $stmt->execute([$report_id]);
        }
        header("Location: reports.php");
        exit;
    } catch (PDOException $e) {
        error_log("Error updating report: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo Vi Phạm - Admin</title>
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

        .badge-pending {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .badge-resolved {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .badge-dismissed {
            background: rgba(107, 114, 128, 0.1);
            color: #6b7280;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .pagination a {
            text-decoration: none;
        }

        .pagination button {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            background: white;
            border-radius: var(--radius-md);
            cursor: pointer;
        }

        .pagination button.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        @media (max-width: 1024px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                display: none;
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
                <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="posts.php"><i class="fas fa-home"></i> Quản lý bài đăng</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Quản lý người dùng</a></li>
                <li><a href="reports.php" class="active"><i class="fas fa-flag"></i> Báo cáo vi phạm</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Cài đặt</a></li>
                <li><a href="../../Controllers/AuthController.php?action=logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <h1>Báo Cáo Vi Phạm</h1>
                    <p style="color: var(--text-secondary); margin-top: 0.25rem;">Tổng cộng: <?php echo $total_reports; ?> báo cáo</p>
                </div>
            </div>

            <div class="admin-content">
                <div class="data-table">
                    <div class="table-header">
                        <h3>Danh sách báo cáo</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Người báo cáo</th>
                                <th>Bài đăng</th>
                                <th>Lý do</th>
                                <th>Trạng thái</th>
                                <th>Ngày báo cáo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reports)): ?>
                                <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($report['id']); ?></td>
                                    <td><?php echo htmlspecialchars($report['reporter_name'] ?? 'Unknown'); ?></td>
                                    <td><?php echo htmlspecialchars(substr($report['post_title'] ?? 'Deleted', 0, 30)); ?></td>
                                    <td><?php echo htmlspecialchars($report['reason']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $report['status']; ?>">
                                            <?php 
                                            $status_labels = [
                                                'pending' => 'Chờ xử lý',
                                                'resolved' => 'Đã xử lý',
                                                'dismissed' => 'Bỏ qua'
                                            ];
                                            echo $status_labels[$report['status']] ?? $report['status'];
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($report['created_at'])); ?></td>
                                    <td>
                                        <?php if ($report['status'] === 'pending'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                            <button type="submit" name="action" value="resolve" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="submit" name="action" value="dismiss" class="btn btn-sm btn-outline">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; color: var(--text-secondary);">
                                        Không có báo cáo
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>">
                        <button <?php echo $i === $page ? 'class="active"' : ''; ?>><?php echo $i; ?></button>
                    </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
