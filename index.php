<?php
// Include configuration
require_once __DIR__ . '/config.php';

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
    
    // Fetch featured posts
    $featured_stmt = $conn->prepare("SELECT id, title, location, price, image, category FROM posts LIMIT 3");
    $featured_stmt->execute();
    $featured_posts = $featured_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch category counts
    $categories_stmt = $conn->prepare("SELECT category, COUNT(*) as count FROM posts GROUP BY category");
    $categories_stmt->execute();
    $categories_data = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($categories_data) {
        $categories = $categories_data;
    }
    
    // Fetch statistics
    $total_posts_stmt = $conn->prepare("SELECT COUNT(*) as count FROM posts");
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
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Tìm Trọ Sinh Viên</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
        }

        .hero-content h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .hero-content p {
            font-size: 1.25rem;
            opacity: 0.95;
            margin-bottom: 2rem;
        }

        .search-box {
            background: white;
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            max-width: 900px;
            margin: 0 auto;
        }

        .search-form {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 1rem;
        }

        .search-input-group {
            position: relative;
        }

        .search-input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        .search-input-group input,
        .search-input-group select {
            padding-left: 3rem;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 3rem;
            text-align: center;
        }

        .stat-item {
            padding: 2rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.125rem;
            opacity: 0.9;
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
                <i class="fas fa-home"></i>
                <span>Tìm Trọ SV</span>
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
                <li><a href="Views/chat/chat.php" class="nav-link">Tin nhắn</a></li>
                <?php endif; ?>
            </ul>

            <div class="nav-actions">
                <?php if (isLoggedIn()): ?>
                    <div style="position: relative; display: inline-block;">
                        <a href="Views/user/notifications.php" class="btn btn-outline btn-sm" title="Thông báo">
                            <i class="fas fa-bell"></i> Thông báo
                        </a>
                        <?php 
                        require_once 'Models/Notification.php';
                        $notifModel = new Notification();
                        $unread = $notifModel->getUnreadCount($_SESSION['user_id']);
                        if ($unread > 0): 
                        ?>
                        <span style="position: absolute; top: -5px; right: -5px; background: var(--danger-color); color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">
                            <?php echo $unread > 99 ? '99+' : $unread; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <a href="Views/user/profile.php" class="btn btn-outline btn-sm"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                    <a href="Controllers/AuthController.php?action=logout" class="btn btn-danger btn-sm">Đăng xuất</a>
                <?php else: ?>
                    <a href="Views/auth/login.php" class="btn btn-outline btn-sm">Đăng nhập</a>
                    <a href="Views/auth/register.php" class="btn btn-primary btn-sm">Đăng ký</a>
                <?php endif; ?>
            </div>

            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

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
                            <img src="<?php echo htmlspecialchars($post['image'] ?? 'https://via.placeholder.com/400x250'); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                            <span class="post-badge"><?php 
                                $badges = ['Mới đăng', 'Nổi bật', 'Giá rẻ'];
                                echo $badges[$index % count($badges)];
                            ?></span>
                            <button class="favorite-btn" onclick="toggleFavorite(<?php echo $post['id']; ?>, this)">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                        <div class="post-content">
                            <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                            <div class="post-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($post['location']); ?></span>
                            </div>
                            <div class="post-features">
                                <div class="feature-item">
                                    <i class="fas fa-expand"></i>
                                    <span>20m²</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-users"></i>
                                    <span>2 người</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-wifi"></i>
                                    <span>WiFi</span>
                                </div>
                            </div>
                            <div class="post-footer">
                                <div class="post-price"><?php echo number_format($post['price'], 0, ',', '.'); ?>/tháng</div>
                                <a href="Views/posts/detail.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">Xem chi tiết</a>
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
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number"><?php echo number_format($total_posts); ?>+</div>
                    <div class="stat-label">Phòng trọ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo number_format($total_users); ?>+</div>
                    <div class="stat-label">Sinh viên</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo number_format($total_landlords); ?>+</div>
                    <div class="stat-label">Chủ trọ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $satisfied_rate; ?>%</div>
                    <div class="stat-label">Hài lòng</div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <h3><i class="fas fa-home"></i> Tìm Trọ SV</h3>
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
                <p>&copy; 2024 Tìm Trọ Sinh Viên. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script>
        function toggleFavorite(postId, button) {
            button.classList.toggle('active');
            const icon = button.querySelector('i');
            if (button.classList.contains('active')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
        }
    </script>
</body>
</html>
