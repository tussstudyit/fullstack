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
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết phòng trọ - Tìm Trọ Sinh Viên</title>
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
                <i class="fas fa-home"></i>
                <span>Tìm Trọ SV</span>
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
                    <div style="position: relative; display: inline-block;">
                        <a href="../user/notifications.php" class="btn btn-outline btn-sm" title="Thông báo">
                            <i class="fas fa-bell"></i> Thông báo
                        </a>
                        <?php 
                        require_once '../../Models/Notification.php';
                        $notifModel = new Notification();
                        $unread = $notifModel->getUnreadCount($_SESSION['user_id']);
                        if ($unread > 0): 
                        ?>
                        <span style="position: absolute; top: -5px; right: -5px; background: var(--danger-color); color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">
                            <?php echo $unread > 99 ? '99+' : $unread; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <a href="../user/profile.php" class="btn btn-outline btn-sm"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></a>
                    <a href="../../Controllers/AuthController.php?action=logout" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                <?php else: ?>
                    <a href="../auth/login.php" class="btn btn-outline btn-sm">Đăng nhập</a>
                    <a href="../auth/register.php" class="btn btn-primary btn-sm">Đăng ký</a>
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
                                    <i class="fas fa-home feature-icon"></i>
                                    <div>
                                        <strong>Loại phòng</strong>
                                        <p><?php 
                                            $roomTypes = ['single' => 'Phòng đơn', 'shared' => 'Phòng ghép', 'apartment' => 'Căn hộ', 'house' => 'Nhà nguyên căn'];
                                            echo $roomTypes[$post['room_type']] ?? 'Phòng đơn';
                                        ?></p>
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
                                    'flexible_hours' => 'fa-clock'
                                ];
                                $amenityLabels = [
                                    'wifi' => 'WiFi',
                                    'ac' => 'Điều hòa',
                                    'fridge' => 'Tủ lạnh',
                                    'washing' => 'Máy giặt',
                                    'parking' => 'Chỗ để xe',
                                    'security' => 'An ninh 24/7',
                                    'water_heater' => 'Máy nóng lạnh',
                                    'flexible_hours' => 'Giờ giấc tự do'
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
                            <img src="<?php echo getPlaceholderImage(60, 60, '667eea', substr($landlord['username'], 0, 1)); ?>" alt="Chủ trọ" class="landlord-avatar">
                            <div class="landlord-details">
                                <h4><?php echo htmlspecialchars($landlord['username'] ?? 'Chủ trọ'); ?></h4>
                                <p>Chủ trọ</p>
                            </div>
                        </div>
                        <div class="contact-actions">
                            <a href="tel:<?php echo htmlspecialchars($landlord['phone'] ?? ''); ?>" class="btn btn-primary">
                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($landlord['phone'] ?? 'Không có'); ?>
                            </a>
                            <a href="../chat/chat.php?user_id=<?php echo $post['user_id']; ?>" class="btn btn-outline">
                                <i class="fas fa-comment"></i> Nhắn tin
                            </a>
                            <?php 
                            $fav_icon_class = $is_favorited ? 'fas' : 'far';
                            $fav_btn_class = $is_favorited ? 'btn-danger' : 'btn-secondary';
                            ?>
                            <button class="btn <?php echo $fav_btn_class; ?> <?php echo $is_favorited ? 'active' : ''; ?>" onclick="toggleFavorite(<?php echo $post_id; ?>, this)" id="favBtn">
                                <i class="<?php echo $fav_icon_class; ?> fa-heart"></i> Yêu thích
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
                        <h2 class="section-title" style="margin-bottom: 0;">Đánh giá</h2>
                    </div>
                    <div class="rating-summary">
                        <div class="rating-score">4.5</div>
                        <div>
                            <div class="rating-stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <p style="color: var(--text-secondary); margin-top: 0.25rem;">12 đánh giá</p>
                        </div>
                    </div>
                </div>

                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <img src="<?php echo getPlaceholderImage(48, 48, '3b82f6', 'B'); ?>" alt="Reviewer" class="reviewer-avatar">
                            <div>
                                <h4>Trần Văn B</h4>
                                <div style="color: #fbbf24; margin: 0.25rem 0;">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <p style="color: var(--text-secondary); font-size: 0.875rem;">3 ngày trước</p>
                            </div>
                        </div>
                    </div>
                    <p style="color: var(--text-secondary);">Phòng rất đẹp, chủ trọ thân thiện. Vị trí thuận tiện đi lại, gần trường. Rất hài lòng!</p>
                </div>

                <div class="review-form">
                    <h3 style="margin-bottom: 1rem;">Viết đánh giá</h3>
                    <form>
                        <div class="form-group">
                            <label class="form-label">Đánh giá của bạn</label>
                            <div class="star-rating">
                                <i class="far fa-star" onclick="setRating(1, this.parentElement)"></i>
                                <i class="far fa-star" onclick="setRating(2, this.parentElement)"></i>
                                <i class="far fa-star" onclick="setRating(3, this.parentElement)"></i>
                                <i class="far fa-star" onclick="setRating(4, this.parentElement)"></i>
                                <i class="far fa-star" onclick="setRating(5, this.parentElement)"></i>
                                <input type="hidden" name="rating" value="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nhận xét</label>
                            <textarea class="form-control" rows="4" placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
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
                    
                    // Update button class
                    if (button.classList.contains('active')) {
                        button.classList.remove('btn-secondary');
                        button.classList.add('btn-danger');
                    } else {
                        button.classList.remove('btn-danger');
                        button.classList.add('btn-secondary');
                    }
                } else {
                    alert(data.message || 'Lỗi khi thay đổi yêu thích');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
