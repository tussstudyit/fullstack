<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Models/Notification.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect(BASE_PATH . 'Views/auth/login.php');
}

$notificationModel = new Notification();
$notifications = $notificationModel->getNotifications($_SESSION['user_id'], 20, 0);
$unread_count = $notificationModel->getUnreadCount($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo - NhaTot</title>
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

        .notifications-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .notification-header h2 {
            margin: 0;
        }

        .mark-all-btn {
            font-size: 0.875rem;
        }

        .notifications-list {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .notification-item {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .notification-item:hover {
            background: var(--light-color);
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item.unread {
            background: rgba(102, 126, 234, 0.05);
            border-left: 4px solid var(--primary-color);
        }

        a:has(.notification-item) {
            text-decoration: none;
        }

        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .notification-icon.info {
            background: #e3f2fd;
            color: #1976d2;
        }

        .notification-icon.success {
            background: #e8f5e9;
            color: #388e3c;
        }

        .notification-icon.warning {
            background: #fff3e0;
            color: #f57c00;
        }

        .notification-icon.danger {
            background: #ffebee;
            color: #d32f2f;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--text-primary);
        }

        .notification-message {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .notification-preview {
            margin-top: 0.75rem;
            padding: 0.75rem;
            background: var(--light-color);
            border-left: 3px solid var(--primary-color);
            border-radius: 0.25rem;
            font-size: 0.875rem;
            max-height: 100px;
            overflow: hidden;
        }

        .notification-preview.message {
            border-left-color: var(--success-color);
            font-style: italic;
        }

        .notification-preview-user {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .notification-preview-user strong {
            color: var(--text-primary);
        }

        .notification-preview-rating {
            margin-left: 0.5rem;
            color: #fbbf24;
            font-size: 0.85rem;
        }

        .notification-time {
            color: #999;
            font-size: 0.75rem;
        }

        .notification-actions {
            display: flex;
            gap: 0.75rem;
            margin-left: 1rem;
        }

        .notification-actions button {
            padding: 0.5rem;
            border: none;
            background: none;
            cursor: pointer;
            color: var(--text-secondary);
            transition: color 0.3s ease;
        }

        .notification-actions button:hover {
            color: var(--danger-color);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--text-secondary);
            opacity: 0.3;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .notification-item {
                padding: 1rem;
            }

            .notification-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .notification-actions {
                flex-direction: column;
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
                <?php if ($_SESSION['role'] === 'landlord'): ?>
                <li><a href="../posts/create.php" class="nav-link">Đăng tin</a></li>
                <?php endif; ?>
                <?php if ($_SESSION['role'] === 'tenant'): ?>
                <li><a href="favorites.php" class="nav-link">Yêu thích</a></li>
                <?php endif; ?>
                <li><a href="../chat/chat.php" class="nav-link">Tin nhắn</a></li>
            </ul>

            <div class="nav-actions">
                    <div class="user-menu-wrapper" style="position: relative;">
                        <button class="user-avatar-btn" onclick="toggleUserMenu(event)">
                            <?php
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
            </div>

            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <div class="page-header">
        <h1>Thông Báo</h1>
        <p>Các thông báo của bạn</p>
    </div>

    <div class="content-wrapper">
        <div class="container">
            <div class="notifications-container">
                <div class="notification-header">
                    <div>
                        <h2>Thông báo (<?php echo count($notifications); ?>)</h2>
                        <?php if ($unread_count > 0): ?>
                        <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0;">
                            <?php echo $unread_count; ?> thông báo chưa đọc
                        </p>
                        <?php endif; ?>
                    </div>
                    <?php if ($unread_count > 0): ?>
                    <button class="btn btn-outline btn-sm mark-all-btn" onclick="markAllAsRead()">
                        <i class="fas fa-check-double"></i> Đánh dấu tất cả đã đọc
                    </button>
                    <?php endif; ?>
                </div>

                <?php if (count($notifications) > 0): ?>
                    <div class="notifications-list">
                        <?php foreach ($notifications as $notification): 
                            // Build correct URL - link already has relative path
                            $notif_url = htmlspecialchars($notification['link'] ?? '#');
                        ?>
                        <a href="<?php echo $notif_url; ?>" style="text-decoration: none; color: inherit;">
                        <div class="notification-item <?php echo !$notification['is_read'] ? 'unread' : ''; ?>" data-id="<?php echo $notification['id']; ?>">
                            <div class="notification-icon <?php 
                                $icon_class = 'info';
                                $icon = 'fa-bell';
                                
                                if ($notification['type'] === 'comment') {
                                    $icon_class = 'info';
                                    $icon = 'fa-comment';
                                } elseif ($notification['type'] === 'rating') {
                                    $icon_class = 'success';
                                    $icon = 'fa-star';
                                } elseif ($notification['type'] === 'reply') {
                                    $icon_class = 'info';
                                    $icon = 'fa-reply';
                                } elseif ($notification['type'] === 'message') {
                                    $icon_class = 'success';
                                    $icon = 'fa-envelope';
                                } elseif ($notification['type'] === 'post_like') {
                                    $icon_class = 'danger';
                                    $icon = 'fa-heart';
                                } elseif ($notification['type'] === 'post_approved') {
                                    $icon_class = 'success';
                                    $icon = 'fa-check';
                                } elseif ($notification['type'] === 'post_rejected') {
                                    $icon_class = 'danger';
                                    $icon = 'fa-times';
                                }
                                echo $icon_class;
                            ?>">
                                <i class="fas <?php echo $icon; ?>"></i>
                            </div>

                            <div class="notification-content">
                                <div class="notification-title"><?php echo htmlspecialchars($notification['title']); ?></div>
                                <div class="notification-message"><?php echo htmlspecialchars($notification['message'] ?? ''); ?></div>
                                <div class="notification-time">
                                    <?php 
                                    $time = strtotime($notification['created_at']);
                                    $now = time();
                                    $diff = $now - $time;
                                    
                                    if ($diff < 60) {
                                        echo 'Vừa xong';
                                    } elseif ($diff < 3600) {
                                        echo floor($diff / 60) . ' phút trước';
                                    } elseif ($diff < 86400) {
                                        echo floor($diff / 3600) . ' giờ trước';
                                    } else {
                                        echo date('d/m/Y H:i', $time);
                                    }
                                    ?>
                                </div>
                                
                                <!-- Preview box for comment/rating/reply -->
                                <?php 
                                if (in_array($notification['type'], ['comment', 'rating', 'reply'])) {
                                    // Extract comment ID from link
                                    preg_match('/comment-(\d+)/', $notification['link'] ?? '', $matches);
                                    if (!empty($matches[1])) {
                                        $comment_id = (int)$matches[1];
                                        try {
                                            $conn = getDB();
                                            $stmt = $conn->prepare("
                                                SELECT c.content, c.rating, u.username 
                                                FROM comments c 
                                                JOIN users u ON c.user_id = u.id 
                                                WHERE c.id = ? 
                                                LIMIT 1
                                            ");
                                            $stmt->execute([$comment_id]);
                                            $comment = $stmt->fetch(PDO::FETCH_ASSOC);
                                            
                                            if ($comment) {
                                                $preview = substr($comment['content'], 0, 150);
                                                if (strlen($comment['content']) > 150) {
                                                    $preview .= '...';
                                                }
                                                ?>
                                                <div class="notification-preview">
                                                    <div class="notification-preview-user">
                                                        <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                                                        <?php if ($comment['rating'] > 0): ?>
                                                            <span class="notification-preview-rating">
                                                                <?php for ($i = 0; $i < $comment['rating']; $i++) echo '★'; ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div style="color: var(--text-primary);">
                                                        <?php echo htmlspecialchars($preview); ?>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        } catch (Exception $e) {
                                            error_log("Error fetching comment preview: " . $e->getMessage());
                                        }
                                    }
                                } elseif ($notification['type'] === 'message') {
                                    ?>
                                    <div class="notification-preview message">
                                        <?php echo htmlspecialchars(substr($notification['message'] ?? '', 0, 100)); ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>

                            <div class="notification-actions">
                                <?php if (!$notification['is_read']): ?>
                                <button onclick="event.preventDefault(); markAsRead(<?php echo $notification['id']; ?>)" title="Đánh dấu đã đọc">
                                    <i class="fas fa-check"></i>
                                </button>
                                <?php endif; ?>
                                <button onclick="event.preventDefault(); deleteNotification(<?php echo $notification['id']; ?>)" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-bell-slash"></i>
                        <h3>Không có thông báo</h3>
                        <p>Bạn không có thông báo nào lúc này</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
        function markAsRead(notificationId) {
            fetch('../../Controllers/NotificationController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=markAsRead&notification_id=' + notificationId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = document.querySelector(`[data-id="${notificationId}"]`);
                    if (item) {
                        item.classList.remove('unread');
                        item.querySelector('button').remove();
                    }
                }
            });
        }

        function markAllAsRead() {
            fetch('../../Controllers/NotificationController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=markAllAsRead'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function deleteNotification(notificationId) {
            if (confirm('Bạn chắc chắn muốn xóa thông báo này?')) {
                fetch('../../Controllers/NotificationController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=deleteNotification&notification_id=' + notificationId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const item = document.querySelector(`[data-id="${notificationId}"]`);
                        if (item) {
                            item.style.opacity = '0';
                            item.style.transform = 'translateX(100%)';
                            setTimeout(() => item.remove(), 300);
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>
