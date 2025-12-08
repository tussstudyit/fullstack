<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../helpers.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin nhắn - NhaTot</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .chat-container {
            display: grid;
            grid-template-columns: 350px 1fr;
            height: calc(100vh - 80px);
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            margin: 2rem auto;
            max-width: 1400px;
        }

        .conversations-sidebar {
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--light-color);
        }

        .sidebar-header h2 {
            margin-bottom: 1rem;
        }

        .search-conversation {
            width: 100%;
        }

        .conversations-list {
            flex: 1;
            overflow-y: auto;
        }

        .conversation-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            gap: 1rem;
        }

        .conversation-item:hover,
        .conversation-item.active {
            background: var(--light-color);
        }

        .conversation-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .conversation-info {
            flex: 1;
            min-width: 0;
        }

        .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.25rem;
        }

        .conversation-name {
            font-weight: 600;
            font-size: 1rem;
        }

        .conversation-time {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .conversation-last-message {
            color: var(--text-secondary);
            font-size: 0.875rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .unread-badge {
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .chat-main {
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--light-color);
        }

        .chat-user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .chat-user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }

        .chat-user-name {
            font-weight: 600;
            font-size: 1.125rem;
        }

        .chat-user-status {
            font-size: 0.875rem;
            color: var(--success-color);
        }

        .chat-actions {
            display: flex;
            gap: 0.5rem;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
            background: #f9fafb;
        }

        .message {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .message.sent {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .message-content {
            max-width: 60%;
        }

        .message-bubble {
            background: white;
            padding: 1rem 1.25rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }

        .message.sent .message-bubble {
            background: var(--primary-color);
            color: white;
        }

        .message-time {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 0.5rem;
        }

        .message.sent .message-time {
            text-align: right;
        }

        .chat-input-area {
            padding: 1.5rem;
            border-top: 1px solid var(--border-color);
            background: white;
        }

        .chat-input-form {
            display: flex;
            gap: 1rem;
            align-items: flex-end;
        }

        .chat-input-wrapper {
            flex: 1;
            position: relative;
        }

        .chat-input {
            width: 100%;
            padding: 1rem 3rem 1rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            resize: none;
            max-height: 120px;
        }

        .emoji-btn {
            position: absolute;
            right: 1rem;
            bottom: 1rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.25rem;
        }

        .empty-chat {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--text-secondary);
        }

        .empty-chat i {
            font-size: 5rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        @media (max-width: 768px) {
            .chat-container {
                grid-template-columns: 1fr;
                height: calc(100vh - 60px);
                margin: 1rem;
            }

            .conversations-sidebar {
                display: none;
            }

            .conversations-sidebar.mobile-show {
                display: flex;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 100;
                background: white;
            }

            .message-content {
                max-width: 80%;
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
                <?php if (isLoggedIn() && $_SESSION['role'] === 'landlord'): ?>
                <li><a href="../posts/create.php" class="nav-link">Đăng tin</a></li>
                <?php endif; ?>
                <?php if (isLoggedIn() && $_SESSION['role'] === 'tenant'): ?>
                <li><a href="../user/favorites.php" class="nav-link">Yêu thích</a></li>
                <?php endif; ?>
                <li><a href="chat.php" class="nav-link active">Tin nhắn</a></li>
            </ul>

            <div class="nav-actions">
                <?php if (isLoggedIn()): ?>
                    <a href="../user/notifications.php" style="position: relative; display: inline-flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 1.5rem; margin-right: 1rem;" title="Thông báo">
                        <i class="fas fa-bell"></i>
                        <?php 
                        require_once '../../Models/Notification.php';
                        $notifModel = new Notification();
                        $unread = $notifModel->getUnreadCount($_SESSION['user_id']);
                        if ($unread > 0): 
                        ?>
                        <span style="position: absolute; top: -8px; right: -8px; background: #ef4444; color: white; border-radius: 50%; min-width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 700; padding: 2px;">
                            <?php echo $unread > 99 ? '99+' : $unread; ?>
                        </span>
                        <?php endif; ?>
                    </a>
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
                            <a href="../user/profile.php" class="dropdown-item">
                                <i class="fas fa-user-circle"></i> Hồ sơ
                            </a>
                            <a href="../../Controllers/AuthController.php?action=logout" class="dropdown-item logout">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="../auth/login.php" class="btn btn-outline btn-sm">Đăng nhập</a>
                    <a href="../auth/register.php" class="btn btn-register btn-sm">Đăng ký</a>
                <?php endif; ?>
            </div>

            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <div class="container">
        <div class="chat-container">
            <aside class="conversations-sidebar">
                <div class="sidebar-header">
                    <h2>Tin nhắn</h2>
                    <input 
                        type="text" 
                        class="form-control search-conversation" 
                        placeholder="Tìm kiếm cuộc hội thoại..."
                    >
                </div>

                <div class="conversations-list">
                    <div class="conversation-item active">
                        <img src="<?php echo getPlaceholderImage(50, 50, '667eea', 'A'); ?>" alt="User" class="conversation-avatar">
                        <div class="conversation-info">
                            <div class="conversation-header">
                                <span class="conversation-name">Nguyễn Văn A</span>
                                <span class="conversation-time">10 phút trước</span>
                            </div>
                            <div class="conversation-last-message">Phòng còn trống không bạn?</div>
                        </div>
                        <div class="unread-badge">3</div>
                    </div>

                    <div class="conversation-item">
                        <img src="<?php echo getPlaceholderImage(50, 50, '764ba2', 'B'); ?>" alt="User" class="conversation-avatar">
                        <div class="conversation-info">
                            <div class="conversation-header">
                                <span class="conversation-name">Trần Thị B</span>
                                <span class="conversation-time">2 giờ trước</span>
                            </div>
                            <div class="conversation-last-message">Cảm ơn bạn nhé!</div>
                        </div>
                    </div>

                    <div class="conversation-item">
                        <img src="<?php echo getPlaceholderImage(50, 50, '3b82f6', 'C'); ?>" alt="User" class="conversation-avatar">
                        <div class="conversation-info">
                            <div class="conversation-header">
                                <span class="conversation-name">Lê Văn C</span>
                                <span class="conversation-time">1 ngày trước</span>
                            </div>
                            <div class="conversation-last-message">Cho mình xem phòng được không?</div>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="chat-main">
                <div class="chat-header">
                    <div class="chat-user-info">
                        <img src="<?php echo getPlaceholderImage(45, 45, '667eea', 'A'); ?>" alt="User" class="chat-user-avatar">
                        <div>
                            <div class="chat-user-name">Nguyễn Văn A</div>
                            <div class="chat-user-status"><i class="fas fa-circle" style="font-size: 0.5rem;"></i> Đang hoạt động</div>
                        </div>
                    </div>
                    <div class="chat-actions">
                        <a href="../posts/detail.php?id=1" class="btn btn-outline btn-sm">
                            <i class="fas fa-home"></i> Xem tin
                        </a>
                        <button class="btn btn-outline btn-sm">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                </div>

                <div class="chat-messages" id="chatMessages">
                    <div class="message received">
                        <img src="<?php echo getPlaceholderImage(40, 40, '667eea', 'A'); ?>" alt="User" class="message-avatar">
                        <div class="message-content">
                            <div class="message-bubble">
                                Xin chào! Mình thấy bài đăng phòng trọ của bạn. Phòng còn trống không?
                            </div>
                            <div class="message-time">10:30 AM</div>
                        </div>
                    </div>

                    <div class="message sent">
                        <img src="<?php echo getPlaceholderImage(40, 40, '3b82f6', 'Me'); ?>" alt="Me" class="message-avatar">
                        <div class="message-content">
                            <div class="message-bubble">
                                Chào bạn! Phòng vẫn còn trống ạ. Bạn muốn xem phòng khi nào?
                            </div>
                            <div class="message-time">10:32 AM</div>
                        </div>
                    </div>

                    <div class="message received">
                        <img src="<?php echo getPlaceholderImage(40, 40, '667eea', 'A'); ?>" alt="User" class="message-avatar">
                        <div class="message-content">
                            <div class="message-bubble">
                                Chiều nay mình có thể đến xem được không bạn?
                            </div>
                            <div class="message-time">10:35 AM</div>
                        </div>
                    </div>

                    <div class="message sent">
                        <img src="<?php echo getPlaceholderImage(40, 40, '3b82f6', 'Me'); ?>" alt="Me" class="message-avatar">
                        <div class="message-content">
                            <div class="message-bubble">
                                Được ạ. Bạn có thể đến lúc 3 giờ chiều nhé!
                            </div>
                            <div class="message-time">10:36 AM</div>
                        </div>
                    </div>
                </div>

                <div class="chat-input-area">
                    <form class="chat-input-form" onsubmit="sendMessage(event)">
                        <div class="chat-input-wrapper">
                            <textarea 
                                class="chat-input" 
                                id="messageInput"
                                placeholder="Nhập tin nhắn..."
                                rows="1"
                                onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); sendMessage(event); }"
                            ></textarea>
                            <button type="button" class="emoji-btn">😊</button>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Gửi
                        </button>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
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

        function sendMessage(event) {
            event.preventDefault();
            
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            
            if (!message) return;
            
            const messagesContainer = document.getElementById('chatMessages');
            const messageHTML = `
                <div class="message sent">
                    <img src="<?php echo getPlaceholderImage(40, 40, '3b82f6', 'Me'); ?>" alt="Me" class="message-avatar">
                    <div class="message-content">
                        <div class="message-bubble">\${message}</div>
                        <div class="message-time">Vừa xong</div>
                    </div>
                </div>
            `;
            
            messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            input.value = '';
            input.style.height = 'auto';
        }

        document.getElementById('messageInput').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    </script>
</body>
</html>
