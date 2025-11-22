<?php
require_once __DIR__ . '/../../config.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('/fullstack/index.php');
    exit;
}

$conn = getDB();
$search = sanitize($_GET['search'] ?? '');
$status_filter = sanitize($_GET['status'] ?? '');
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 10;
$posts = [];
$total_posts = 0;
$total_pages = 0;

try {
    $query = "SELECT p.id, p.title, p.address, p.price, p.status, p.created_at, u.full_name 
              FROM posts p
              JOIN users u ON p.user_id = u.id
              WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $query .= " AND (p.title LIKE ? OR p.address LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    if (!empty($status_filter)) {
        $query .= " AND p.status = ?";
        $params[] = $status_filter;
    }
    
    // Count total
    $count_query = str_replace("SELECT p.id, p.title, p.address, p.price, p.status, p.created_at, u.full_name", "SELECT COUNT(*) as cnt", $query);
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->execute($params);
    $count_result = $count_stmt->fetch(PDO::FETCH_ASSOC);
    $total_posts = $count_result['cnt'] ?? 0;
    $total_pages = ceil($total_posts / $per_page);
    
    $query .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $per_page;
    $params[] = ($page - 1) * $per_page;
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
}

// Xử lý xóa bài đăng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $post_id = intval($_POST['post_id']);
    try {
        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        header("Location: posts.php");
        exit;
    } catch (PDOException $e) {
        error_log("Error deleting post: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Bài Đăng - Admin</title>
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

        .filter-box {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 1rem;
            align-items: end;
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

        .badge-approved {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .badge-pending {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .badge-rejected {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
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

            .filter-box {
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
                <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="posts.php" class="active"><i class="fas fa-home"></i> Quản lý bài đăng</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Quản lý người dùng</a></li>
                <li><a href="reports.php"><i class="fas fa-flag"></i> Báo cáo vi phạm</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Cài đặt</a></li>
                <li><a href="../../Controllers/AuthController.php?action=logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <h1>Quản lý Bài Đăng</h1>
                    <p style="color: var(--text-secondary); margin-top: 0.25rem;">Tổng cộng: <?php echo $total_posts; ?> bài đăng</p>
                </div>
            </div>

            <div class="admin-content">
                <div class="filter-box">
                    <form method="GET" style="display: contents;">
                        <div>
                            <label>Tìm kiếm</label>
                            <input type="text" name="search" class="form-control" placeholder="Tìm theo tiêu đề, địa chỉ..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div>
                            <label>Trạng thái</label>
                            <select name="status" class="form-control">
                                <option value="">Tất cả</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                                <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Đã duyệt</option>
                                <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Từ chối</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </form>
                </div>

                <div class="data-table">
                    <div class="table-header">
                        <h3>Danh sách bài đăng</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tiêu đề</th>
                                <th>Người đăng</th>
                                <th>Giá</th>
                                <th>Trạng thái</th>
                                <th>Ngày đăng</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($posts)): ?>
                                <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($post['id']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($post['title'], 0, 40)); ?></td>
                                    <td><?php echo htmlspecialchars($post['full_name']); ?></td>
                                    <td><?php echo number_format($post['price'], 0, ',', '.'); ?> VND</td>
                                    <td>
                                        <span class="badge badge-<?php echo $post['status']; ?>">
                                            <?php 
                                            $status_labels = [
                                                'pending' => 'Chờ duyệt',
                                                'approved' => 'Đã duyệt',
                                                'rejected' => 'Từ chối'
                                            ];
                                            echo $status_labels[$post['status']] ?? $post['status'];
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></td>
                                    <td>
                                        <a href="../../Views/posts/detail.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Xác nhận xóa bài đăng này?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; color: var(--text-secondary);">
                                        Không tìm thấy bài đăng
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?>">
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
