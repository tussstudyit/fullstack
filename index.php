<?php
// Include configuration
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Models/PostImage.php';
require_once __DIR__ . '/helpers.php';

// Initialize variables with default values
$featured_posts = [];
$categories = [
    ['category' => 1, 'count' => 156],
    ['category' => 2, 'count' => 89],
    ['category' => 3, 'count' => 45],
    ['category' => 4, 'count' => 112]
];
$total_posts = 1200;
$total_users = 5000;
$total_landlords = 800;
$satisfied_rate = 98;
$db_error = false;

try {
    // Get database connection using config
    $conn = getDB();
    $postImageModel = new PostImage();
    
    // Fetch featured posts
    $featured_stmt = $conn->prepare("SELECT id, title, slug, address, district, city, price, area, room_type, room_status, max_people FROM posts WHERE status = 'approved' LIMIT 3");
    $featured_stmt->execute();
    $featured_posts = $featured_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Thêm ảnh chính cho mỗi bài đăng
    foreach ($featured_posts as &$post) {
        $post['image'] = $postImageModel->getPrimaryImage($post['id']);
    }
    unset($post);
    
    // Fetch category counts
    $categories_stmt = $conn->prepare("SELECT category, COUNT(*) as count FROM posts WHERE status = 'approved' GROUP BY category");
    $categories_stmt->execute();
    $categories_data = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($categories_data) {
        $categories = $categories_data;
    }
    
    // Fetch statistics
    $total_posts_stmt = $conn->prepare("SELECT COUNT(*) as count FROM posts WHERE status = 'approved'");
    $total_posts_stmt->execute();
    $result = $total_posts_stmt->fetch(PDO::FETCH_ASSOC);
    if ($result && isset($result['count'])) {
        $total_posts = $result['count'];
    }
    
    $total_users_stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE role='student'");
    $total_users_stmt->execute();
    $result = $total_users_stmt->fetch(PDO::FETCH_ASSOC);
    if ($result && isset($result['count'])) {
        $total_users = $result['count'];
    }
    
    $total_landlords_stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE role='landlord'");
    $total_landlords_stmt->execute();
    $result = $total_landlords_stmt->fetch(PDO::FETCH_ASSOC);
    if ($result && isset($result['count'])) {
        $total_landlords = $result['count'];
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $db_error = true;
    // Continue with default values
}

// Get user's favorites if logged in
$user_favorites = [];
if (isLoggedIn()) {
    try {
        $fav_stmt = $conn->prepare("SELECT post_id FROM favorites WHERE user_id = ?");
        $fav_stmt->execute([$_SESSION['user_id']]);
        $favs = $fav_stmt->fetchAll(PDO::FETCH_ASSOC);
        $user_favorites = array_column($favs, 'post_id');
    } catch (PDOException $e) {
        error_log("Error fetching favorites: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - NhaTot</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .carousel-section {
            width: 100%;
            height: 100vh;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
        }

        .carousel-container {
            position: relative;
            width: 90%;
            height: 90%;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
        }

        .carousel-slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .carousel-slide.active {
            opacity: 1;
        }

        .carousel-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .carousel-slide::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.4) 50%, transparent 100%);
            z-index: 1;
        }

        .carousel-slide-content {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            text-align: center;
            width: 90%;
            z-index: 2;
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .carousel-slide-content h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .carousel-slide-content p {
            font-size: 1.125rem;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }

        .carousel-controls {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            display: flex;
            gap: 1rem;
        }

        .carousel-btn {
            background: rgba(255, 255, 255, 0.5);
            border: none;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .carousel-btn.active {
            background: white;
            width: 30px;
            border-radius: 6px;
        }

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.3);
            color: white;
            border: none;
            padding: 1rem 1.5rem;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 5;
            border-radius: 0.5rem;
        }

        .carousel-nav:hover {
            background: rgba(0,0,0,0.6);
        }

        .carousel-prev {
            left: 1rem;
        }

        .carousel-next {
            right: 1rem;
        }
        
        .search-box {
            background: white;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);
            max-width: 700px;
            margin: 0 auto;
            width: 90%;
        }

        .search-form {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
        }

        .search-input-group {
            position: relative;
            flex: 1;
            min-width: 180px;
        }

        .search-input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.95rem;
        }

        .search-input-group input,
        .search-input-group select {
            width: 100%;
            padding: 0.875rem 0.875rem 0.875rem 2.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
            color: #1f2937;
        }

        .search-input-group input:focus,
        .search-input-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: #f0f9ff;
        }

        .search-input-group input::placeholder {
            color: #9ca3af;
        }

        .search-btn {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            color: white;
            border: none;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            height: 42px;
        }

        .search-btn:hover {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(59, 130, 246, 0.3);
        }

        .search-btn:active {
            transform: translateY(0);
        }

        .categories-section {
            padding: 4rem 0;
            background: var(--light-color);
        }

        .section-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 3rem;
            color: var(--text-primary);
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
        }

        .category-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-lg);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .category-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .category-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .category-name {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .category-count {
            color: var(--text-secondary);
        }

        .featured-posts-section {
            padding: 4rem 0;
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
        }

        .post-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .post-card:hover .post-image img {
            transform: scale(1.1);
        }

        .post-badge {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.875rem;
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
            transition: all 0.3s ease;
            border: none;
            font-size: 1.25rem;
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
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .post-location {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .post-features {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
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
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stats-section {
            padding: 4rem 0;
            background: var(--light-color);
        }

        .stats-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .stats-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 3rem;
            color: var(--text-primary);
        }

        .amenities-box {
            background: white;
            padding: 3rem 2rem;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
            max-width: 100%;
            overflow-x: auto;
        }

        .amenity-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            flex: 1;
            min-width: 100px;
        }

        .amenity-item:hover {
            background: #f0f9ff;
            transform: translateY(-5px);
        }

        .amenity-icon {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .amenity-name {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-primary);
            text-align: center;
            white-space: nowrap;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-item {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .stat-item:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-5px);
            border-color: #e0f2fe;
        }

        .stat-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.75rem;
            color: white;
        }

        .stat-item:nth-child(1) .stat-icon {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .stat-item:nth-child(2) .stat-icon {
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
        }

        .stat-item:nth-child(3) .stat-icon {
            background: linear-gradient(135deg, #93c5fd, #60a5fa);
        }

        .stat-item:nth-child(4) .stat-icon {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .stat-label {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .stats-cta {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            color: white;
            padding: 5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(96, 165, 250, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 3rem;
        }

        .cta-left {
            flex: 1;
            max-width: 50%;
        }

        .cta-brand {
            font-family: 'Dancing Script', cursive;
            font-size: 3.5rem;
            font-weight: 700;
            margin: 0 0 1rem 0;
            background: linear-gradient(135deg, #ffffff 0%, #e0f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .cta-slogan {
            font-size: 1.25rem;
            line-height: 1.6;
            opacity: 0.95;
            margin: 0;
            font-style: italic;
            font-weight: 300;
        }

        .cta-right {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(10px);
            min-width: 300px;
        }

        .cta-right h4 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .cta-right p {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }

        .cta-explore-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: white;
            color: var(--primary-color);
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.05rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .cta-explore-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .footer {
            background: var(--dark-color);
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2rem;
        }

        .footer-about h3 {
            margin-bottom: 1rem;
        }

        .footer-links h4 {
            margin-bottom: 1rem;
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
        }

        @media (max-width: 768px) {
            .search-form {
                grid-template-columns: 1fr;
            }

            .category-grid,
            .stats-grid,
            .footer-grid {
                grid-template-columns: 1fr;
            }

            .amenities-box {
                flex-wrap: wrap;
                padding: 2rem 1rem;
                gap: 1rem;
            }

            .amenity-item {
                flex: 1 1 calc(25% - 1rem);
                min-width: 80px;
            }

            .amenity-icon {
                width: 48px;
                height: 48px;
                font-size: 1.25rem;
            }

            .amenity-name {
                font-size: 0.85rem;
            }

            .stats-cta {
                flex-direction: column;
                gap: 2rem;
                padding: 2rem;
            }

            .cta-left {
                max-width: 100%;
                text-align: center;
            }

            .cta-brand {
                font-size: 2.5rem;
            }

            .cta-slogan {
                font-size: 1.05rem;
            }

            .cta-right {
                min-width: 100%;
            }

            .hero-content h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="index.php" class="logo">
                <div class="logo-icon-box">
                    <i class="fas fa-home"></i>
                </div>
                <div class="logo-text">
                    <h1>NhaTot</h1>
                    <p>Nơi bạn thuộc về</p>
                </div>
            </a>

            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link active">Trang chủ</a></li>
                <li><a href="Views/posts/list.php" class="nav-link">Danh sách trọ</a></li>
                <?php if (isLoggedIn() && $_SESSION['role'] === 'landlord'): ?>
                <li><a href="Views/posts/create.php" class="nav-link">Đăng tin</a></li>
                <?php endif; ?>
                <?php if (isLoggedIn() && $_SESSION['role'] === 'tenant'): ?>
                <li><a href="Views/user/favorites.php" class="nav-link">Yêu thích</a></li>
                <?php endif; ?>
                <?php if (isLoggedIn()): ?>
                <li style="position: relative;">
                    <a href="Views/chat/chat.php" class="nav-link">Tin nhắn</a>
                    <?php 
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
                            require_once __DIR__ . '/Models/Notification.php';
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
                                <a href="Views/user/notifications.php">Xem tất cả thông báo</a>
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
                                    ? 'uploads/avatars/' . htmlspecialchars($user_data['avatar']) 
                                    : 'https://via.placeholder.com/40/3b82f6/ffffff?text=' . strtoupper(substr($_SESSION['username'], 0, 1));
                            } catch (Exception $e) {
                                $avatar_src = 'https://via.placeholder.com/40/3b82f6/ffffff?text=' . strtoupper(substr($_SESSION['username'], 0, 1));
                            }
                            ?>
                            <img src="<?php echo $avatar_src; ?>" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #3b82f6; cursor: pointer;">
                        </button>
                        <div class="user-dropdown-menu" id="userDropdownMenu" style="display: none;">
                            <a href="Views/user/profile.php" class="dropdown-item">
                                <i class="fas fa-user-circle"></i> Hồ sơ
                            </a>
                            <a href="Controllers/AuthController.php?action=logout" class="dropdown-item logout">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="Views/auth/login.php" class="btn btn-outline btn-sm">Đăng nhập</a>
                    <a href="Views/auth/register.php" class="btn btn-register btn-sm">Đăng ký</a>
                <?php endif; ?>
            </div>

            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <section class="carousel-section">
        <div class="carousel-container">
            <div class="carousel-slide active">
                <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1200&h=800&fit=crop" alt="Phòng trọ hiện đại">
                <div class="carousel-slide-content">
                    <h2>Tìm Phòng Trọ Dễ Dàng</h2>
                    <p>Hàng nghìn phòng trọ chất lượng cao, giá cả phải chăng</p>
                </div>
            </div>
            <div class="carousel-slide">
                <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1200&h=800&fit=crop" alt="Căn hộ mini tiện nghi">
                <div class="carousel-slide-content">
                    <h2>Căn Hộ Mini Tiện Nghi</h2>
                    <p>Đầy đủ nội thất, sẵn sàng ở ngay</p>
                </div>
            </div>
            <div class="carousel-slide">
                <img src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=1200&h=800&fit=crop" alt="Môi trường sống tốt">
                <div class="carousel-slide-content">
                    <h2>Môi Trường Sống Tốt</h2>
                    <p>An toàn, yên tĩnh, gần các cơ sở giáo dục</p>
                </div>
            </div>
            <div class="carousel-slide">
                <img src="https://images.unsplash.com/photo-1484154218962-a197022b5858?w=1200&h=800&fit=crop" alt="Nội thất đẹp">
                <div class="carousel-slide-content">
                    <h2>Nội Thất Hiện Đại</h2>
                    <p>Phòng trọ được trang bị đầy đủ tiện nghi</p>
                </div>
            </div>
            <div class="carousel-slide">
                <img src="https://images.unsplash.com/photo-1536376072261-38c75010e6c9?w=1200&h=800&fit=crop" alt="Cộng đồng sinh viên">
                <div class="carousel-slide-content">
                    <h2>Cộng Đồng Sinh Viên</h2>
                    <p>Giao tiếp, chia sẻ kinh nghiệm với đồng trang lứa</p>
                </div>
            </div>
            
            <button class="carousel-nav carousel-prev" onclick="changeSlide(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="carousel-nav carousel-next" onclick="changeSlide(1)">
                <i class="fas fa-chevron-right"></i>
            </button>

            <div class="carousel-controls">
                <button class="carousel-btn active" onclick="currentSlide(0)"></button>
                <button class="carousel-btn" onclick="currentSlide(1)"></button>
                <button class="carousel-btn" onclick="currentSlide(2)"></button>
                <button class="carousel-btn" onclick="currentSlide(3)"></button>
                <button class="carousel-btn" onclick="currentSlide(4)"></button>
            </div>
        </div>
    </section>

    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>Tìm Phòng Trọ Sinh Viên Dễ Dàng</h1>
                <p>Hàng nghìn phòng trọ giá rẻ, gần trường, an toàn cho sinh viên</p>

                <div class="search-box">
                    <form class="search-form" action="Views/posts/list.php" method="GET">
                        <div class="search-input-group">
                            <i class="fas fa-search"></i>
                            <input 
                                type="text" 
                                class="form-control" 
                                name="search" 
                                placeholder="Tìm theo tên, địa chỉ..."
                            >
                        </div>

                        <div class="search-input-group">
                            <i class="fas fa-map-marker-alt"></i>
                            <select class="form-control" name="district">
                                <option value="">Chọn quận/huyện</option>
                                <option value="Quận Hải Châu">Quận Hải Châu</option>
                                <option value="Quận Thanh Khê">Quận Thanh Khê</option>
                                <option value="Quận Cẩm Lệ">Quận Cẩm Lệ</option>
                                <option value="Quận Ngũ Hành Sơn">Quận Ngũ Hành Sơn</option>
                                <option value="Quận Sơn Trà">Quận Sơn Trà</option>
                                <option value="Quận Liên Chiểu">Quận Liên Chiểu</option>
                                <option value="Huyện Hòa Vang">Huyện Hòa Vang</option>
                            </select>
                        </div>

                        <div class="search-input-group">
                            <i class="fas fa-dollar-sign"></i>
                            <select class="form-control" name="price_range">
                                <option value="">Khoảng giá</option>
                                <option value="0-2000000">Dưới 2 triệu</option>
                                <option value="2000000-3000000">2-3 triệu</option>
                                <option value="3000000-5000000">3-5 triệu</option>
                                <option value="5000000-999999999">Trên 5 triệu</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="categories-section">
        <div class="container">
            <h2 class="section-title">Danh Mục Phòng Trọ</h2>
            <div class="category-grid">
                <div class="category-card" onclick="window.location.href='Views/posts/list.php?category=1'">
                    <div class="category-icon"><i class="fas fa-home"></i></div>
                    <div class="category-name">Phòng trọ SV</div>
                    <div class="category-count"><?php echo isset($categories[0]) ? $categories[0]['count'] : 0; ?> phòng</div>
                </div>

                <div class="category-card" onclick="window.location.href='Views/posts/list.php?category=2'">
                    <div class="category-icon"><i class="fas fa-building"></i></div>
                    <div class="category-name">Căn hộ mini</div>
                    <div class="category-count"><?php echo isset($categories[1]) ? $categories[1]['count'] : 0; ?> phòng</div>
                </div>

                <div class="category-card" onclick="window.location.href='Views/posts/list.php?category=3'">
                    <div class="category-icon"><i class="fas fa-house"></i></div>
                    <div class="category-name">Nhà nguyên căn</div>
                    <div class="category-count"><?php echo isset($categories[2]) ? $categories[2]['count'] : 0; ?> phòng</div>
                </div>

                <div class="category-card" onclick="window.location.href='Views/posts/list.php?category=4'">
                    <div class="category-icon"><i class="fas fa-users"></i></div>
                    <div class="category-name">Ở ghép</div>
                    <div class="category-count"><?php echo isset($categories[3]) ? $categories[3]['count'] : 0; ?> phòng</div>
                </div>
            </div>
        </div>
    </section>

    <section class="featured-posts-section">
        <div class="container">
            <h2 class="section-title">Phòng Trọ Nổi Bật</h2>
            <div class="grid grid-3">
                <?php if (count($featured_posts) > 0): ?>
                    <?php foreach ($featured_posts as $index => $post): ?>
                    <div class="post-card">
                        <div class="post-image">
                            <img src="<?php echo $post['image'] ? 'uploads/' . htmlspecialchars($post['image']) : getPlaceholderImage(400, 250, '667eea', urlencode($post['title'])); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                            <span class="post-badge"><?php 
                                $badges = ['Mới đăng', 'Nổi bật', 'Giá rẻ'];
                                echo $badges[$index % count($badges)];
                            ?></span>
                            <?php 
                            $isFavorited = in_array($post['id'], $user_favorites);
                            $activeClass = $isFavorited ? 'active' : '';
                            $iconClass = $isFavorited ? 'fas' : 'far';
                            ?>
                            <button class="favorite-btn <?php echo $activeClass; ?>" onclick="toggleFavorite(<?php echo $post['id']; ?>, this)">
                                <i class="<?php echo $iconClass; ?> fa-heart"></i>
                            </button>
                        </div>
                        <div class="post-content">
                            <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                            <div class="post-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($post['address'] . ', ' . $post['district']); ?></span>
                            </div>
                            <div class="post-features">
                                <div class="feature-item">
                                    <i class="fas fa-door-open"></i>
                                    <span><?php echo $post['room_status'] == 'available' ? '✓ Còn trống' : '✗ Đã hết'; ?></span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-expand"></i>
                                    <span><?php echo htmlspecialchars($post['area']); ?>m²</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-users"></i>
                                    <span><?php echo htmlspecialchars($post['max_people']); ?> người</span>
                                </div>
                            </div>
                            <div class="post-footer">
                                <div class="post-price"><?php echo number_format($post['price'], 0, ',', '.'); ?>/tháng</div>
                                <a href="Views/posts/detail.php?slug=<?php echo $post['slug']; ?>" class="btn btn-primary btn-sm">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="grid-column: 1/-1; text-align: center; padding: 2rem;">Chưa có phòng trọ nào được đăng tải</p>
                <?php endif; ?>
            </div>

            <div class="text-center mt-4">
                <a href="Views/posts/list.php" class="btn btn-outline btn-lg">Xem tất cả phòng trọ</a>
            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="container">
            <h2 class="stats-title">Không Gian Sống Tiện Nghi</h2>
            
            <div class="amenities-box">
                <div class="amenity-item">
                    <div class="amenity-icon">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <div class="amenity-name">WiFi</div>
                </div>

                <div class="amenity-item">
                    <div class="amenity-icon">
                        <i class="fas fa-snowflake"></i>
                    </div>
                    <div class="amenity-name">Điều hòa</div>
                </div>

                <div class="amenity-item">
                    <div class="amenity-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <div class="amenity-name">Nước nóng</div>
                </div>

                <div class="amenity-item">
                    <div class="amenity-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="amenity-name">An ninh 24/7</div>
                </div>

                <div class="amenity-item">
                    <div class="amenity-icon">
                        <i class="fas fa-parking"></i>
                    </div>
                    <div class="amenity-name">Chỗ để xe</div>
                </div>

                <div class="amenity-item">
                    <div class="amenity-icon">
                        <i class="fas fa-wind"></i>
                    </div>
                    <div class="amenity-name">Máy giặt</div>
                </div>

                <div class="amenity-item">
                    <div class="amenity-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="amenity-name">Giờ tự do</div>
                </div>

                <div class="amenity-item">
                    <div class="amenity-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="amenity-name">Gác lửng</div>
                </div>
            </div>

            <div class="stats-cta" style="margin-top: 3rem;">
                <div class="cta-left">
                    <h3 class="cta-brand">NhaTot</h3>
                    <p class="cta-slogan">"Nơi khởi đầu mọi hành trình, tìm nhà - trọn niềm tin"</p>
                </div>
                <div class="cta-right">
                    <h4>Tìm phòng trọ lý tưởng</h4>
                    <p>Hàng nghìn phòng trọ chất lượng đang chờ bạn</p>
                    <a href="Views/auth/register.php" class="cta-explore-btn">
                        <i class="fas fa-search"></i> Khám phá ngay
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <h3><i class="fas fa-home"></i> NhaTot</h3>
                    <p>Nền tảng tìm kiếm phòng trọ uy tín dành cho sinh viên. Giúp sinh viên tìm được phòng trọ phù hợp, giá rẻ, gần trường.</p>
                </div>

                <div class="footer-links">
                    <h4>Về chúng tôi</h4>
                    <ul>
                        <li><a href="#">Giới thiệu</a></li>
                        <li><a href="#">Liên hệ</a></li>
                        <li><a href="#">Điều khoản</a></li>
                        <li><a href="#">Chính sách</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4>Hỗ trợ</h4>
                    <ul>
                        <li><a href="#">Hướng dẫn đăng tin</a></li>
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                        <li><a href="#">Báo cáo sai phạm</a></li>
                        <li><a href="#">Góp ý</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4>Kết nối</h4>
                    <ul>
                        <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
                        <li><a href="#"><i class="fab fa-youtube"></i> YouTube</a></li>
                        <li><a href="#"><i class="fab fa-tiktok"></i> TikTok</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 NhaTot. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
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

        // Carousel functionality
        let currentSlideIndex = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const buttons = document.querySelectorAll('.carousel-btn');

        function showSlide(index) {
            if (index >= slides.length) {
                currentSlideIndex = 0;
            } else if (index < 0) {
                currentSlideIndex = slides.length - 1;
            } else {
                currentSlideIndex = index;
            }

            slides.forEach(slide => slide.classList.remove('active'));
            buttons.forEach(btn => btn.classList.remove('active'));

            slides[currentSlideIndex].classList.add('active');
            buttons[currentSlideIndex].classList.add('active');
        }

        function changeSlide(direction) {
            showSlide(currentSlideIndex + direction);
        }

        function currentSlide(index) {
            showSlide(index);
        }

        // Auto-rotate carousel every 5 seconds
        setInterval(() => {
            changeSlide(1);
        }, 5000);

        function toggleFavorite(postId, button) {
            <?php if (!isLoggedIn()): ?>
            alert('Vui lòng đăng nhập để yêu thích');
            window.location.href = 'Views/auth/login.php';
            return;
            <?php endif; ?>
            
            const isFavorited = button.classList.contains('active');
            const action = isFavorited ? 'remove' : 'add';
            
            fetch('Controllers/FavoriteController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=' + action + '&post_id=' + postId
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    button.classList.toggle('active');
                    const icon = button.querySelector('i');
                    icon.classList.toggle('far');
                    icon.classList.toggle('fas');
                } else {
                    alert(data.message || 'Lỗi khi thay đổi yêu thích');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
    <script src="assets/js/notifications.js"></script>
</body>
</html>
