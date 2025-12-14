<?php
require_once __DIR__ . '/../../config.php';

// Redirect if not logged in or not tenant
if (!isLoggedIn() || $_SESSION['role'] !== 'tenant') {
    redirect(BASE_PATH . 'index.php');
}

require_once __DIR__ . '/../../Models/Favorite.php';
require_once __DIR__ . '/../../Models/Post.php';
require_once __DIR__ . '/../../Models/PostImage.php';
require_once __DIR__ . '/../../helpers.php';

$favoriteModel = new Favorite();
$postModel = new Post();
$postImageModel = new PostImage();
$conn = getDB();

// Lấy danh sách yêu thích của user
$favorites = [];
try {
    $stmt = $conn->prepare("
        SELECT p.id, p.title, p.description, p.address, p.district, p.city, p.price, p.area, p.status, p.slug
        FROM posts p
        JOIN favorites f ON p.id = f.post_id
        WHERE f.user_id = ? AND p.status = 'approved'
        ORDER BY f.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $favorites = $stmt->fetchAll();
    
    // Lấy ảnh cho mỗi post
    foreach ($favorites as &$post) {
        $post['image'] = $postImageModel->getPrimaryImage($post['id']);
    }
    unset($post);
} catch (PDOException $e) {
    error_log("Error fetching favorites: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu thích - NhaTot</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-header {
            background: #3470f3ff;
            color: white;
            padding: 3rem 0;
            text-align: center;
        }

        .content-wrapper {
            padding: 3rem 0;
        }

        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
        }

        .post-card {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .post-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-5px);
        }

        .post-image {
            position: relative;
            height: 220px;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .post-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .favorite-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: white;
            color: var(--danger-color);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            font-size: 1.25rem;
            transition: all 0.3s ease;
        }

        .favorite-btn:hover {
            transform: scale(1.1);
        }

        .favorite-btn.active i {
            color: var(--danger-color);
        }

        .post-content {
            padding: 1.5rem;
        }

        .post-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .post-location {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .post-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .post-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
        }

        .empty-state i {
            font-size: 5rem;
            color: var(--text-secondary);
            opacity: 0.3;
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .posts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="../../index.php" class="logo">
                <div class="logo-icon-box">
                    <i class="fas fa-home"></i>
                </div>
                <div class="logo-text">
                    <h1>NhaTot</h1>
                    <p>Nơi bạn thuộc về</p>
                </div>
            </a>

            <ul class="nav-menu">
                <li><a href="../../index.php" class="nav-link">Trang chủ</a></li>
                <li><a href="../posts/list.php" class="nav-link">Danh sách trọ</a></li>
                <li><a href="favorites.php" class="nav-link active">Yêu thích</a></li>
                <?php if (isLoggedIn()): ?>
                <li style="position: relative;">
                    <a href="../chat/chat.php" class="nav-link">Tin nhắn</a>
                    <?php 
                    try {
                        $db = getDB();
                        $unread_stmt = $db->prepare("
                            SELECT COUNT(DISTINCT c.id) as unread_conversations
                            FROM conversations c
                            INNER JOIN messages m ON m.conversation_id = c.id
                            WHERE m.is_read = 0 
                            AND m.sender_id != ?
                            AND (c.landlord_id = ? OR c.tenant_id = ?)
                        ");
                        $unread_stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
                        $unread_result = $unread_stmt->fetch(PDO::FETCH_ASSOC);
                        $unread_count = $unread_result['unread_conversations'] ?? 0;
                        
                        if ($unread_count > 0):
                    ?>
                    <span class="notification-badge" style="position: absolute; top: -5px; right: -10px; background: #ef4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.7rem; font-weight: 700; min-width: 18px; text-align: center;">
                        <?php echo $unread_count > 99 ? '99+' : $unread_count; ?>
                    </span>
                    <?php 
                        endif;
                    } catch (Exception $e) {
                        // Ignore errors
                    }
                    ?>
                </li>
                <?php endif; ?>
            </ul>

            <div class="nav-actions">
                <?php if (isLoggedIn()): ?>
                    <div class="notification-wrapper">
                        <button class="notification-bell-btn" onclick="toggleNotificationDropdown(event)" title="Thông báo">
                            <i class="fas fa-bell"></i>
                            <?php 
                            require_once '../../Models/Notification.php';
                            $notifModel = new Notification();
                            $unread = $notifModel->getUnreadCount($_SESSION['user_id']);
                            if ($unread > 0): 
                            ?>
                            <span class="notification-badge">
                                <?php echo $unread > 99 ? '99+' : $unread; ?>
                            </span>
                            <?php endif; ?>
                        </button>
                        <div class="notification-dropdown" id="notificationDropdown">
                            <div class="notification-dropdown-header">
                                <h3>Thông báo</h3>
                                <button class="mark-all-read-btn" onclick="markAllNotificationsAsRead()">Đánh dấu tất cả đã đọc</button>
                            </div>
                            <div class="notification-dropdown-list" id="notificationList">
                                <div class="notification-empty">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <p>Đang tải...</p>
                                </div>
                            </div>
                            <div class="notification-dropdown-footer">
                                <a href="notifications.php">Xem tất cả thông báo</a>
                            </div>
                        </div>
                    </div>
                    <div class="user-menu-wrapper" style="position: relative;">
                        <button class="user-avatar-btn" onclick="toggleUserMenu(event)">
                            <?php
                            // Load user avatar from database
                            try {
                                $db = getDB();
                                $user_stmt = $db->prepare("SELECT avatar FROM users WHERE id = ?");
                                $user_stmt->execute([$_SESSION['user_id']]);
                                $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);
                                $avatar_src = (!empty($user_data['avatar'])) 
                                    ? '../../uploads/avatars/' . htmlspecialchars($user_data['avatar']) 
                                    : 'https://via.placeholder.com/40/3b82f6/ffffff?text=' . strtoupper(substr($_SESSION['username'], 0, 1));
                            } catch (Exception $e) {
                                $avatar_src = 'https://via.placeholder.com/40/3b82f6/ffffff?text=' . strtoupper(substr($_SESSION['username'], 0, 1));
                            }
                            ?>
                            <img src="<?php echo $avatar_src; ?>" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #3b82f6; cursor: pointer;">
                        </button>
                        <div class="user-dropdown-menu" id="userDropdownMenu" style="display: none;">
                            <a href="profile.php" class="dropdown-item">
                                <i class="fas fa-user-circle"></i> Hồ sơ
                            </a>
                            <a href="../../Controllers/AuthController.php?action=logout" class="dropdown-item logout">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <div class="page-header">
        <h1>Phòng Trọ Yêu Thích</h1>
        <p>Các phòng trọ bạn đã lưu</p>
    </div>

    <div class="content-wrapper">
        <div class="container">
            <div style="margin-bottom: 2rem;">
                <h2>Danh sách yêu thích (<?php echo count($favorites); ?>)</h2>
            </div>

            <?php if (empty($favorites)): ?>
                <div class="empty-state">
                    <i class="fas fa-heart"></i>
                    <h3>Chưa có bài đăng yêu thích</h3>
                    <p>Hãy lưu các bài đăng bạn thích để xem sau</p>
                    <a href="../posts/list.php" class="btn btn-primary" style="margin-top: 1rem;">Tìm phòng trọ</a>
                </div>
            <?php else: ?>
                <div class="posts-grid">
                    <?php foreach ($favorites as $post): ?>
                    <?php 
                    $imageUrl = $post['image'] ? getBasePath() . '/uploads/' . htmlspecialchars($post['image']) : getPlaceholderImage(320, 220, '667eea', urlencode($post['title']));
                    ?>
                    <div class="post-card" data-post-id="<?php echo $post['id']; ?>">
                        <div class="post-image">
                            <img src="<?php echo $imageUrl; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <button class="favorite-btn active" onclick="toggleFavorite(<?php echo $post['id']; ?>, this); if(!this.classList.contains('active')) this.closest('.post-card').remove();">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <div class="post-content">
                            <h3 class="post-title"><?php echo htmlspecialchars(substr($post['title'], 0, 50)); ?></h3>
                            <div class="post-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($post['address'] . ', ' . $post['district']); ?></span>
                            </div>
                            <div class="post-footer">
                                <div class="post-price"><?php echo number_format($post['price']); ?>đ</div>
                                <a href="../posts/detail.php?slug=<?php echo $post['slug']; ?>" class="btn btn-primary btn-sm">Chi tiết</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleUserMenu(event) {
            event.stopPropagation();
            const menu = document.getElementById('userDropdownMenu');
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        }
        
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('userDropdownMenu');
            const userWrapper = document.querySelector('.user-menu-wrapper');
            if (menu && !userWrapper.contains(event.target)) {
                menu.style.display = 'none';
            }
        });

        function toggleFavorite(postId, button) {
            const isFavorited = button.classList.contains('active');
            
            fetch('../../Controllers/FavoriteController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=' + (isFavorited ? 'remove' : 'add') + '&post_id=' + postId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.classList.toggle('active');
                    if (!button.classList.contains('active')) {
                        button.closest('.post-card').remove();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
    <script src="../../assets/js/notifications.js"></script>
</body>
</html>
