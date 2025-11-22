<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Models/Post.php';
require_once __DIR__ . '/../../helpers.php';

// Redirect if not logged in or not landlord
if (!isLoggedIn() || $_SESSION['role'] !== 'landlord') {
    redirect('/fullstack/index.php');
}

// Get user's posts
$postModel = new Post();
$posts = $postModel->getByUserId($_SESSION['user_id']);
$totalPosts = count($posts);
?>
<!DOCTYPE html>
<html lang=\"vi\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Quản lý tin đăng - Tìm Trọ Sinh Viên</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            text-align: center;
        }

        .content-wrapper {
            padding: 3rem 0;
        }

        .posts-list {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .post-item {
            display: grid;
            grid-template-columns: 200px 1fr auto;
            gap: 1.5rem;
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            transition: background 0.3s ease;
        }

        .post-item:hover {
            background: var(--light-color);
        }

        .post-item:last-child {
            border-bottom: none;
        }

        .post-image img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: var(--radius-md);
        }

        .post-info h3 {
            margin-bottom: 0.5rem;
            font-size: 1.25rem;
        }

        .post-meta {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
        }

        .post-stats {
            display: flex;
            gap: 1.5rem;
            margin-top: 0.75rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .post-actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-end;
        }

        @media (max-width: 768px) {
            .post-item {
                grid-template-columns: 1fr;
            }

            .post-actions {
                flex-direction: row;
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="../../index.php" class="logo">
                <i class="fas fa-home"></i>
                <span>Tìm Trọ SV</span>
            </a>

            <ul class="nav-menu">
                <li><a href="../../index.php" class="nav-link">Trang chủ</a></li>
                <li><a href="../posts/list.php" class="nav-link">Danh sách trọ</a></li>
                <li><a href="../posts/create.php" class="nav-link active">Đăng tin</a></li>
                <li><a href="../chat/chat.php" class="nav-link">Tin nhắn</a></li>
            </ul>

            <div class="nav-actions">
                <div style="position: relative; display: inline-block;">
                    <a href="notifications.php" class="btn btn-outline btn-sm" title="Thông báo">
                        <i class="fas fa-bell"></i> Thông báo
                    </a>
                    <?php 
                    require_once __DIR__ . '/../../Models/Notification.php';
                    $notifModel = new Notification();
                    $unread = $notifModel->getUnreadCount($_SESSION['user_id']);
                    if ($unread > 0): 
                    ?>
                    <span style="position: absolute; top: -5px; right: -5px; background: var(--danger-color); color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">
                        <?php echo $unread > 99 ? '99+' : $unread; ?>
                    </span>
                    <?php endif; ?>
                </div>
                <a href="#" class="btn btn-outline btn-sm"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></a>
                <a href="../../Controllers/AuthController.php?action=logout" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </div>

            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <div class="page-header">
        <h1>Quản Lý Tin Đăng</h1>
        <p>Quản lý các bài đăng phòng trọ của bạn</p>
    </div>

    <div class="content-wrapper">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h2>Tin đăng của tôi (<?php echo $totalPosts; ?>)</h2>
                </div>
                <a href="../posts/create.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Đăng tin mới
                </a>
            </div>

            <div class="posts-list">
                <?php if (empty($posts)): ?>
                <div style="padding: 3rem; text-align: center; color: var(--text-light);">
                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                    <p>Bạn chưa có tin đăng nào. <a href="../posts/create.php" style="color: var(--primary-color); font-weight: 500;">Đăng tin ngay</a></p>
                </div>
                <?php else: ?>
                <?php foreach ($posts as $post): ?>
                <div class="post-item" data-post-id="<?php echo $post['id']; ?>">
                    <div class="post-image">
                        <img src="<?php echo getPlaceholderImage(200, 150, '667eea', urlencode($post['title'])); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    </div>
                    <div class="post-info">
                        <span class="badge badge-success">Đã duyệt</span>
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <div class="post-meta">
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($post['address'] . ', ' . $post['district'] . ', ' . $post['city']); ?>
                        </div>
                        <div style="color: var(--primary-color); font-size: 1.25rem; font-weight: 700;">
                            <?php echo number_format($post['price']); ?> đ/tháng
                        </div>
                        <div class="post-stats">
                            <div class="stat-item">
                                <i class="fas fa-eye"></i>
                                <span><?php echo $post['views'] ?? 0; ?> lượt xem</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-clock"></i>
                                <span>Đăng <?php echo timeAgo(strtotime($post['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="post-actions">
                        <a href="../posts/detail.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-eye"></i> Xem
                        </a>
                        <a href="../posts/create.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="if(confirmDelete('Bạn có chắc muốn xóa tin này?')) { deletePost(<?php echo $post['id']; ?>); }">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
