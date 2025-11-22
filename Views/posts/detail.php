<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../helpers.php';
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
                    <a href="../user/profile.php" class="btn btn-outline btn-sm"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                    <a href="../../Controllers/AuthController.php?action=logout" class="btn btn-danger btn-sm">Đăng xuất</a>
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
                        <img src="<?php echo getPlaceholderImage(1200, 600, '667eea', 'Main Image'); ?>" alt="Phòng trọ" class="main-image" id="mainImage">
                        <div class="thumbnail-grid">
                            <img src="<?php echo getPlaceholderImage(300, 200, '667eea', '1'); ?>" alt="Thumbnail 1" class="thumbnail active" onclick="changeMainImage(this)">
                            <img src="<?php echo getPlaceholderImage(300, 200, '764ba2', '2'); ?>" alt="Thumbnail 2" class="thumbnail" onclick="changeMainImage(this)">
                            <img src="<?php echo getPlaceholderImage(300, 200, '3b82f6', '3'); ?>" alt="Thumbnail 3" class="thumbnail" onclick="changeMainImage(this)">
                            <img src="<?php echo getPlaceholderImage(300, 200, '8b5cf6', '4'); ?>" alt="Thumbnail 4" class="thumbnail" onclick="changeMainImage(this)">
                        </div>
                    </div>

                    <div class="detail-content">
                        <div class="detail-header">
                            <h1 class="detail-title">Phòng trọ gần ĐH Bách Khoa - An ninh tốt</h1>
                            <div class="detail-meta">
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>123 Lý Thường Kiệt, Quận 10, TP.HCM</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Đăng 2 ngày trước</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-eye"></i>
                                    <span>156 lượt xem</span>
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
                                        <p>20m²</p>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-users feature-icon"></i>
                                    <div>
                                        <strong>Số người tối đa</strong>
                                        <p>2 người</p>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-home feature-icon"></i>
                                    <div>
                                        <strong>Loại phòng</strong>
                                        <p>Phòng đơn</p>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-venus-mars feature-icon"></i>
                                    <div>
                                        <strong>Giới tính</strong>
                                        <p>Nam/Nữ</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="detail-section">
                            <h2 class="section-title">Mô tả</h2>
                            <p>Phòng trọ sạch sẽ, thoáng mát, an ninh tốt. Gần trường Đại học Bách Khoa, thuận tiện đi lại. Khu vực có đầy đủ tiện ích: siêu thị, chợ, quán ăn, tiệm giặt là.</p>
                            <p>Phòng mới sơn sửa, nội thất cơ bản bao gồm: giường, tủ quần áo, bàn học. Có ban công thoáng mát.</p>
                        </div>

                        <div class="detail-section">
                            <h2 class="section-title">Tiện ích</h2>
                            <div class="amenities-list">
                                <div class="amenity-tag">
                                    <i class="fas fa-wifi" style="color: var(--primary-color);"></i>
                                    <span>WiFi</span>
                                </div>
                                <div class="amenity-tag">
                                    <i class="fas fa-snowflake" style="color: var(--primary-color);"></i>
                                    <span>Điều hòa</span>
                                </div>
                                <div class="amenity-tag">
                                    <i class="fas fa-lightbulb" style="color: var(--primary-color);"></i>
                                    <span>Tủ lạnh</span>
                                </div>
                                <div class="amenity-tag">
                                    <i class="fas fa-shower" style="color: var(--primary-color);"></i>
                                    <span>Nóng lạnh</span>
                                </div>
                                <div class="amenity-tag">
                                    <i class="fas fa-parking" style="color: var(--primary-color);"></i>
                                    <span>Chỗ để xe</span>
                                </div>
                                <div class="amenity-tag">
                                    <i class="fas fa-shield-alt" style="color: var(--primary-color);"></i>
                                    <span>An ninh 24/7</span>
                                </div>
                            </div>
                        </div>

                        <div class="detail-section">
                            <h2 class="section-title">Nội quy</h2>
                            <ul style="padding-left: 1.5rem; color: var(--text-secondary);">
                                <li>Không sử dụng chất cấm, hút thuốc trong phòng</li>
                                <li>Giữ gìn vệ sinh chung</li>
                                <li>Không gây ồn ào sau 22h</li>
                                <li>Đóng tiền phòng đúng hạn mỗi tháng</li>
                                <li>Báo trước 1 tháng khi muốn chuyển đi</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <aside class="detail-sidebar">
                    <div class="price-card">
                        <div class="price-amount">2.5 triệu</div>
                        <div class="price-unit">VNĐ/tháng</div>
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;">Tiền cọc: 2.5 triệu</p>
                            <p style="color: var(--text-secondary); font-size: 0.875rem;">Điện: 3,500đ/kWh | Nước: 20,000đ/người</p>
                        </div>
                    </div>

                    <div class="contact-card">
                        <h3 style="margin-bottom: 1.5rem;">Liên hệ</h3>
                        <div class="landlord-info">
                            <img src="<?php echo getPlaceholderImage(60, 60, '667eea', 'A'); ?>" alt="Chủ trọ" class="landlord-avatar">
                            <div class="landlord-details">
                                <h4>Nguyễn Văn A</h4>
                                <p>Chủ trọ</p>
                            </div>
                        </div>
                        <div class="contact-actions">
                            <a href="tel:0912345678" class="btn btn-primary">
                                <i class="fas fa-phone"></i> 0912 345 678
                            </a>
                            <a href="../chat/chat.php" class="btn btn-outline">
                                <i class="fas fa-comment"></i> Nhắn tin
                            </a>
                            <button class="btn btn-secondary" onclick="toggleFavorite(1, this)">
                                <i class="far fa-heart"></i> Yêu thích
                            </button>
                        </div>
                    </div>

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
    </script>
</body>
</html>
