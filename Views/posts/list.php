<?php
require_once __DIR__ . '/../../config.php';

$search = sanitize($_GET['search'] ?? '');
$district = sanitize($_GET['district'] ?? '');
$price_range = sanitize($_GET['price_range'] ?? '');
$category = sanitize($_GET['category'] ?? '');
$posts = [];

try {
    $query = "SELECT * FROM posts WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $query .= " AND (title LIKE ? OR location LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
    }
    if (!empty($district)) {
        $query .= " AND location LIKE ?";
        $params[] = "%$district%";
    }
    if (!empty($price_range)) {
        [$min, $max] = explode('-', $price_range);
        $query .= " AND price BETWEEN ? AND ?";
        $params[] = (int)$min;
        $params[] = (int)$max;
    }
    if (!empty($category)) {
        $query .= " AND category = ?";
        $params[] = (int)$category;
    }
    
    $query .= " ORDER BY created_at DESC";
    $stmt = getDB()->prepare($query);
    $stmt->execute($params);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Query error: " . $e->getMessage());
    $posts = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách phòng trọ - Tìm Trọ Sinh Viên</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            color: var(--primary-color);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 3rem;
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
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination button.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
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
                <i class="fas fa-home"></i>
                <span>Tìm Trọ SV</span>
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
                        <div class="filter-title">Khu vực</div>
                        <select class="form-control" id="districtFilter">
                            <option value="">Tất cả quận/huyện</option>
                            <option value="Quận 1">Quận 1</option>
                            <option value="Quận 3">Quận 3</option>
                            <option value="Quận 5">Quận 5</option>
                            <option value="Quận 10">Quận 10</option>
                            <option value="Quận Bình Thạnh">Quận Bình Thạnh</option>
                            <option value="Quận Thủ Đức">Quận Thủ Đức</option>
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
                            <strong>402 phòng trọ</strong> phù hợp
                        </div>
                        <div class="sort-options">
                            <span>Sắp xếp:</span>
                            <select class="form-control" style="width: auto;">
                                <option>Mới nhất</option>
                                <option>Giá thấp đến cao</option>
                                <option>Giá cao đến thấp</option>
                                <option>Diện tích lớn nhất</option>
                            </select>
                        </div>
                    </div>

                    <div class="posts-grid">
                        <div class="post-card">
                            <div class="post-image">
                                <img src="https://via.placeholder.com/400x250/667eea/ffffff?text=Phong+Tro+1" alt="Phòng trọ 1">
                                <span class="post-badge">Mới đăng</span>
                                <button class="favorite-btn" onclick="toggleFavorite(1, this)">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                            <div class="post-content">
                                <h3 class="post-title">Phòng trọ gần ĐH Bách Khoa - An ninh tốt</h3>
                                <div class="post-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>123 Lý Thường Kiệt, Quận 10</span>
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
                                    <div class="post-price">2.5tr/tháng</div>
                                    <a href="detail.php?id=1" class="btn btn-primary btn-sm">Chi tiết</a>
                                </div>
                            </div>
                        </div>

                        <div class="post-card">
                            <div class="post-image">
                                <img src="https://via.placeholder.com/400x250/764ba2/ffffff?text=Can+Ho+Mini" alt="Căn hộ mini">
                                <span class="post-badge">Nổi bật</span>
                                <button class="favorite-btn" onclick="toggleFavorite(2, this)">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                            <div class="post-content">
                                <h3 class="post-title">Căn hộ mini cao cấp Quận 1 - Full nội thất</h3>
                                <div class="post-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>456 Nguyễn Huệ, Quận 1</span>
                                </div>
                                <div class="post-features">
                                    <div class="feature-item">
                                        <i class="fas fa-expand"></i>
                                        <span>35m²</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-users"></i>
                                        <span>2 người</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-snowflake"></i>
                                        <span>Điều hòa</span>
                                    </div>
                                </div>
                                <div class="post-footer">
                                    <div class="post-price">8tr/tháng</div>
                                    <a href="detail.php?id=2" class="btn btn-primary btn-sm">Chi tiết</a>
                                </div>
                            </div>
                        </div>

                        <div class="post-card">
                            <div class="post-image">
                                <img src="https://via.placeholder.com/400x250/3b82f6/ffffff?text=Phong+SV" alt="Phòng SV">
                                <span class="post-badge">Giá rẻ</span>
                                <button class="favorite-btn" onclick="toggleFavorite(3, this)">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                            <div class="post-content">
                                <h3 class="post-title">Phòng trọ sinh viên giá rẻ - Gần chợ trường</h3>
                                <div class="post-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>789 Lê Văn Việt, Quận 9</span>
                                </div>
                                <div class="post-features">
                                    <div class="feature-item">
                                        <i class="fas fa-expand"></i>
                                        <span>18m²</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-user"></i>
                                        <span>1 người</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-wifi"></i>
                                        <span>WiFi</span>
                                    </div>
                                </div>
                                <div class="post-footer">
                                    <div class="post-price">1.8tr/tháng</div>
                                    <a href="detail.php?id=3" class="btn btn-primary btn-sm">Chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pagination">
                        <button><i class="fas fa-chevron-left"></i></button>
                        <button class="active">1</button>
                        <button>2</button>
                        <button>3</button>
                        <button>4</button>
                        <button>5</button>
                        <button><i class="fas fa-chevron-right"></i></button>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
