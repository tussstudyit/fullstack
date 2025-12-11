<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../helpers.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
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

        .conversation-last-message,
        .conversation-preview {
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

        /* Tin nhắn không có avatar (giống Messenger) */
        .message.no-avatar {
            margin-bottom: 0.25rem;
        }

        .message-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .message-avatar-spacer {
            width: 28px;
            height: 28px;
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
            max-width: 100%;
            width: 100%;
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
            min-width: 0;
            position: relative;
            background: #f0f9ff;
            border-radius: 20px;
            padding: 0.625rem 2.75rem 0.625rem 1rem;
            max-width: 100%;
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
                <li style="position: relative;">
                    <a href="chat.php" class="nav-link active">Tin nhắn</a>
                    <?php 
                    if (isLoggedIn()) {
                        try {
                            $db = getDB();
                            // Đếm số cuộc hội thoại có tin nhắn chưa đọc
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
                    }
                    ?>
                </li>
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
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h2 style="margin: 0;">Tin nhắn</h2>
                        <span id="totalUnreadBadge" style="background: #ef4444; color: white; border-radius: 10px; padding: 2px 8px; font-size: 0.75rem; font-weight: 700; display: none;"></span>
                    </div>
                    <input 
                        type="text" 
                        class="form-control search-conversation" 
                        placeholder="Tìm kiếm cuộc hội thoại..."
                    >
                </div>

                <div class="conversations-list" id="conversationsList">
                    <!-- Conversations sẽ được load từ database -->
                </div>
            </aside>

            <main class="chat-main">
                <div class="chat-header" id="chatHeader" style="display: none;">
                    <div class="chat-user-info">
                        <img src="<?php echo getPlaceholderImage(45, 45, '667eea', '?'); ?>" alt="User" class="chat-user-avatar" id="chatUserAvatar">
                        <div>
                            <div class="chat-user-name" id="chatUserName">Đang tải...</div>
                            <div class="chat-user-status" id="chatUserStatus"><i class="fas fa-circle" style="font-size: 0.5rem;"></i> Đang hoạt động</div>
                        </div>
                    </div>
                    <div class="chat-actions">
                        <a href="../posts/detail.php?id=0" class="btn btn-outline btn-sm" id="postLink">
                            <i class="fas fa-home"></i> Xem tin
                        </a>
                        <button class="info-toggle-btn" onclick="toggleInfoPanel()" id="infoToggleBtn">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </div>
                </div>

                <div class="chat-messages" id="chatMessages">
                    <!-- Placeholder khi chưa chọn conversation -->
                    <div id="emptyState" style="display: flex; align-items: center; justify-content: center; height: 100%; flex-direction: column; color: #6b7280; text-align: center;">
                        <i class="fas fa-comments" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <p style="font-size: 1.1rem; margin-bottom: 0.5rem;">Chào mừng bạn đến với tin nhắn</p>
                        <p style="font-size: 0.9rem; color: #9ca3af;">Chọn một cuộc trò chuyện hoặc bắt đầu nhắn tin mới</p>
                    </div>
                </div>

                <div class="chat-input-area" id="chatInputArea" style="display: none; flex-direction: column;">
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
                    <img src="<?php echo getPlaceholderImage(80, 80, '667eea', '?'); ?>" alt="User" class="info-user-avatar-large" id="infoUserAvatar">
                    <div class="info-user-name" id="infoUserName">Đang tải...</div>
                    <div class="info-user-status" id="infoUserStatus"><i class="fas fa-circle" style="font-size: 0.5rem;"></i> Đang hoạt động</div>
                </div>

                <div class="info-section">
                    <div class="info-section-title">Tùy chọn</div>
                    <a href="../user/profile.php?id=0" class="info-option" id="profileLink">
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
        // ============ CURRENT USER INFO ============
        const currentUserAvatar = <?php 
            try {
                $db = getDB();
                $user_stmt = $db->prepare("SELECT avatar FROM users WHERE id = ?");
                $user_stmt->execute([$_SESSION["user_id"]]);
                $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($user_data["avatar"] ?? null);
            } catch (Exception $e) {
                echo 'null';
            }
        ?>;
        
        // ============ WEBSOCKET CONFIGURATION ============
        let ws = null;
        let currentConversationId = null;
        let reconnectAttempts = 0;
        const MAX_RECONNECT_ATTEMPTS = 5;
        const RECONNECT_DELAY = 3000;

        // Kết nối WebSocket
        function connectWebSocket() {
            try {
                ws = new WebSocket('ws://localhost:8080');
                
                ws.onopen = function() {
                    console.log('✅ WebSocket connected');
                    reconnectAttempts = 0;
                    
                    // Xác thực ngay khi kết nối
                    ws.send(JSON.stringify({
                        type: 'authenticate',
                        user_id: <?php echo $_SESSION['user_id'] ?? 0; ?>,
                        username: '<?php echo $_SESSION['username'] ?? 'Guest'; ?>'
                    }));
                    
                    updateConnectionStatus(true);
                };
                
                ws.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        console.log('📩 Received:', data);
                        
                        switch(data.type) {
                            case 'authenticated':
                                console.log('✅ Authenticated as user ' + data.user_id);
                                break;
                                
                            case 'message':
                                console.log('💬 Received message:', {
                                    conversation_id: data.conversation_id,
                                    current_conversation: currentConversationId,
                                    sender_id: data.sender_id,
                                    text: data.text,
                                    avatar: data.avatar,
                                    username: data.username
                                });
                                
                                // Hiển thị tin nhắn nếu thuộc cuộc trò chuyện hiện tại
                                if (data.conversation_id == currentConversationId) {
                                    addMessageToUI({
                                        id: data.message_id,
                                        sender_id: data.sender_id,
                                        text: data.text,
                                        timestamp: data.timestamp || new Date().toISOString(),
                                        is_read: 0,
                                        avatar: data.avatar,
                                        username: data.username
                                    });
                                    console.log('✅ Message added to UI');
                                } else {
                                    console.log('💡 Message for different conversation, updating preview only');
                                    // Tin nhắn từ conversation khác - kiểm tra và cập nhật badge
                                    console.log('🔍 Looking for conversation item with id:', data.conversation_id);
                                    const convItem = document.querySelector(`[data-conversation-id="${data.conversation_id}"]`);
                                    console.log('🔍 Found convItem:', !!convItem, convItem?.dataset?.conversationId);
                                    
                                    if (convItem) {
                                        const hadUnreadBefore = parseInt(convItem.dataset.unreadCount || 0) > 0;
                                        console.log('📊 Unread status:', {
                                            hadUnreadBefore,
                                            unreadCount: convItem.dataset.unreadCount,
                                            willIncrementNavbar: !hadUnreadBefore
                                        });
                                        
                                        // Cập nhật badge conversation và total
                                        updateConversationBadge(data.conversation_id, 1);
                                    } else {
                                        console.warn('⚠️ Conversation not found in list. It might be newly created or not loaded yet.');
                                    }
                                }
                                
                                // Luôn cập nhật conversation list preview
                                updateConversationPreview(data.conversation_id, data.text);
                                break;
                                
                            case 'typing':
                                if (data.conversation_id == currentConversationId) {
                                    showTypingIndicator(data.username);
                                }
                                break;
                                
                            case 'online':
                                updateUserOnlineStatus(data.user_id, true);
                                break;
                                
                            case 'offline':
                                updateUserOnlineStatus(data.user_id, false);
                                break;
                                
                            case 'error':
                                console.error('❌ Server error:', data.message);
                                break;
                        }
                    } catch (e) {
                        console.error('❌ Failed to parse message:', e);
                    }
                };
                
                ws.onerror = function(error) {
                    console.error('❌ WebSocket error:', error);
                    updateConnectionStatus(false);
                };
                
                ws.onclose = function() {
                    console.log('🔌 WebSocket disconnected');
                    updateConnectionStatus(false);
                    
                    // Tự động kết nối lại
                    if (reconnectAttempts < MAX_RECONNECT_ATTEMPTS) {
                        reconnectAttempts++;
                        console.log(`🔄 Reconnecting... (${reconnectAttempts}/${MAX_RECONNECT_ATTEMPTS})`);
                        setTimeout(connectWebSocket, RECONNECT_DELAY);
                    } else {
                        alert('Mất kết nối đến server. Vui lòng tải lại trang.');
                    }
                };
                
            } catch (e) {
                console.error('❌ Failed to connect:', e);
                updateConnectionStatus(false);
            }
        }

        function updateConnectionStatus(isConnected) {
            const statusIndicator = document.querySelector('.info-user-status');
            if (statusIndicator && isConnected) {
                statusIndicator.innerHTML = '<i class="fas fa-circle" style="font-size: 0.5rem; color: #10b981;"></i> Đang hoạt động';
            }
        }

        function updateUserOnlineStatus(userId, isOnline) {
            // Cập nhật trạng thái online/offline trong UI
            const status = isOnline ? 'online' : 'offline';
            console.log(`👤 User ${userId} is ${status}`);
        }

        function updateConversationPreview(conversationId, lastMessage) {
            // Cập nhật tin nhắn cuối trong danh sách hội thoại
            const preview = document.querySelector(`[data-conversation-id="${conversationId}"] .conversation-preview`);
            if (preview) {
                preview.textContent = lastMessage;
            }
        }

        function updateConversationBadge(conversationId, increment) {
            const convItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
            if (!convItem) return;
            
            let badge = convItem.querySelector('.unread-badge');
            
            if (increment > 0) {
                if (!badge) {
                    // Tạo badge mới
                    badge = document.createElement('div');
                    badge.className = 'unread-badge';
                    badge.textContent = '1';
                    convItem.appendChild(badge);
                } else {
                    // Cập nhật số lượng
                    let count = parseInt(badge.textContent) || 0;
                    count += increment;
                    badge.textContent = count > 99 ? '99+' : count;
                }
                
                // Cập nhật data attribute
                let currentCount = parseInt(convItem.dataset.unreadCount || 0);
                convItem.dataset.unreadCount = currentCount + increment;
                
                // Cập nhật tổng badge ở header
                updateTotalUnreadInSidebar();
            }
        }

        function updateTotalUnreadInSidebar() {
            const conversations = document.querySelectorAll('.conversation-item');
            let total = 0;
            conversations.forEach(conv => {
                total += parseInt(conv.dataset.unreadCount || 0);
            });
            
            const badge = document.getElementById('totalUnreadBadge');
            if (total > 0) {
                badge.textContent = `${total > 99 ? '99+' : total} tin`;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }

        function showTypingIndicator(username) {
            // Hiển thị "đang nhập..."
            console.log(`⌨️ ${username} is typing...`);
        }

        // Kết nối khi trang load
        window.addEventListener('load', function() {
            connectWebSocket();
        });

        // Đóng kết nối khi rời trang
        window.addEventListener('beforeunload', function() {
            if (ws && ws.readyState === WebSocket.OPEN) {
                ws.close();
            }
        });

        // ============ UI FUNCTIONS ============
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
            
            // Gửi qua WebSocket
            if (ws && ws.readyState === WebSocket.OPEN && currentConversationId) {
                console.log('📤 Sending message via WebSocket:', {
                    conversation_id: currentConversationId,
                    sender_id: <?php echo $_SESSION['user_id'] ?? 0; ?>,
                    text: message
                });
                
                ws.send(JSON.stringify({
                    type: 'message',
                    conversation_id: currentConversationId,
                    sender_id: <?php echo $_SESSION['user_id'] ?? 0; ?>,
                    text: message,
                    image: null
                }));
                
                // Hiển thị tin nhắn ngay lập tức (optimistic update)
                addMessageToUI({
                    sender_id: <?php echo $_SESSION['user_id'] ?? 0; ?>,
                    text: message,
                    timestamp: new Date().toISOString(),
                    is_read: 0,
                    avatar: currentUserAvatar
                }, true);
                
                input.value = '';
                input.style.height = 'auto';
            } else {
                console.error('❌ Cannot send message:', {
                    wsConnected: ws && ws.readyState === WebSocket.OPEN,
                    conversationId: currentConversationId
                });
                alert('Chưa kết nối đến server. Vui lòng tải lại trang.');
            }
        }

        function addMessageToUI(messageData, isSent = false) {
            const messagesContainer = document.getElementById('chatMessages');
            const isMyMessage = messageData.sender_id == <?php echo $_SESSION['user_id'] ?? 0; ?>;
            const messageClass = isMyMessage ? 'sent' : 'received';
            
            console.log('🖼️ Avatar data:', {
                sender_id: messageData.sender_id,
                avatar: messageData.avatar,
                isMyMessage: isMyMessage
            });
            
            // Xử lý avatar
            let avatarSrc = '<?php echo getPlaceholderImage(40, 40, "3b82f6", "U"); ?>';
            if (messageData.avatar) {
                avatarSrc = '../../uploads/avatars/' + messageData.avatar;
                console.log('✅ Using user avatar:', avatarSrc);
            } else if (isMyMessage) {
                avatarSrc = '<?php 
                    try {
                        $db = getDB();
                        $user_stmt = $db->prepare("SELECT avatar FROM users WHERE id = ?");
                        $user_stmt->execute([$_SESSION["user_id"]]);
                        $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);
                        echo (!empty($user_data["avatar"])) 
                            ? "../../uploads/avatars/" . htmlspecialchars($user_data["avatar"]) 
                            : getPlaceholderImage(40, 40, "3b82f6", strtoupper(substr($_SESSION["username"], 0, 1)));
                    } catch (Exception $e) {
                        echo getPlaceholderImage(40, 40, "3b82f6", "U");
                    }
                ?>';
                console.log('✅ Using my avatar from PHP');
            } else {
                console.log('⚠️ No avatar provided, using placeholder');
            }
            
            // Kiểm tra tin nhắn đã bị thu hồi
            const isRecalled = messageData.is_recalled || false;
            const messageId = messageData.id || 'temp_' + Date.now();
            
            let messageContent = '';
            if (isRecalled) {
                messageContent = `
                    <div class="message-bubble recalled">
                        <i class="fas fa-ban"></i> Tin nhắn đã được thu hồi
                    </div>
                `;
            } else {
                messageContent = `
                    <div class="message-bubble">${escapeHtml(messageData.text)}</div>
                `;
            }
            
            // Kiểm tra xem có cần hiện avatar không (kiểu Messenger)
            // Chỉ hiện avatar cho tin nhắn đầu tiên hoặc khi người gửi khác với tin trước
            const messages = messagesContainer.querySelectorAll('.message');
            const lastMessage = messages[messages.length - 1];
            let showAvatar = true;
            
            if (lastMessage) {
                const lastSenderId = lastMessage.querySelector('.message-content')?.dataset.senderId;
                if (lastSenderId == messageData.sender_id) {
                    showAvatar = false;
                }
            }
            
            const messageHTML = `
                <div class="message ${messageClass} ${!showAvatar ? 'no-avatar' : ''}" data-message-id="${messageId}">
                    ${showAvatar ? `<img src="${avatarSrc}" alt="Avatar" class="message-avatar">` : '<div class="message-avatar-spacer"></div>'}
                    <div class="message-content" data-sender-id="${messageData.sender_id}">
                        ${messageContent}
                        <div class="message-time">${formatTime(messageData.timestamp || messageData.created_at)}</div>
                    </div>
                </div>
            `;
            
            messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) return 'Vừa xong';
            if (diff < 3600000) return Math.floor(diff / 60000) + ' phút trước';
            if (diff < 86400000) return Math.floor(diff / 3600000) + ' giờ trước';
            
            return date.toLocaleString('vi-VN', { 
                hour: '2-digit', 
                minute: '2-digit',
                day: '2-digit',
                month: '2-digit'
            });
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
            if (!currentConversationId) {
                alert('Vui lòng chọn một cuộc trò chuyện trước');
                return;
            }

            const files = event.target.files;
            if (files.length === 0) return;

            // Upload từng ảnh
            for (let i = 0; i < files.length; i++) {
                uploadAndSendImage(files[i], currentConversationId);
            }

            // Reset file input
            event.target.value = '';
        }

        function uploadAndSendImage(file, conversationId) {
            // Kiểm tra kích thước file
            if (file.size > 5 * 1024 * 1024) {
                showNotification('Ảnh quá lớn (tối đa 5MB)', 'error');
                return;
            }

            // Kiểm tra loại file
            if (!file.type.startsWith('image/')) {
                showNotification('Vui lòng chọn file ảnh', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('image', file);
            formData.append('conversation_id', conversationId);

            // Show loading indicator
            showNotification('Đang tải ảnh lên...', 'info');

            fetch('../../api/upload-message-image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Gửi ảnh qua WebSocket
                    if (ws && ws.readyState === WebSocket.OPEN && currentConversationId) {
                        console.log('📤 Sending image via WebSocket:', {
                            conversation_id: currentConversationId,
                            image: data.filename
                        });

                        ws.send(JSON.stringify({
                            type: 'message',
                            conversation_id: currentConversationId,
                            sender_id: <?php echo $_SESSION['user_id'] ?? 0; ?>,
                            text: '',
                            image: data.filename
                        }));

                        // Hiển thị ảnh ngay lập tức (optimistic update)
                        addMessageImageToUI({
                            sender_id: <?php echo $_SESSION['user_id'] ?? 0; ?>,
                            image: '../../' + data.url,
                            timestamp: new Date().toISOString(),
                            is_read: 0,
                            avatar: currentUserAvatar
                        }, true);
                    }
                } else {
                    showNotification('Lỗi tải ảnh: ' + data.error, 'error');
                }
            })
            .catch(error => {
                console.error('Error uploading image:', error);
                showNotification('Lỗi tải ảnh', 'error');
            });
        }

        function addMessageImageToUI(messageData, isSent = false) {
            const messagesContainer = document.getElementById('chatMessages');
            const isMyMessage = messageData.sender_id == <?php echo $_SESSION['user_id'] ?? 0; ?>;
            const messageClass = isMyMessage ? 'sent' : 'received';

            // Xử lý avatar
            let avatarSrc = '<?php echo getPlaceholderImage(40, 40, "3b82f6", "U"); ?>';
            if (messageData.avatar) {
                avatarSrc = '../../uploads/avatars/' + messageData.avatar;
            }

            const messageId = messageData.id || 'temp_' + Date.now();

            // Kiểm tra xem có cần hiện avatar không
            const messages = messagesContainer.querySelectorAll('.message');
            const lastMessage = messages[messages.length - 1];
            let showAvatar = true;

            if (lastMessage) {
                const lastSenderId = lastMessage.querySelector('.message-content')?.dataset.senderId;
                if (lastSenderId == messageData.sender_id) {
                    showAvatar = false;
                }
            }

            const messageHTML = `
                <div class="message ${messageClass} ${!showAvatar ? 'no-avatar' : ''}" data-message-id="${messageId}">
                    ${showAvatar ? `<img src="${avatarSrc}" alt="Avatar" class="message-avatar">` : '<div class="message-avatar-spacer"></div>'}
                    <div class="message-content" data-sender-id="${messageData.sender_id}">
                        <div class="message-bubble" style="background: transparent; padding: 0;">
                            <img src="${messageData.image}" alt="Image" style="max-width: 280px; max-height: 400px; border-radius: 12px; cursor: pointer; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" onclick="openImageViewer('${messageData.image}')" loading="lazy">
                        </div>
                        <div class="message-time" style="margin-top: 4px;">${formatTime(messageData.timestamp)}</div>
                    </div>
                </div>
            `;

            messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function openImageViewer(imageSrc) {
            const viewer = document.createElement('div');
            viewer.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.95); display: flex; align-items: center; justify-content: center; z-index: 9999; cursor: pointer; animation: fadeIn 0.2s ease-in;';
            
            // Close button
            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '✕';
            closeBtn.style.cssText = 'position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.3); border: none; color: white; font-size: 28px; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; hover: rgba(255,255,255,0.5); transition: background 0.2s;';
            closeBtn.onclick = (e) => { e.stopPropagation(); viewer.remove(); };
            
            viewer.onclick = () => viewer.remove();

            const img = document.createElement('img');
            img.src = imageSrc;
            img.style.cssText = 'max-width: 90vw; max-height: 90vh; cursor: default; object-fit: contain; border-radius: 4px;';
            img.onclick = (e) => e.stopPropagation();

            viewer.appendChild(img);
            viewer.appendChild(closeBtn);
            document.body.appendChild(viewer);
            
            // Add animation
            const style = document.createElement('style');
            style.textContent = '@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }';
            document.head.appendChild(style);
        }

        function openCamera() {
            alert('Mở camera để chụp ảnh\n\nTính năng này yêu cầu quyền truy cập camera và sẽ được phát triển sau.');
            // TODO: Implement camera access
            // navigator.mediaDevices.getUserMedia({ video: true })
        }

        // ============ CONVERSATION MANAGEMENT ============
        
        // Lấy post_id và user_id từ URL để tạo hoặc mở conversation
        const urlParams = new URLSearchParams(window.location.search);
        const postId = urlParams.get('post_id');
        const otherUserId = urlParams.get('user_id');
        
        if (postId && otherUserId) {
            // Trường hợp: Từ trang detail, tạo conversation mới
            createOrGetConversation(postId, otherUserId);
            loadOtherUserInfo(otherUserId);
            
            const postLink = document.getElementById('postLink');
            if (postLink) {
                postLink.href = '../posts/detail.php?id=' + postId;
            }
        } else {
            // Trường hợp: Vào trang chat trực tiếp, load danh sách conversations
            loadConversationsList();
        }

        function loadConversationsList() {
            console.log('📋 Loading conversations list...');
            fetch('../../api/chat.php?action=getConversations')
                .then(response => response.json())
                .then(data => {
                    console.log('📦 Conversations:', data);
                    if (data.success && data.data && data.data.length > 0) {
                        renderConversationsList(data.data);
                    } else {
                        console.log('📭 No conversations yet');
                        document.getElementById('conversationsList').innerHTML = `
                            <div style="padding: 2rem; text-align: center; color: #6b7280;">
                                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                                <p>Chưa có cuộc trò chuyện nào</p>
                                <p style="font-size: 0.9rem;">Bắt đầu nhắn tin với chủ trọ từ trang bài đăng</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('❌ Error loading conversations:', error);
                });
        }

        function renderConversationsList(conversations) {
            const conversationsList = document.getElementById('conversationsList');
            conversationsList.innerHTML = conversations.map((conv, index) => `
                <div class="conversation-item ${index === 0 ? 'active' : ''}" 
                     data-conversation-id="${conv.id}"
                     data-other-user-id="${conv.other_user_id}"
                     data-other-user-name="${conv.other_user_name}"
                     data-other-user-avatar="${conv.other_user_avatar || ''}"
                     data-post-id="${conv.post_id}"
                     data-unread-count="${conv.unread_count || 0}"
                     onclick="selectConversationById(${conv.id})">
                    <img src="${conv.other_user_avatar ? '../../uploads/avatars/' + conv.other_user_avatar : '<?php echo getPlaceholderImage(50, 50, "667eea", "?"); ?>'}" 
                         alt="${conv.other_user_name}" 
                         class="conversation-avatar">
                    <div class="conversation-info">
                        <div class="conversation-header">
                            <span class="conversation-name">${conv.other_user_name}</span>
                            <span class="conversation-time">${formatConversationTime(conv.last_message_at)}</span>
                        </div>
                        <div class="conversation-preview">${conv.last_message || 'Chưa có tin nhắn'}</div>
                    </div>
                    ${conv.unread_count > 0 ? `<div class="unread-badge">${conv.unread_count}</div>` : ''}
                </div>
            `).join('');
            
            // Cập nhật tổng tin nhắn chưa đọc
            updateTotalUnreadBadge(conversations);
        }

        function updateTotalUnreadBadge(conversations) {
            const totalUnread = conversations.reduce((sum, conv) => sum + (conv.unread_count || 0), 0);
            const badge = document.getElementById('totalUnreadBadge');
            
            if (totalUnread > 0) {
                badge.textContent = `${totalUnread > 99 ? '99+' : totalUnread} tin`;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }

        function selectConversationById(conversationId) {
            const items = document.querySelectorAll('.conversation-item');
            items.forEach(item => {
                if (item.dataset.conversationId == conversationId) {
                    selectConversation({
                        id: conversationId,
                        other_user_id: item.dataset.otherUserId,
                        other_user_name: item.dataset.otherUserName,
                        other_user_avatar: item.dataset.otherUserAvatar,
                        post_id: item.dataset.postId
                    });
                    // Update active state
                    items.forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                }
            });
        }

        function selectConversation(conv) {
            currentConversationId = conv.id;
            
            // Ẩn empty state và hiển thị header & input
            const emptyState = document.getElementById('emptyState');
            const chatHeader = document.getElementById('chatHeader');
            const chatInputArea = document.getElementById('chatInputArea');
            const chatMessages = document.getElementById('chatMessages');
            
            if (emptyState) emptyState.style.display = 'none';
            if (chatHeader) chatHeader.style.display = 'flex';
            if (chatInputArea) chatInputArea.style.display = 'flex';
            
            // Xóa unread badge khi mở conversation
            const conversationItem = document.querySelector(`[data-conversation-id="${conv.id}"]`);
            let hadUnreadMessages = false;
            if (conversationItem) {
                const badge = conversationItem.querySelector('.unread-badge');
                if (badge) {
                    hadUnreadMessages = true;
                    const unreadCount = parseInt(badge.textContent) || 0;
                    badge.remove();
                    // Reset data-unread-count
                    conversationItem.dataset.unreadCount = 0;
                    // Cập nhật tổng tin nhắn chưa đọc trong sidebar header
                    updateTotalUnreadInSidebar();
                }
            }
            
            // Đánh dấu tin nhắn là đã đọc
            markMessagesAsRead(conv.id);
            
            // Update chat header
            const chatUserName = document.getElementById('chatUserName');
            const chatUserAvatar = document.getElementById('chatUserAvatar');
            const infoUserName = document.getElementById('infoUserName');
            const infoUserAvatar = document.getElementById('infoUserAvatar');
            const profileLink = document.getElementById('profileLink');
            const postLink = document.getElementById('postLink');
            
            if (chatUserName) chatUserName.textContent = conv.other_user_name || 'Người dùng';
            if (infoUserName) infoUserName.textContent = conv.other_user_name || 'Người dùng';
            if (profileLink) profileLink.href = '../user/profile.php?id=' + conv.other_user_id;
            if (postLink) postLink.href = '../posts/detail.php?id=' + conv.post_id;
            
            // Update avatars
            const avatarUrl = conv.other_user_avatar 
                ? '../../uploads/avatars/' + conv.other_user_avatar 
                : '<?php echo getPlaceholderImage(45, 45, "667eea", "?"); ?>';
            if (chatUserAvatar) chatUserAvatar.src = avatarUrl;
            if (infoUserAvatar) infoUserAvatar.src = avatarUrl.replace('45', '80');
            
            // Load messages for this conversation
            loadMessages(currentConversationId);
            
            console.log('✅ Selected conversation:', currentConversationId, 'Avatar:', avatarUrl);
        }

        function formatConversationTime(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) return 'Vừa xong';
            if (diff < 3600000) return Math.floor(diff / 60000) + ' phút';
            if (diff < 86400000) return Math.floor(diff / 3600000) + ' giờ';
            if (diff < 604800000) return Math.floor(diff / 86400000) + ' ngày';
            
            return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
        }

        function loadOtherUserInfo(userId) {
            fetch(`../../api/user.php?action=getUserInfo&user_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.user) {
                        updateChatHeader(data.user);
                    }
                })
                .catch(error => {
                    console.error('❌ Error loading user info:', error);
                });
        }

        function updateChatHeader(user) {
            // Update chat header
            const chatUserName = document.getElementById('chatUserName');
            const chatUserAvatar = document.getElementById('chatUserAvatar');
            const infoUserName = document.getElementById('infoUserName');
            const infoUserAvatar = document.getElementById('infoUserAvatar');
            const profileLink = document.getElementById('profileLink');
            
            if (chatUserName) chatUserName.textContent = user.username || 'Người dùng';
            if (infoUserName) infoUserName.textContent = user.username || 'Người dùng';
            if (profileLink) profileLink.href = '../user/profile.php?id=' + user.id;
            
            // Update avatars if available
            if (user.avatar) {
                if (chatUserAvatar) chatUserAvatar.src = '../../uploads/avatars/' + user.avatar;
                if (infoUserAvatar) infoUserAvatar.src = '../../uploads/avatars/' + user.avatar;
            }
            
            console.log('✅ Updated chat header for user:', user.username);
        }

        function createOrGetConversation(postId, userId) {
            console.log('🔄 Creating/getting conversation:', {postId, userId});
            
            fetch('../../api/chat.php?action=createOrGetConversation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    post_id: parseInt(postId),
                    other_user_id: parseInt(userId)
                })
            })
            .then(response => {
                console.log('📡 Response status:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('📄 Response text:', text);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        currentConversationId = data.conversation_id;
                        loadMessages(currentConversationId);
                        console.log('✅ Conversation loaded:', currentConversationId);
                    } else {
                        console.error('❌ Failed to create conversation:', data.message);
                    }
                } catch (e) {
                    console.error('❌ JSON parse error:', e);
                    console.error('Raw response:', text);
                }
            })
            .catch(error => {
                console.error('❌ Error creating conversation:', error);
            });
        }

        function markMessagesAsRead(conversationId) {
            fetch('../../api/chat.php?action=markAsRead', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    conversation_id: conversationId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('✅ Messages marked as read for conversation:', conversationId);
                }
            })
            .catch(error => {
                console.error('❌ Error marking messages as read:', error);
            });
        }

        function loadMessages(conversationId) {
            console.log('🔄 Loading messages for conversation:', conversationId);
            fetch(`../../api/chat.php?action=getMessages&conversation_id=${conversationId}`)
                .then(response => response.json())
                .then(data => {
                    console.log('📦 API Response:', data);
                    if (data.success && data.data) {
                        const messagesContainer = document.getElementById('chatMessages');
                        messagesContainer.innerHTML = ''; // Xóa tin nhắn cũ
                        
                        // API trả về data.data chứ không phải data.messages
                        data.data.forEach(msg => {
                            console.log('💬 Adding message:', msg);
                            console.log('📸 Image field:', msg.image, 'Type:', typeof msg.image);
                            
                            // Nếu có ảnh, hiển thị ảnh
                            if (msg.image && msg.image.trim() !== '') {
                                console.log('🖼️ Displaying image message');
                                addMessageImageToUI({
                                    id: msg.id,
                                    sender_id: msg.sender_id,
                                    image: '../../uploads/messages/' + msg.image,
                                    timestamp: msg.created_at,
                                    is_read: msg.is_read,
                                    avatar: msg.avatar,
                                    username: msg.username
                                });
                            } else {
                                // Hiển thị text message
                                console.log('💬 Displaying text message');
                                addMessageToUI({
                                    id: msg.id,
                                    sender_id: msg.sender_id,
                                    text: msg.message,
                                    timestamp: msg.created_at,
                                    is_read: msg.is_read,
                                    avatar: msg.avatar,
                                    username: msg.username,
                                    is_recalled: msg.is_recalled || false
                                });
                            }
                        });
                        
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        console.log(`✅ Loaded ${data.data.length} messages`);
                    } else if (data.success && (!data.data || data.data.length === 0)) {
                        console.log('📭 No messages yet in this conversation');
                    } else {
                        console.error('❌ API returned success=false:', data);
                    }
                })
                .catch(error => {
                    console.error('❌ Error loading messages:', error);
                });
        }
    
    </script>
    <script src="../../assets/js/notifications.js"></script>
</body>
</html>
