<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../helpers.php';
require_once __DIR__ . '/../../Models/Post.php';
require_once __DIR__ . '/../../Models/PostImage.php';
require_once __DIR__ . '/../../Models/User.php';

// Get post ID from URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$post_id) {
    redirect('../../index.php');
}

// Get post data
$postModel = new Post();
$post = $postModel->findById($post_id);

if (!$post) {
    redirect('../../index.php');
}

// Get post images
$postImageModel = new PostImage();
$images = $postImageModel->getImages($post_id);
$primaryImage = $postImageModel->getPrimaryImage($post_id);

// Get landlord info
$userModel = new User();
$landlord = $userModel->findById($post['user_id'] ?? 0);

// Check if post is favorited by current user
$is_favorited = false;
if (isLoggedIn()) {
    try {
        $conn = getDB();
        $fav_check = $conn->prepare("SELECT id FROM favorites WHERE post_id = ? AND user_id = ?");
        $fav_check->execute([$post_id, $_SESSION['user_id']]);
        $is_favorited = $fav_check->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error checking favorite: " . $e->getMessage());
    }
}

// Get post likes count
$likes_count = 0;
try {
    $conn = getDB();
    $likes_check = $conn->prepare("SELECT COUNT(*) as count FROM post_likes WHERE post_id = ?");
    $likes_check->execute([$post_id]);
    $likes_result = $likes_check->fetch(PDO::FETCH_ASSOC);
    $likes_count = $likes_result['count'] ?? 0;
} catch (PDOException $e) {
    error_log("Error checking likes: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết phòng trọ - NhaTot</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .content-wrapper {
            padding: 3rem 0;
        }

        .detail-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .detail-main {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .image-gallery {
            position: relative;
        }

        .main-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
        }

        .thumbnail-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            padding: 1rem;
            background: var(--light-color);
        }

        .thumbnail {
            height: 100px;
            object-fit: cover;
            border-radius: var(--radius-sm);
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .thumbnail:hover,
        .thumbnail.active {
            border-color: var(--primary-color);
        }

        .detail-content {
            padding: 2rem;
        }

        .detail-header {
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .detail-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .detail-meta {
            display: flex;
            gap: 2rem;
            color: var(--text-secondary);
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        .detail-section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--light-color);
            border-radius: var(--radius-md);
        }

        .feature-icon {
            color: var(--primary-color);
            font-size: 1.25rem;
        }

        .amenities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .amenity-tag {
            padding: 0.5rem 1rem;
            background: var(--light-color);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-sidebar {
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .price-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
        }

        .price-amount {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .price-unit {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .contact-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
        }

        .landlord-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .landlord-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        .landlord-details h4 {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .landlord-details p {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .contact-actions {
            display: grid;
            gap: 1rem;
        }

        .reviews-section {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            margin-top: 2rem;
        }

        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .rating-summary {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .rating-score {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .rating-stars {
            color: #fbbf24;
            font-size: 1.5rem;
        }

        .review-item {
            padding: 1.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .review-item:last-child {
            border-bottom: none;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .reviewer-info {
            display: flex;
            gap: 1rem;
        }

        .reviewer-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }

        .review-form {
            background: var(--light-color);
            padding: 1.5rem;
            border-radius: var(--radius-md);
            margin-top: 2rem;
        }

        .star-rating {
            display: flex;
            gap: 0.5rem;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .star-rating i {
            color: #d1d5db;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .star-rating i.fas {
            color: #fbbf24;
        }

        .star-rating i:hover,
        .star-rating i:hover ~ i {
            color: #fbbf24;
        }

        @media (max-width: 1024px) {
            .detail-layout {
                grid-template-columns: 1fr;
            }

            .detail-sidebar {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .features-grid {
                grid-template-columns: 1fr;
            }

            .main-image {
                height: 300px;
            }

            .thumbnail-grid {
                grid-template-columns: repeat(3, 1fr);
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
                <li><a href="list.php" class="nav-link">Danh sách trọ</a></li>
                <?php if (isLoggedIn() && $_SESSION['role'] === 'landlord'): ?>
                <li><a href="create.php" class="nav-link">Đăng tin</a></li>
                <?php endif; ?>
                <?php if (isLoggedIn() && $_SESSION['role'] === 'tenant'): ?>
                <li><a href="../user/favorites.php" class="nav-link">Yêu thích</a></li>
                <?php endif; ?>
                <li><a href="../chat/chat.php" class="nav-link">Tin nhắn</a></li>
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

    <div class="content-wrapper">
        <div class="container">
            <div class="detail-layout">
                <div class="detail-main">
                    <div class="image-gallery">
                        <img src="<?php echo $primaryImage ? '../../uploads/' . htmlspecialchars($primaryImage) : getPlaceholderImage(1200, 600, '667eea', 'No Image'); ?>" alt="<?php echo htmlspecialchars($post['title'] ?? 'Phòng trọ'); ?>" class="main-image" id="mainImage">
                        <div class="thumbnail-grid">
                            <?php if (!empty($images)): ?>
                                <?php foreach ($images as $img): ?>
                                <img src="../../uploads/<?php echo htmlspecialchars($img['image_url']); ?>" alt="Thumbnail" class="thumbnail <?php echo $img['is_primary'] ? 'active' : ''; ?>" onclick="changeMainImage(this)">
                                <?php endforeach; ?>
                            <?php else: ?>
                                <img src="<?php echo getPlaceholderImage(300, 200, '667eea', '1'); ?>" alt="Placeholder" class="thumbnail active">
                                <img src="<?php echo getPlaceholderImage(300, 200, '764ba2', '2'); ?>" alt="Placeholder" class="thumbnail">
                                <img src="<?php echo getPlaceholderImage(300, 200, '3b82f6', '3'); ?>" alt="Placeholder" class="thumbnail">
                                <img src="<?php echo getPlaceholderImage(300, 200, '8b5cf6', '4'); ?>" alt="Placeholder" class="thumbnail">
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="detail-content">
                        <div class="detail-header">
                            <h1 class="detail-title"><?php echo htmlspecialchars($post['title'] ?? 'Phòng trọ'); ?></h1>
                            <div class="detail-meta">
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($post['address'] ?? ''); ?>, <?php echo htmlspecialchars($post['district'] ?? ''); ?>, <?php echo htmlspecialchars($post['city'] ?? ''); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Đăng <?php echo date('d/m/Y', strtotime($post['created_at'] ?? date('Y-m-d H:i:s'))); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-eye"></i>
                                    <span><?php echo $post['view_count'] ?? 0; ?> lượt xem</span>
                                </div>
                            </div>
                        </div>

                        <div class="detail-section">
                            <h2 class="section-title">Thông tin cơ bản</h2>
                            <div class="features-grid">
                                <div class="feature-item">
                                    <i class="fas fa-home feature-icon"></i>
                                    <div>
                                        <strong>Tình trạng phòng</strong>
                                        <p><?php 
                                            echo $post['room_status'] == 'available' ? '✓ Còn trống' : '✗ Đã hết';
                                        ?></p>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-expand feature-icon"></i>
                                    <div>
                                        <strong>Diện tích</strong>
                                        <p><?php echo $post['area'] ?? 0; ?>m²</p>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-users feature-icon"></i>
                                    <div>
                                        <strong>Số người tối đa</strong>
                                        <p><?php echo $post['max_people'] ?? 1; ?> người</p>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-venus-mars feature-icon"></i>
                                    <div>
                                        <strong>Giới tính</strong>
                                        <p><?php 
                                            $genders = ['male' => 'Nam', 'female' => 'Nữ', 'any' => 'Nam/Nữ'];
                                            echo $genders[$post['gender']] ?? 'Nam/Nữ';
                                        ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="detail-section">
                            <h2 class="section-title">Mô tả</h2>
                            <p><?php echo nl2br(htmlspecialchars($post['description'] ?? '')); ?></p>
                        </div>

                        <?php 
                        $amenities = !empty($post['amenities']) ? json_decode($post['amenities'], true) : [];
                        if (!empty($amenities)):
                        ?>
                        <div class="detail-section">
                            <h2 class="section-title">Tiện ích</h2>
                            <div class="amenities-list">
                                <?php 
                                $amenityIcons = [
                                    'wifi' => 'fa-wifi',
                                    'ac' => 'fa-snowflake',
                                    'fridge' => 'fa-lightbulb',
                                    'washing' => 'fa-wind',
                                    'parking' => 'fa-parking',
                                    'security' => 'fa-shield-alt',
                                    'water_heater' => 'fa-tint',
                                    'flexible_hours' => 'fa-clock',
                                    'mezzanine' => 'fa-layer-group'
                                ];
                                $amenityLabels = [
                                    'wifi' => 'WiFi',
                                    'ac' => 'Điều hòa',
                                    'fridge' => 'Tủ lạnh',
                                    'washing' => 'Máy giặt',
                                    'parking' => 'Chỗ để xe',
                                    'security' => 'An ninh 24/7',
                                    'water_heater' => 'Máy nóng lạnh',
                                    'flexible_hours' => 'Giờ giấc tự do',
                                    'mezzanine' => 'Gác lửng'
                                ];
                                foreach ($amenities as $amenity): 
                                ?>
                                <div class="amenity-tag">
                                    <i class="fas <?php echo $amenityIcons[$amenity] ?? 'fa-check'; ?>" style="color: var(--primary-color);"></i>
                                    <span><?php echo $amenityLabels[$amenity] ?? $amenity; ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <aside class="detail-sidebar">
                    <div class="price-card">
                        <div class="price-amount"><?php echo number_format($post['price'] ?? 0, 0, '.', '.'); ?> đ</div>
                        <div class="price-unit">VNĐ/tháng</div>
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                            <?php if (!empty($post['deposit_amount'])): ?>
                            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;">Tiền cọc: <?php echo number_format($post['deposit_amount'], 0, '.', '.'); ?> đ</p>
                            <?php endif; ?>
                            <p style="color: var(--text-secondary); font-size: 0.875rem;">
                                Điện: <?php echo !empty($post['electric_price']) ? number_format($post['electric_price'], 0, '.', '.') . 'đ/kWh' : 'Theo thoả thuận'; ?> 
                                | Nước: <?php echo !empty($post['water_price']) ? number_format($post['water_price'], 0, '.', '.') . 'đ/người' : 'Theo thoả thuận'; ?>
                            </p>
                        </div>
                    </div>

                    <?php if ($landlord): ?>
                    <div class="contact-card">
                        <h3 style="margin-bottom: 1.5rem;">Liên hệ</h3>
                        <div class="landlord-info">
                            <?php
                            $landlord_avatar = 'https://via.placeholder.com/60/3b82f6/ffffff?text=' . strtoupper(substr($landlord['username'] ?? 'U', 0, 1));
                            if (!empty($landlord['avatar'])) {
                                if (file_exists(__DIR__ . '/../../uploads/avatars/' . basename($landlord['avatar']))) {
                                    $landlord_avatar = '../../uploads/avatars/' . basename($landlord['avatar']);
                                } elseif (file_exists(__DIR__ . '/../../' . $landlord['avatar'])) {
                                    $landlord_avatar = '../../' . $landlord['avatar'];
                                }
                            }
                            ?>
                            <img src="<?php echo $landlord_avatar; ?>" alt="Chủ trọ" class="landlord-avatar">
                            <div class="landlord-details">
                                <h4><?php echo htmlspecialchars($landlord['username'] ?? 'Chủ trọ'); ?></h4>
                                <p>Chủ trọ</p>
                            </div>
                        </div>
                        <div class="contact-actions">
                            <a href="tel:<?php echo htmlspecialchars($landlord['phone'] ?? ''); ?>" class="btn btn-primary" style="background: #10b981; border-color: #10b981;">
                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($landlord['phone'] ?? 'Không có'); ?>
                            </a>
                            <a href="../chat/chat.php?user_id=<?php echo $post['user_id']; ?>" class="btn btn-primary" style="background: #10b981; border-color: #10b981;">
                                <i class="fas fa-comment"></i> Nhắn tin
                            </a>
                            <?php 
                            $fav_icon_class = $is_favorited ? 'fas' : 'far';
                            $fav_btn_class = $is_favorited ? 'btn-danger' : 'btn-danger';
                            ?>
                            <button class="btn <?php echo $fav_btn_class; ?> <?php echo $is_favorited ? 'active' : ''; ?>" onclick="toggleFavorite(<?php echo $post_id; ?>, this)" id="favBtn">
                                <i class="<?php echo $fav_icon_class; ?> fa-heart"></i> Yêu thích
                            </button>
                            <button class="btn btn-primary" onclick="toggleLike(<?php echo $post_id; ?>)" style="width: 100%; margin-top: 0.5rem; background: #0ea5e9; border-color: #0ea5e9;">
                                <i class="fas fa-thumbs-up"></i> Thích (<span id="likesCount"><?php echo $likes_count; ?></span>)
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="contact-card">
                        <h4 style="margin-bottom: 1rem;">Chia sẻ</h4>
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="btn btn-outline" style="flex: 1;">
                                <i class="fab fa-facebook"></i>
                            </button>
                            <button class="btn btn-outline" style="flex: 1;">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button class="btn btn-outline" style="flex: 1;">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </aside>
            </div>

            <div class="reviews-section">
                <div class="reviews-header">
                    <div>
                        <h2 class="section-title" style="margin-bottom: 0;">Bình luận</h2>
                    </div>
                    <div class="rating-summary">
                        <div class="rating-score" id="avgRating">0</div>
                        <div>
                            <div class="rating-stars" id="ratingStars">
                                <i class="far fa-star"></i>
                                <i class="far fa-star"></i>
                                <i class="far fa-star"></i>
                                <i class="far fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <p style="color: var(--text-secondary); margin-top: 0.25rem;"><span id="commentCount">0</span> bình luận</p>
                        </div>
                    </div>
                </div>

                <!-- Comments list -->
                <div id="comments-list" class="comments-list" style="width: 100%; box-sizing: border-box;">
                    <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                        <p>Đang tải bình luận...</p>
                    </div>
                </div>

                <!-- Comment form -->
                <div class="review-form">
                    <?php if (isLoggedIn() && ($_SESSION['role'] === 'tenant' || $_SESSION['role'] === 'admin')): ?>
                    <h3 style="margin-bottom: 1rem;">Viết bình luận</h3>
                    <form id="commentForm">
                        <div class="form-group">
                            <label class="form-label">Đánh giá của bạn</label>
                            <div class="star-rating" id="commentRating">
                                <i class="far fa-star" data-value="1"></i>
                                <i class="far fa-star" data-value="2"></i>
                                <i class="far fa-star" data-value="3"></i>
                                <i class="far fa-star" data-value="4"></i>
                                <i class="far fa-star" data-value="5"></i>
                                <input type="hidden" id="ratingInput" value="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bình luận</label>
                            <textarea class="form-control" id="commentContent" rows="4" placeholder="Chia sẻ trải nghiệm của bạn..." maxlength="5000"></textarea>
                            <small style="color: var(--text-secondary);" id="charCount">0/5000</small>
                        </div>
                        <button type="submit" class="btn btn-primary" id="submitCommentBtn">Gửi bình luận</button>
                    </form>
                    <?php elseif (isLoggedIn() && $_SESSION['role'] === 'landlord'): ?>
                    <div style="padding: 1.5rem; background: var(--light-color); border-radius: var(--radius-md); text-align: center;">
                        <p style="margin: 0; color: var(--text-secondary);">
                            <i class="fas fa-info-circle"></i> Chủ trọ chỉ có thể xem đánh giá và bình chọn bình luận
                        </p>
                    </div>
                    <?php else: ?>
                    <div style="padding: 1.5rem; background: var(--light-color); border-radius: var(--radius-md); text-align: center;">
                        <p style="margin: 0; color: var(--text-secondary);">
                            <a href="../auth/login.php" style="color: var(--primary-color); text-decoration: none;">Đăng nhập</a> để viết bình luận
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
        const POST_ID = <?php echo $post_id; ?>;
        const USER_ROLE = '<?php echo $_SESSION['role'] ?? 'guest'; ?>';

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

        // Load comments on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadComments();
            setupCommentForm();
        });

        // Load comments from API
        function loadComments(limit = 10, offset = 0) {
            fetch(`../../api/comments.php?action=getComments&post_id=${POST_ID}&limit=${limit}&offset=${offset}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        displayComments(data.data);
                        // Scroll to anchor after comments are rendered
                        scrollToAnchor();
                    } else {
                        console.error('Error loading comments:', data.error);
                        document.getElementById('comments-list').innerHTML = '<p style="color: red; text-align: center;">Lỗi tải bình luận</p>';
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    document.getElementById('comments-list').innerHTML = '<p style="color: red; text-align: center;">Lỗi kết nối</p>';
                });
        }

        // Helper function to get avatar URL
        function getAvatarUrl(avatar, username) {
            if (avatar && avatar !== '' && avatar !== null) {
                // If avatar starts with 'uploads/', use it as is
                if (avatar.startsWith('uploads/')) {
                    return '../../' + avatar;
                }
                // Otherwise add uploads/avatars/ prefix
                return '../../uploads/avatars/' + avatar;
            }
            // Fallback to placeholder
            return 'https://via.placeholder.com/40/3b82f6/ffffff?text=' + username.charAt(0).toUpperCase();
        }

        // Helper function to render nested replies recursively
        function renderNestedReplies(replies) {
            if (!replies || replies.length === 0) return '';
            
            return `<div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">` +
                replies.map(reply => {
                    const avatarUrl = getAvatarUrl(reply.avatar, reply.username);
                    return `
                <div style="margin-top: 0.75rem; padding-left: 1rem; border-left: 3px solid #10b981;">
                    <div style="display: flex; gap: 0.75rem;">
                        <img src="${avatarUrl}" alt="${reply.username}" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.25rem;">
                                <div>
                                    <strong style="font-size: 0.85rem;">
                                        ${escapeHtml(reply.username)}
                                        ${(reply.role === 'landlord' || reply.role === 'admin') ? `<span style="background: #10b981; color: white; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.65rem; margin-left: 0.5rem;">Chủ trọ</span>` : ''}
                                    </strong>
                                    <br>
                                    <small style="color: #6b7280; font-size: 0.75rem;">${formatDate(reply.created_at)}</small>
                                </div>
                                ${isCurrentUserComment(reply.user_id_display) ? `
                                    <button onclick="deleteComment(${reply.id})" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 0; font-size: 0.8rem;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                ` : ''}
                            </div>
                            <p style="margin: 0 0 0.5rem 0; color: #374151; line-height: 1.5; font-size: 0.85rem;">${escapeHtml(reply.content)}</p>
                            <div style="display: flex; gap: 1rem; align-items: center; font-size: 0.8rem;">
                                <button class="vote-btn upvote ${reply.user_vote === 1 ? 'active' : ''}" onclick="voteComment(${reply.id}, 1)" style="background: none; border: none; color: #6b7280; cursor: pointer; display: flex; align-items: center; gap: 0.25rem;">
                                    <i class="fas fa-thumbs-up"></i>
                                    <span>${reply.upvotes || 0}</span>
                                </button>
                                <button class="vote-btn downvote ${reply.user_vote === -1 ? 'active' : ''}" onclick="voteComment(${reply.id}, -1)" style="background: none; border: none; color: #6b7280; cursor: pointer; display: flex; align-items: center; gap: 0.25rem;">
                                    <i class="fas fa-thumbs-down"></i>
                                    <span>${reply.downvotes || 0}</span>
                                </button>
                                ${USER_ROLE && USER_ROLE !== 'guest' ? `
                                    <button onclick="toggleReplyForm(${reply.id})" style="background: none; border: none; color: var(--primary-color); cursor: pointer; display: flex; align-items: center; gap: 0.25rem; margin-left: auto;">
                                        <i class="fas fa-reply"></i>
                                        <span>Phản hồi</span>
                                    </button>
                                ` : ''}
                            </div>
                            <div id="reply-form-${reply.id}" style="margin-top: 0.75rem; display: none; padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem; width: 100%; box-sizing: border-box;">
                                <textarea id="reply-content-${reply.id}" placeholder="Nhập phản hồi của bạn..." style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-family: inherit; resize: vertical; min-height: 60px; box-sizing: border-box; font-size: 0.85rem;" maxlength="5000"></textarea>
                                <div style="margin-top: 0.5rem; font-size: 0.75rem; color: #6b7280;">
                                    <span id="reply-char-count-${reply.id}">0</span>/5000
                                </div>
                                <div style="margin-top: 0.5rem; display: flex; gap: 0.5rem;">
                                    <button onclick="submitReply(${reply.id}); return false;" style="flex: 1; padding: 0.4rem 0.75rem; background: var(--primary-color); color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-size: 0.8rem;">
                                        Gửi
                                    </button>
                                    <button onclick="toggleReplyForm(${reply.id}); return false;" style="flex: 1; padding: 0.4rem 0.75rem; background: white; color: #6b7280; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer; font-size: 0.8rem;">
                                        Hủy
                                    </button>
                                </div>
                            </div>
                            ${renderNestedReplies(reply.replies)}
                        </div>
                    </div>
                </div>
            `;
                }).join('') + `</div>`;
        }

        // Display comments on page
        function displayComments(data) {
            const commentsList = document.getElementById('comments-list');
            const comments = data.comments || [];
            const avgRating = parseFloat(data.avg_rating) || 0;
            const commentCount = parseInt(data.comment_count) || 0;
            
            // Debug: check if avatar exists
            console.log('Comments data:', comments);

            // Update rating display
            document.getElementById('avgRating').textContent = avgRating.toFixed(1);
            document.getElementById('commentCount').textContent = commentCount;
            updateRatingStars(Math.round(avgRating), 'ratingStars');

            // Display comments
            if (comments.length === 0) {
                commentsList.innerHTML = '<p style="text-align: center; color: var(--text-secondary); padding: 2rem;">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>';
                return;
            }

            commentsList.innerHTML = comments.map(comment => {
                let repliesHtml = renderNestedReplies(comment.replies);
                const avatarUrl = getAvatarUrl(comment.avatar, comment.username);

                return `
                    <div id="comment-${comment.id}" class="comment-item" style="border: 1px solid #e5e7eb; padding: 1.25rem; margin-bottom: 1rem; border-radius: 0.5rem; background: white; box-sizing: border-box;">
                        <div class="comment-header" style="display: flex; gap: 1rem; margin-bottom: 0.75rem;">
                            <img src="${avatarUrl}" alt="${comment.username}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div>
                                        <h5 style="margin: 0 0 0.25rem 0; font-weight: 600;">${escapeHtml(comment.username)}</h5>
                                        ${comment.rating > 0 ? `<div style="color: #fbbf24; font-size: 0.9rem; margin: 0.25rem 0;">${'⭐'.repeat(comment.rating)}</div>` : ''}
                                        <small style="color: #6b7280;">${formatDate(comment.created_at)}</small>
                                    </div>
                                    ${isCurrentUserComment(comment.user_id_display) ? `
                                        <button class="btn-delete-comment" onclick="deleteComment(${comment.id})" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 0;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        <p style="margin: 0 0 0.75rem 0; color: #374151; line-height: 1.6;">${escapeHtml(comment.content)}</p>
                        
                        <div class="comment-votes" style="display: flex; gap: 1rem; align-items: center; margin-bottom: ${repliesHtml ? '1rem' : '0'};">
                            <button class="vote-btn upvote ${comment.user_vote === 1 ? 'active' : ''}" onclick="voteComment(${comment.id}, 1)" style="background: none; border: none; color: #6b7280; cursor: pointer; display: flex; align-items: center; gap: 0.25rem;">
                                <i class="fas fa-thumbs-up"></i>
                                <span>${comment.upvotes || 0}</span>
                            </button>
                            <button class="vote-btn downvote ${comment.user_vote === -1 ? 'active' : ''}" onclick="voteComment(${comment.id}, -1)" style="background: none; border: none; color: #6b7280; cursor: pointer; display: flex; align-items: center; gap: 0.25rem;">
                                <i class="fas fa-thumbs-down"></i>
                                <span>${comment.downvotes || 0}</span>
                            </button>
                            ${USER_ROLE && USER_ROLE !== 'guest' ? `
                                <button onclick="toggleReplyForm(${comment.id})" style="background: none; border: none; color: var(--primary-color); cursor: pointer; display: flex; align-items: center; gap: 0.25rem; margin-left: auto;">
                                    <i class="fas fa-reply"></i>
                                    <span>Phản hồi</span>
                                </button>
                            ` : ''}
                        </div>

                        ${repliesHtml}

                        <div id="reply-form-${comment.id}" style="margin-top: 1rem; display: none; padding: 1rem; background: #f9fafb; border-radius: 0.5rem; width: 100%; box-sizing: border-box;">
                            <textarea id="reply-content-${comment.id}" placeholder="Nhập phản hồi của bạn..." style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-family: inherit; resize: vertical; min-height: 80px; box-sizing: border-box;" maxlength="5000"></textarea>
                            <div style="margin-top: 0.5rem; font-size: 0.85rem; color: #6b7280;">
                                <span id="reply-char-count-${comment.id}">0</span>/5000
                            </div>
                            <div style="margin-top: 0.75rem; display: flex; gap: 0.5rem;">
                                <button onclick="submitReply(${comment.id}); return false;" style="flex: 1; padding: 0.5rem 1rem; background: var(--primary-color); color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                                    Gửi phản hồi
                                </button>
                                <button onclick="toggleReplyForm(${comment.id}); return false;" style="flex: 1; padding: 0.5rem 1rem; background: white; color: #6b7280; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                                    Hủy
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            // Helper function to setup character counters for nested replies recursively
            function setupCharCounters(replies) {
                if (!replies || replies.length === 0) return;
                
                replies.forEach(reply => {
                    const textarea = document.getElementById(`reply-content-${reply.id}`);
                    if (textarea) {
                        textarea.addEventListener('input', (e) => {
                            document.getElementById(`reply-char-count-${reply.id}`).textContent = e.target.value.length;
                        });
                    }
                    // Recursively setup counters for nested replies
                    setupCharCounters(reply.replies);
                });
            }
            
            // Setup character counters for reply forms
            comments.forEach(comment => {
                const textarea = document.getElementById(`reply-content-${comment.id}`);
                if (textarea) {
                    textarea.addEventListener('input', (e) => {
                        document.getElementById(`reply-char-count-${comment.id}`).textContent = e.target.value.length;
                    });
                }
                
                // Setup character counters for nested replies
                setupCharCounters(comment.replies);
            });
        }

        // Setup comment form
        function setupCommentForm() {
            const form = document.getElementById('commentForm');
            if (!form) return;

            // Star rating interaction
            const ratingDiv = document.getElementById('commentRating');
            const ratingInput = document.getElementById('ratingInput');
            
            if (ratingDiv) {
                ratingDiv.querySelectorAll('i').forEach(star => {
                    star.addEventListener('click', (e) => {
                        const value = parseInt(e.target.dataset.value);
                        ratingInput.value = value;
                        updateRatingStars(value, 'commentRating');
                    });

                    star.addEventListener('mouseover', (e) => {
                        const value = parseInt(e.target.dataset.value);
                        highlightStars(value, 'commentRating');
                    });
                });

                ratingDiv.addEventListener('mouseout', () => {
                    const currentValue = parseInt(ratingInput.value);
                    updateRatingStars(currentValue, 'commentRating');
                });
            }

            // Character counter
            const textarea = document.getElementById('commentContent');
            if (textarea) {
                textarea.addEventListener('input', (e) => {
                    document.getElementById('charCount').textContent = `${e.target.value.length}/5000`;
                });
            }

            // Form submission
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                submitComment();
            });
        }

        // Submit comment
        function submitComment() {
            const content = document.getElementById('commentContent').value.trim();
            const rating = parseInt(document.getElementById('ratingInput').value);

            if (!content) {
                alert('Vui lòng nhập bình luận');
                return;
            }

            const submitBtn = document.getElementById('submitCommentBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang gửi...';

            fetch('../../api/comments.php?action=addComment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    post_id: POST_ID,
                    content: content,
                    rating: rating > 0 ? rating : 0
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('commentForm').reset();
                    document.getElementById('ratingInput').value = 0;
                    document.getElementById('charCount').textContent = '0/5000';
                    updateRatingStars(0, 'commentRating');
                    loadComments();
                    alert('Bình luận đã được gửi!');
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể gửi bình luận'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Lỗi kết nối');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Gửi bình luận';
            });
        }

        // Vote on comment
        function voteComment(commentId, voteValue) {
            <?php if (!isLoggedIn()): ?>
            alert('Vui lòng đăng nhập');
            window.location.href = '../auth/login.php';
            return;
            <?php endif; ?>

            fetch('../../api/comments.php?action=voteComment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    comment_id: commentId,
                    vote: voteValue
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    loadComments();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể bình chọn'));
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Delete comment
        function deleteComment(commentId) {
            if (!confirm('Bạn chắc chắn muốn xóa bình luận này?')) return;

            fetch('../../api/comments.php?action=deleteComment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: commentId })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    loadComments();
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể xóa bình luận'));
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Toggle reply form visibility
        function toggleReplyForm(commentId) {
            console.log('Toggling reply form for ID:', commentId);
            const form = document.getElementById(`reply-form-${commentId}`);
            console.log('Form found:', form !== null);
            if (form) {
                form.style.display = form.style.display === 'none' ? 'block' : 'none';
                if (form.style.display === 'block') {
                    const textarea = document.getElementById(`reply-content-${commentId}`);
                    if (textarea) {
                        textarea.focus();
                    }
                }
            } else {
                console.error(`Form #reply-form-${commentId} not found`);
            }
        }

        // Submit reply
        function submitReply(parentId) {
            console.log('submitReply called with parentId:', parentId);
            const content = document.getElementById(`reply-content-${parentId}`).value.trim();
            console.log('Content:', content);

            if (!content) {
                alert('Vui lòng nhập phản hồi');
                return false;
            }

            <?php if (!isLoggedIn()): ?>
            alert('Vui lòng đăng nhập');
            window.location.href = '../auth/login.php';
            return false;
            <?php endif; ?>

            // Find all submit buttons in this reply form and disable them
            const form = document.getElementById(`reply-form-${parentId}`);
            console.log('Form found for parentId', parentId, ':', form !== null);
            
            if (!form) {
                console.error(`Form #reply-form-${parentId} not found`);
                alert('Form not found');
                return false;
            }
            
            const buttons = form.querySelectorAll('button');
            const submitBtn = buttons[0]; // First button is submit
            const originalText = submitBtn.textContent;
            
            // Disable both buttons
            buttons.forEach(btn => btn.disabled = true);
            submitBtn.textContent = 'Đang gửi...';

            console.log('Sending reply with post_id:', POST_ID, 'parent_id:', parentId);

            fetch('../../api/comments.php?action=addReply', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    post_id: POST_ID,
                    parent_id: parentId,
                    content: content
                })
            })
            .then(r => r.json())
            .then(data => {
                console.log('Response:', data);
                if (data.success) {
                    document.getElementById(`reply-content-${parentId}`).value = '';
                    if (document.getElementById(`reply-char-count-${parentId}`)) {
                        document.getElementById(`reply-char-count-${parentId}`).textContent = '0';
                    }
                    toggleReplyForm(parentId);
                    loadComments();
                    alert('Phản hồi đã được gửi!');
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể gửi phản hồi'));
                    // Re-enable buttons on error
                    buttons.forEach(btn => btn.disabled = false);
                    submitBtn.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Lỗi kết nối');
                // Re-enable buttons on error
                buttons.forEach(btn => btn.disabled = false);
                submitBtn.textContent = originalText;
            });
            
            return false;
        }

        // Helper functions
        function updateRatingStars(rating, elementId) {
            const container = document.getElementById(elementId);
            if (container) {
                container.querySelectorAll('i').forEach((star, index) => {
                    if (index < rating) {
                        star.classList.remove('far');
                        star.classList.add('fas');
                    } else {
                        star.classList.remove('fas');
                        star.classList.add('far');
                    }
                });
            }
        }

        function highlightStars(rating, elementId) {
            const container = document.getElementById(elementId);
            if (container) {
                container.querySelectorAll('i').forEach((star, index) => {
                    if (index < rating) {
                        star.style.color = '#fbbf24';
                    } else {
                        star.style.color = 'inherit';
                    }
                });
            }
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) return 'vừa xong';
            if (diff < 3600000) return Math.floor(diff / 60000) + ' phút trước';
            if (diff < 86400000) return Math.floor(diff / 3600000) + ' giờ trước';
            if (diff < 2592000000) return Math.floor(diff / 86400000) + ' ngày trước';
            
            return date.toLocaleDateString('vi-VN');
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        function isCurrentUserComment(userId) {
            <?php if (isLoggedIn()): ?>
            return userId == <?php echo $_SESSION['user_id']; ?>;
            <?php endif; ?>
            return false;
        }

        // Original functions
        function changeMainImage(thumbnail) {
            document.getElementById('mainImage').src = thumbnail.src.replace('300x200', '1200x600');
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            thumbnail.classList.add('active');
        }

        function toggleFavorite(postId, button) {
            <?php if (!isLoggedIn()): ?>
            alert('Vui lòng đăng nhập để yêu thích');
            window.location.href = '../auth/login.php';
            return;
            <?php endif; ?>
            
            const isFavorited = button.classList.contains('active');
            const action = isFavorited ? 'remove' : 'add';
            
            fetch('../../Controllers/FavoriteController.php', {
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
                    
                    // Button always stays red (btn-danger)
                    // No class change needed
                } else {
                    alert(data.message || 'Lỗi khi thay đổi yêu thích');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Toggle like
        function toggleLike(postId) {
            <?php if (!isLoggedIn()): ?>
            alert('Vui lòng đăng nhập để thích bài viết');
            window.location.href = '../auth/login.php';
            return;
            <?php endif; ?>

            fetch('../../Controllers/LikeController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=toggleLike&post_id=' + postId
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('likesCount').textContent = data.likes_count;
                } else {
                    alert(data.message || 'Lỗi khi cập nhật lượt thích');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Handle anchor navigation to comments
        function scrollToAnchor() {
            const hash = window.location.hash;
            if (hash) {
                const elementId = hash.substring(1); // Remove # from hash
                const element = document.getElementById(elementId);
                if (element) {
                    // Small delay to ensure DOM is fully rendered
                    setTimeout(() => {
                        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        // Highlight the comment temporarily
                        element.style.backgroundColor = '#fef3c7';
                        setTimeout(() => {
                            element.style.backgroundColor = 'white';
                        }, 2000);
                    }, 100);
                }
            }
        }
    </script>
    <script src="../../assets/js/notifications.js"></script>
</body>
</html>
