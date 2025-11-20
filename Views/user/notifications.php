<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Models/Notification.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('/fullstack/Views/auth/login.php');
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
    <title>Thông báo - Tìm Trọ Sinh Viên</title>
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
                <i class="fas fa-home"></i>
                <span>Tìm Trọ SV</span>
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
                <a href="profile.php" class="btn btn-outline btn-sm"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                <a href="../../Controllers/AuthController.php?action=logout" class="btn btn-danger btn-sm">Đăng xuất</a>
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
                        <?php foreach ($notifications as $notification): ?>
                        <div class="notification-item <?php echo !$notification['is_read'] ? 'unread' : ''; ?>" data-id="<?php echo $notification['id']; ?>">
                            <div class="notification-icon <?php 
                                $icon_class = 'info';
                                if (strpos($notification['type'], 'success') !== false) {
                                    $icon_class = 'success';
                                } elseif (strpos($notification['type'], 'warning') !== false) {
                                    $icon_class = 'warning';
                                } elseif (strpos($notification['type'], 'danger') !== false) {
                                    $icon_class = 'danger';
                                }
                                echo $icon_class;
                            ?>">
                                <?php if ($notification['type'] === 'post_comment'): ?>
                                    <i class="fas fa-comment"></i>
                                <?php elseif ($notification['type'] === 'post_like'): ?>
                                    <i class="fas fa-heart"></i>
                                <?php elseif ($notification['type'] === 'post_approved'): ?>
                                    <i class="fas fa-check"></i>
                                <?php elseif ($notification['type'] === 'post_rejected'): ?>
                                    <i class="fas fa-times"></i>
                                <?php elseif ($notification['type'] === 'message'): ?>
                                    <i class="fas fa-envelope"></i>
                                <?php else: ?>
                                    <i class="fas fa-bell"></i>
                                <?php endif; ?>
                            </div>

                            <div class="notification-content">
                                <div class="notification-title"><?php echo htmlspecialchars($notification['title']); ?></div>
                                <div class="notification-message"><?php echo htmlspecialchars($notification['message']); ?></div>
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
                            </div>

                            <div class="notification-actions">
                                <?php if (!$notification['is_read']): ?>
                                <button onclick="markAsRead(<?php echo $notification['id']; ?>)" title="Đánh dấu đã đọc">
                                    <i class="fas fa-check"></i>
                                </button>
                                <?php endif; ?>
                                <button onclick="deleteNotification(<?php echo $notification['id']; ?>)" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
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
