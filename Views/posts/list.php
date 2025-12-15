<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Models/PostImage.php';
require_once __DIR__ . '/../../helpers.php';

$search = sanitize($_GET['search'] ?? ''); // Tìm kiếm: từ khóa từ URL
$district = sanitize($_GET['district'] ?? ''); // Lọc theo quận/huyện
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0; // Giá tối thiểu (triệu)
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : PHP_INT_MAX; // Giá tối đa
$room_type = sanitize($_GET['room_type'] ?? ''); // Loại phòng: single/shared/apartment/house
$sort = sanitize($_GET['sort'] ?? 'newest'); // Sắp xếp: newest/price_asc/price_desc
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1; // Phân trang: lấy page từ URL, mặc định = 1
$per_page = 6; // Số bài đăng mỗi trang
$posts = [];
$total_posts = 0;
$total_pages = 0;
$postImageModel = new PostImage();

try {
    $db = getDB();
    $query = "SELECT id, title, slug, address, district, city, price, area, room_type, room_status, max_people, created_at FROM posts WHERE status = 'approved' AND (city = 'TP. Đà Nẵng' OR city = 'Đà Nẵng')";
    $params = [];
    
    if (!empty($search)) { // Lọc search: tìm trong tiêu đề hoặc địa chỉ
        $query .= " AND (title LIKE ? OR address LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
    }
    if (!empty($district)) { // Lọc district: WHERE district = ?
        $query .= " AND district = ?";
        $params[] = $district;
    }
    if ($min_price > 0 || $max_price < PHP_INT_MAX) { // Lọc giá: BETWEEN min và max (nhân 1M)
        $query .= " AND price BETWEEN ? AND ?";
        $params[] = $min_price * 1000000;
        $params[] = $max_price * 1000000;
    }
    if (!empty($room_type)) { // Lọc loại phòng: WHERE room_type = ?
        $query .= " AND room_type = ?";
        $params[] = $room_type;
    }
    
    // Count total
    $count_query = str_replace("SELECT id, title, slug, address, district, city, price, area, room_type, room_status, max_people, created_at", "SELECT COUNT(*) as cnt", $query); // Đếm tổng posts
    $count_stmt = $db->prepare($count_query);
    $count_stmt->execute($params);
    $count_result = $count_stmt->fetch(PDO::FETCH_ASSOC);
    $total_posts = $count_result['cnt'] ?? 0; // Lưu tổng số bài
    $total_pages = ceil($total_posts / $per_page); // tính tổng số trang
    
    // Add sort
    switch ($sort) { // Sắp xếp theo: price_asc/price_desc/newest
        case 'price_asc':
            $query .= " ORDER BY price ASC"; // Giá thấp → cao
            break;
        case 'price_desc':
            $query .= " ORDER BY price DESC"; // Giá cao → thấp
            break;
        default:
            $query .= " ORDER BY created_at DESC"; // Mặc định: mới nhất
    }

    $offset = ($page - 1) * $per_page; // Add pagination: LIMIT items_per_page OFFSET (page-1)*per_page
    $query .= " LIMIT ? OFFSET ?";
    $params[] = $per_page;
    $params[] = $offset;
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Thêm ảnh chính cho mỗi bài đăng
    foreach ($posts as &$post) {
        $post['image'] = $postImageModel->getPrimaryImage($post['id']);
    }
    unset($post);
} catch (PDOException $e) {
    error_log("Query error: " . $e->getMessage());
    $posts = [];
    $total_posts = 0;
}

// Get user's favorites if logged in
$user_favorites = [];
if (isLoggedIn()) {
    try {
        $db = getDB();
        $fav_stmt = $db->prepare("SELECT post_id FROM favorites WHERE user_id = ?");
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
    <title>Danh sách phòng trọ - NhaTot</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-header {
            background: #3470f3ff;
            color: white;
            padding: 3rem 0;
            text-align: center;
        }

        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .content-wrapper {
            padding: 3rem 0;
        }

        .posts-layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
        }

        .filters-sidebar {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .filter-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        .filter-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .filter-title {
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .price-inputs {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 0.5rem;
            align-items: center;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            cursor: pointer;
        }

        .posts-main {
            min-height: 500px;
        }

        .posts-header {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .sort-options {
            display: flex;
            gap: 1rem;
            align-items: center;
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
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
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
            font-size: 0.875rem;
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
            color: #3b82f6;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 3rem;
            flex-wrap: wrap;
        }

        .pagination a {
            text-decoration: none;
        }

        .pagination button {
            padding: 0.75rem 1.25rem;
            border: 1px solid var(--border-color);
            background: white;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pagination button:hover {
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            color: white;
            border-color: #3b82f6;
        }

        .pagination button.active {
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            color: white;
            border-color: #3b82f6;
        }

        .no-results {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }

        .no-results i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 1024px) {
            .posts-layout {
                grid-template-columns: 1fr;
            }

            .filters-sidebar {
                position: static;
            }
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
                <li><a href="list.php" class="nav-link active">Danh sách trọ</a></li>
                <?php if (isLoggedIn() && $_SESSION['role'] === 'landlord'): ?>
                <li><a href="create.php" class="nav-link">Đăng tin</a></li>
                <?php endif; ?>
                <?php if (isLoggedIn() && $_SESSION['role'] === 'tenant'): ?>
                <li><a href="../user/favorites.php" class="nav-link">Yêu thích</a></li>
                <?php endif; ?>
                <?php if (isLoggedIn()): ?>
                <li style="position: relative;">
                    <a href="../chat/chat.php" class="nav-link">Tin nhắn</a>
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

    <div class="page-header">
        <h1>Danh Sách Phòng Trọ</h1>
        <p>Tìm kiếm phòng trọ phù hợp với bạn</p>
    </div>

    <div class="content-wrapper">
        <div class="container">
            <div class="posts-layout">
                <aside class="filters-sidebar">
                    <h3 style="margin-bottom: 1.5rem;">Bộ lọc</h3>

                    <div class="filter-section">
                        <input type="text" class="form-control" placeholder="Tìm kiếm..." style="margin-bottom: 1rem;">
                    </div>

                    <div class="filter-section">
                        <div class="filter-title">Khu vực (TP. Đà Nẵng)</div>
                        <select class="form-control" id="districtFilter">
                            <option value="">Tất cả quận/huyện</option>
                            <option value="Quận Hải Châu">Quận Hải Châu</option>
                            <option value="Quận Thanh Khê">Quận Thanh Khê</option>
                            <option value="Quận Sơn Trà">Quận Sơn Trà</option>
                            <option value="Quận Ngũ Hành Sơn">Quận Ngũ Hành Sơn</option>
                            <option value="Quận Liên Chiểu">Quận Liên Chiểu</option>
                            <option value="Huyện Hòa Vang">Huyện Hòa Vang</option>
                        </select>
                    </div>

                    <div class="filter-section">
                        <div class="filter-title">Khoảng giá (triệu/tháng)</div>
                        <div class="price-inputs">
                            <input type="number" class="form-control" id="minPrice" placeholder="Từ" min="0">
                            <span>-</span>
                            <input type="number" class="form-control" id="maxPrice" placeholder="Đến" min="0">
                        </div>
                    </div>

                    <div class="filter-section">
                        <div class="filter-title">Loại phòng</div>
                        <div class="checkbox-group">
                            <label>
                                <input type="checkbox" name="room_type" value="single">
                                <span>Phòng đơn</span>
                            </label>
                            <label>
                                <input type="checkbox" name="room_type" value="shared">
                                <span>Phòng ghép</span>
                            </label>
                            <label>
                                <input type="checkbox" name="room_type" value="apartment">
                                <span>Căn hộ</span>
                            </label>
                            <label>
                                <input type="checkbox" name="room_type" value="house">
                                <span>Nhà nguyên căn</span>
                            </label>
                        </div>
                    </div>

                    <div class="filter-section">
                        <div class="filter-title">Tiện ích</div>
                        <div class="checkbox-group">
                            <label>
                                <input type="checkbox" name="amenities" value="wifi">
                                <span>WiFi</span>
                            </label>
                            <label>
                                <input type="checkbox" name="amenities" value="ac">
                                <span>Điều hòa</span>
                            </label>
                            <label>
                                <input type="checkbox" name="amenities" value="fridge">
                                <span>Tủ lạnh</span>
                            </label>
                            <label>
                                <input type="checkbox" name="amenities" value="washing">
                                <span>Máy giặt</span>
                            </label>
                        </div>
                    </div>

                    <button class="btn btn-primary" onclick="filterPosts()" style="width: 100%;">
                        <i class="fas fa-filter"></i> Áp dụng lọc
                    </button>
                </aside>

                <main class="posts-main">
                    <div class="posts-header">
                        <div>
                            <strong><?php echo $total_posts; ?> phòng trọ</strong> phù hợp
                        </div>
                        <div class="sort-options">
                            <span>Sắp xếp:</span>
                            <select class="form-control" style="width: auto;" onchange="updateSort(this.value)">
                                <option value="newest">Mới nhất</option>
                                <option value="price_asc">Giá thấp đến cao</option>
                                <option value="price_desc">Giá cao đến thấp</option>
                            </select>
                        </div>
                    </div>

                    <?php if (count($posts) > 0): ?>
                    <div class="posts-grid">
                        <?php foreach ($posts as $post): ?>
                        <div class="post-card">
                            <div class="post-image">
                                <img src="<?php echo $post['image'] ? '../../uploads/' . htmlspecialchars($post['image']) : getPlaceholderImage(400, 250, '667eea', urlencode($post['room_type'])); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                <span class="post-badge">
                                    <?php 
                                    $created = strtotime($post['created_at']);
                                    $diff = time() - $created;
                                    if ($diff < 86400 * 7) echo 'Mới đăng';
                                    elseif ($post['price'] < 2000000) echo 'Giá rẻ';
                                    else echo 'Nổi bật';
                                    ?>
                                </span>
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
                                <h3 class="post-title"><?php echo htmlspecialchars(substr($post['title'], 0, 50)); ?></h3>
                                <div class="post-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($post['address']) . ', ' . htmlspecialchars($post['district']); ?></span>
                                </div>
                                <div class="post-features">
                                    <div class="feature-item">
                                        <i class="fas fa-door-open"></i>
                                        <span><?php echo $post['room_status'] == 'available' ? '✓ Còn trống' : '✗ Đã hết'; ?></span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-expand"></i>
                                        <span><?php echo number_format($post['area'], 1); ?>m²</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-<?php echo $post['max_people'] > 1 ? 'users' : 'user'; ?>"></i>
                                        <span><?php echo $post['max_people']; ?> người</span>
                                    </div>
                                </div>
                                <div class="post-footer">
                                    <div class="post-price"><?php echo number_format($post['price'] / 1000000, 1); ?>tr/tháng</div>
                                    <a href="detail.php?slug=<?php echo $post['slug']; ?>" class="btn btn-primary btn-sm">Chi tiết</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="no-results">
                        <i class="fas fa-search"></i>
                        <h3>Không tìm thấy phòng trọ</h3>
                        <p>Vui lòng thử thay đổi điều kiện tìm kiếm</p>
                    </div>
                    <?php endif; ?>

                    <!-- Hiển thị pagination nếu có >1 trang -->
                    <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <!-- Nút "Prev" (trang trước) -->
                        <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($district) ? '&district=' . urlencode($district) : ''; ?>">
                            <button><i class="fas fa-chevron-left"></i></button>
                        </a>
                        <?php endif; ?>

                        <?php 
                        $start_page = max(1, $page - 2); // Tính trang bắt đầu: page - 2
                        $end_page = min($total_pages, $page + 2); // Tính trang kết thúc: page + 2
                        
                        if ($start_page > 1): // Nếu ko phải trang 1, hiển thị trang 1 + "..." ?>
                        <a href="?page=1<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                            <button>1</button>
                        </a>
                        <?php if ($start_page > 2): ?>
                        <span style="padding: 0.75rem 0.5rem;">...</span> <!-- Ellipsis nếu khoảng trống > 2 trang -->
                        <?php endif;
                        endif;
                        
                        for ($i = $start_page; $i <= $end_page; $i++): // Vòng lặp hiển thị các trang giữa ?>
                        <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($district) ? '&district=' . urlencode($district) : ''; ?>">
                            <button <?php echo $i === $page ? 'class="active"' : ''; ?>><?php echo $i; ?></button>
                        </a>
                        <?php endfor; // Kết thúc vòng lặp trang
                        
                        if ($end_page < $total_pages): // Nếu có trang sau, show "..." + trang cuối 
                            if ($end_page < $total_pages - 1): ?>
                        <span style="padding: 0.75rem 0.5rem;">// Nếu khoảng trống > 2, show ellipsis ...</span>
                            <?php endif; ?>
                        <a href="?page=<?php echo $total_pages; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                            <button><?php echo $total_pages; ?></button>
                        </a>
                        <?php endif; ?>

                        <!-- Nút "Next" (trang sau) -->
                        <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($district) ? '&district=' . urlencode($district) : ''; ?>">
                            <button><i class="fas fa-chevron-right"></i></button>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

    <script src="../../assets/js/main.js"></script>
    <script>
        function filterPosts() {
            const search = document.querySelector('input[placeholder="Tìm kiếm"]')?.value || '';
            const district = document.getElementById('districtFilter').value;
            const minPrice = document.getElementById('minPrice').value;
            const maxPrice = document.getElementById('maxPrice').value;
            
            // Get selected room type from checkboxes
            const roomTypeCheckboxes = document.querySelectorAll('input[name="room_type"]:checked');
            let roomType = '';
            if (roomTypeCheckboxes.length > 0) {
                roomType = roomTypeCheckboxes[0].value; // Get first checked value
            }
            
            let url = 'list.php?';
            if (search) url += '&search=' + encodeURIComponent(search);
            if (district) url += '&district=' + encodeURIComponent(district);
            if (minPrice) url += '&min_price=' + minPrice;
            if (maxPrice) url += '&max_price=' + maxPrice;
            if (roomType) url += '&room_type=' + encodeURIComponent(roomType);
            
            window.location.href = url;
        }
        
        function updateSort(value) {
            const params = new URLSearchParams(window.location.search);
            params.set('sort', value);
            window.location.href = '?' + params.toString();
        }
        
        function toggleFavorite(postId, btn) {
            <?php if (!isLoggedIn()): ?>
            alert('Vui lòng đăng nhập để yêu thích');
            window.location.href = '../auth/login.php';
            return;
            <?php endif; ?>
            
            const isFavorited = btn.classList.contains('active');
            const action = isFavorited ? 'remove' : 'add';
            
            fetch('../../Controllers/FavoriteController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=' + action + '&post_id=' + postId
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    btn.classList.toggle('active');
                    btn.querySelector('i').classList.toggle('far');
                    btn.querySelector('i').classList.toggle('fas');
                }
            });
        }
        
        // Load filter values from URL on page load
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            
            // Load search value
            const searchInput = document.querySelector('input[placeholder="Tìm kiếm"]');
            if (searchInput && params.has('search')) {
                searchInput.value = params.get('search');
            }
            
            // Load district filter
            const districtFilter = document.getElementById('districtFilter');
            if (districtFilter && params.has('district')) {
                districtFilter.value = params.get('district');
            }
            
            // Load price filters
            const minPrice = document.getElementById('minPrice');
            if (minPrice && params.has('min_price')) {
                minPrice.value = params.get('min_price');
            }
            
            const maxPrice = document.getElementById('maxPrice');
            if (maxPrice && params.has('max_price')) {
                maxPrice.value = params.get('max_price');
            }
            
            // Load room type checkboxes
            const roomTypeCheckboxes = document.querySelectorAll('input[name="room_type"]');
            if (params.has('room_type') && roomTypeCheckboxes.length > 0) {
                const selectedRoomType = params.get('room_type');
                roomTypeCheckboxes.forEach(checkbox => {
                    if (checkbox.value === selectedRoomType) {
                        checkbox.checked = true;
                    }
                });
            }
        });

        // User menu toggle
        function toggleUserMenu(event) {
            event.stopPropagation();
            const menu = document.getElementById('userDropdownMenu');
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('userDropdownMenu');
            const userWrapper = document.querySelector('.user-menu-wrapper');
            if (menu && !userWrapper.contains(event.target)) {
                menu.style.display = 'none';
            }
        });
    </script>
    <script src="../../assets/js/notifications.js"></script>
</body>
</html>
