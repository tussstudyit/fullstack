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
        body {
            margin: 0;
            overflow: hidden;
        }

        .chat-container {
            display: grid;
            grid-template-columns: 360px 1fr;
            height: calc(100vh - 70px);
            background: white;
            overflow: hidden;
            transition: grid-template-columns 0.3s ease;
        }

        .chat-container.info-open {
            grid-template-columns: 360px 1fr 360px;
        }

        .conversations-sidebar {
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        .sidebar-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--border-color);
            background: white;
            flex-shrink: 0;
        }

        .sidebar-header h2 {
            margin: 0 0 1rem 0;
            font-size: 1.5rem;
        }

        .search-conversation {
            width: 100%;
        }

        .conversations-list {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .conversations-list::-webkit-scrollbar {
            width: 6px;
        }

        .conversations-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .conversations-list::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }

        .conversation-item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            gap: 0.875rem;
            align-items: center;
        }

        .conversation-item:hover {
            background: #f0f9ff;
        }

        .conversation-item.active {
            background: #dbeafe;
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
            align-items: center;
            margin-bottom: 0.25rem;
        }

        .conversation-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: #050505;
        }

        .conversation-time {
            font-size: 0.75rem;
            color: #65676b;
        }

        .conversation-last-message {
            color: #65676b;
            font-size: 0.85rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .unread-badge {
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0 6px;
        }

        .chat-main {
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            background: white;
        }

        .chat-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            flex-shrink: 0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .chat-user-info {
            display: flex;
            align-items: center;
            gap: 0.875rem;
        }

        .chat-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .chat-user-name {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 2px;
        }

        .chat-user-status {
            font-size: 0.8rem;
            color: var(--success-color);
        }

        .chat-actions {
            display: flex;
            gap: 0.5rem;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            background: white;
            padding: 1.25rem;
        }

        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #d0d0d0;
            border-radius: 4px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #b0b0b0;
        }

        .message {
            display: flex;
            gap: 0.625rem;
            margin-bottom: 0.5rem;
            align-items: flex-end;
        }

        .message.sent {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .message-content {
            max-width: 60%;
            display: flex;
            flex-direction: column;
        }

        .message.sent .message-content {
            align-items: flex-end;
        }

        .message-bubble {
            background: #f0f0f0;
            padding: 0.5rem 0.875rem;
            border-radius: 18px;
            word-wrap: break-word;
            line-height: 1.4;
        }

        .message.sent .message-bubble {
            background: var(--primary-color);
            color: white;
        }

        .message-time {
            font-size: 0.7rem;
            color: #65676b;
            margin-top: 0.25rem;
            padding: 0 0.5rem;
        }

        .chat-input-area {
            padding: 0.875rem 1.5rem 1.25rem;
            border-top: 1px solid #e4e6eb;
            background: white;
            flex-shrink: 0;
        }

        .chat-input-form {
            display: flex;
            gap: 0.625rem;
            align-items: flex-end;
        }

        .input-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .input-action-btn {
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-size: 1.25rem;
        }

        .input-action-btn:hover {
            background: #eff6ff;
        }

        .input-action-btn input[type="file"] {
            display: none;
        }

        .chat-input-wrapper {
            flex: 1;
            position: relative;
            background: #f0f9ff;
            border-radius: 20px;
            padding: 0.625rem 2.75rem 0.625rem 1rem;
        }

        .chat-input {
            width: 100%;
            padding: 0;
            border: none;
            background: transparent;
            resize: none;
            max-height: 100px;
            font-family: inherit;
            font-size: 0.9375rem;
            line-height: 1.4;
            outline: none;
        }

        .emoji-btn {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.125rem;
            padding: 0.25rem;
            opacity: 0.6;
            transition: opacity 0.2s;
        }

        .emoji-btn:hover {
            opacity: 1;
        }

        .send-btn {
            background: var(--primary-color);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .send-btn:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
        }

        .send-btn:active {
            transform: scale(0.95);
        }

        .info-toggle-btn {
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .info-toggle-btn:hover {
            background: #eff6ff;
        }

        .info-toggle-btn.active {
            background: #dbeafe;
        }

        .chat-info-panel {
            border-left: 1px solid var(--border-color);
            display: none;
            flex-direction: column;
            height: 100%;
            overflow-y: auto;
            background: white;
        }

        .chat-info-panel.show {
            display: flex;
        }

        .info-panel-header {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            text-align: center;
        }

        .info-user-avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            object-fit: cover;
        }

        .info-user-name {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .info-user-status {
            font-size: 0.85rem;
            color: var(--success-color);
        }

        .info-section {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-section-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #65676b;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-option {
            padding: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.875rem;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.2s;
            text-decoration: none;
            color: inherit;
        }

        .info-option:hover {
            background: #f0f9ff;
        }

        .info-option-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #eff6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
        }

        .info-option-text {
            flex: 1;
            font-size: 0.9375rem;
        }

        .info-option.danger .info-option-icon {
            background: #fee;
            color: var(--danger-color);
        }

        .info-option.danger .info-option-text {
            color: var(--danger-color);
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
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.2;
        }

        @media (max-width: 768px) {
            .chat-container {
                grid-template-columns: 1fr;
            }

            .conversations-sidebar {
                display: none;
            }

            .conversations-sidebar.mobile-show {
                display: flex;
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 100;
                background: white;
            }

            .message-content {
                max-width: 75%;
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
                                <a href="../user/notifications.php">Xem tất cả thông báo</a>
                            </div>
                        </div>
                    </div>
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
                        <button class="info-toggle-btn" onclick="toggleInfoPanel()" id="infoToggleBtn">
                            <i class="fas fa-info-circle"></i>
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
                        <div class="input-actions">
                            <button type="button" class="input-action-btn" onclick="document.getElementById('imageInput').click()" title="Gửi ảnh">
                                <i class="fas fa-image"></i>
                                <input type="file" id="imageInput" accept="image/*" multiple onchange="handleImageUpload(event)">
                            </button>
                            <button type="button" class="input-action-btn" onclick="openCamera()" title="Chụp ảnh">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <div class="chat-input-wrapper">
                            <textarea 
                                class="chat-input" 
                                id="messageInput"
                                placeholder="Aa"
                                rows="1"
                                onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); sendMessage(event); }"
                            ></textarea>
                            <button type="button" class="emoji-btn">😊</button>
                        </div>
                        <button type="submit" class="send-btn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </main>

            <aside class="chat-info-panel" id="chatInfoPanel">
                <div class="info-panel-header">
                    <img src="<?php echo getPlaceholderImage(80, 80, '667eea', 'A'); ?>" alt="User" class="info-user-avatar-large">
                    <div class="info-user-name">Nguyễn Văn A</div>
                    <div class="info-user-status"><i class="fas fa-circle" style="font-size: 0.5rem;"></i> Đang hoạt động</div>
                </div>

                <div class="info-section">
                    <div class="info-section-title">Tùy chọn</div>
                    <a href="../user/profile.php?id=1" class="info-option">
                        <div class="info-option-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="info-option-text">Xem trang cá nhân</div>
                    </a>
                    <div class="info-option" onclick="searchInConversation()">
                        <div class="info-option-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="info-option-text">Tìm kiếm trong đoạn chat</div>
                    </div>
                    <div class="info-option" onclick="changeTheme()">
                        <div class="info-option-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div class="info-option-text">Đổi giao diện</div>
                    </div>
                </div>

                <div class="info-section">
                    <div class="info-section-title">File phương tiện</div>
                    <div class="info-option" onclick="viewMedia()">
                        <div class="info-option-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <div class="info-option-text">Ảnh & Video</div>
                    </div>
                    <div class="info-option" onclick="viewFiles()">
                        <div class="info-option-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="info-option-text">File</div>
                    </div>
                    <div class="info-option" onclick="viewLinks()">
                        <div class="info-option-icon">
                            <i class="fas fa-link"></i>
                        </div>
                        <div class="info-option-text">Liên kết</div>
                    </div>
                </div>

                <div class="info-section">
                    <div class="info-section-title">Quyền riêng tư & hỗ trợ</div>
                    <div class="info-option" onclick="muteConversation()">
                        <div class="info-option-icon">
                            <i class="fas fa-bell-slash"></i>
                        </div>
                        <div class="info-option-text">Tắt thông báo</div>
                    </div>
                    <div class="info-option" onclick="blockUser()">
                        <div class="info-option-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="info-option-text">Chặn</div>
                    </div>
                    <div class="info-option danger" onclick="deleteConversation()">
                        <div class="info-option-icon">
                            <i class="fas fa-trash"></i>
                        </div>
                        <div class="info-option-text">Xóa đoạn chat</div>
                    </div>
                </div>
            </aside>
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

        function toggleInfoPanel() {
            const container = document.querySelector('.chat-container');
            const panel = document.getElementById('chatInfoPanel');
            const btn = document.getElementById('infoToggleBtn');
            
            container.classList.toggle('info-open');
            panel.classList.toggle('show');
            btn.classList.toggle('active');
        }

        function searchInConversation() {
            alert('Tính năng tìm kiếm trong đoạn chat');
        }

        function changeTheme() {
            alert('Tính năng đổi giao diện');
        }

        function muteConversation() {
            alert('Đã tắt thông báo cho đoạn chat này');
        }

        function blockUser() {
            if (confirm('Bạn có chắc muốn chặn người dùng này?')) {
                alert('Đã chặn người dùng');
            }
        }

        function deleteConversation() {
            if (confirm('Bạn có chắc muốn xóa đoạn chat này?')) {
                alert('Đã xóa đoạn chat');
            }
        }

        function viewMedia() {
            alert('Xem ảnh & video trong đoạn chat');
        }

        function viewFiles() {
            alert('Xem file đã gửi trong đoạn chat');
        }

        function viewLinks() {
            alert('Xem các liên kết đã chia sẻ');
        }

        function handleImageUpload(event) {
            const files = event.target.files;
            if (files.length > 0) {
                const fileNames = Array.from(files).map(f => f.name).join(', ');
                alert(`Đã chọn ${files.length} ảnh: ${fileNames}\n\nTính năng upload ảnh sẽ được phát triển sau.`);
                // TODO: Implement image upload functionality
            }
        }

        function openCamera() {
            alert('Mở camera để chụp ảnh\n\nTính năng này yêu cầu quyền truy cập camera và sẽ được phát triển sau.');
            // TODO: Implement camera access
            // navigator.mediaDevices.getUserMedia({ video: true })
        }
    </script>
    <script src="../../assets/js/notifications.js"></script>
</body>
</html>
