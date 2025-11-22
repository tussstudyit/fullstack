<?php
require_once __DIR__ . '/../../config.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('../../Views/auth/login.php');
}

require_once __DIR__ . '/../../Models/User.php';

$user = null;
try {
    $query = "SELECT id, username, email, phone, full_name, role, created_at FROM users WHERE id = ?";
    $stmt = getDB()->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Query error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ người dùng - Tìm Trọ Sinh Viên</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html {
            height: 100%;
        }

        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .header {
            position: sticky !important;
            top: 0 !important;
            z-index: 1000 !important;
            flex-shrink: 0;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            text-align: center;
            flex-shrink: 0;
        }

        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .main-content {
            padding: 3rem 0;
            flex: 1;
        }

        .profile-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .profile-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .profile-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .profile-section h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1.5rem;
        }

        .profile-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            flex-wrap: wrap;
        }

        .profile-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 0.95rem;
        }

        .profile-value {
            color: #1f2937;
            font-size: 1rem;
            font-weight: 500;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn-group .btn {
            flex: 1;
            min-width: 150px;
        }

        .role-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .role-badge.landlord {
            background: #e3f2fd;
            color: #1976d2;
        }

        .role-badge.tenant {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .role-badge.admin {
            background: #fce4ec;
            color: #c2185b;
        }

        .alert {
            padding: 2rem;
            text-align: center;
            border-radius: 12px;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert h2 {
            margin-bottom: 1rem;
        }

        .footer {
            margin-top: auto;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.75rem;
            }

            .profile-container {
                padding: 1.5rem;
            }

            .profile-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn-group .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header / Navigation -->
    <header class="header">
        <nav class="navbar">
            <a href="../../index.php" class="logo">
                <i class="fas fa-home"></i>
                <span>Tìm Trọ SV</span>
            </a>

            <ul class="nav-menu">
                <li><a href="../../index.php" class="nav-link">Trang chủ</a></li>
                <li><a href="../posts/list.php" class="nav-link">Danh sách trọ</a></li>
                <?php if (isLoggedIn() && $_SESSION['role'] === 'landlord'): ?>
                    <li><a href="../posts/create.php" class="nav-link">Đăng tin</a></li>
                <?php endif; ?>
                <?php if (isLoggedIn()): ?>
                    <li><a href="favorites.php" class="nav-link">Yêu thích</a></li>
                    <li><a href="../chat/chat.php" class="nav-link">Tin nhắn</a></li>
                <?php endif; ?>
            </ul>

            <div class="nav-actions">
                <?php if (isLoggedIn()): ?>
                    <a href="notifications.php" class="btn btn-outline btn-sm" title="Thông báo">
                        <i class="fas fa-bell"></i> Thông báo
                    </a>
                    <a href="#" class="btn btn-outline btn-sm"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
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

    <!-- Page Header -->
    <section class="page-header">
        <h1>Hồ sơ cá nhân</h1>
        <p>Quản lý thông tin tài khoản</p>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
                <?php if ($user): ?>
                    <div class="profile-container">
                        <!-- User Info Section -->
                        <div class="profile-section">
                            <h2>Thông tin cơ bản</h2>
                            
                            <div class="profile-item">
                                <span class="profile-label">Tên đầy đủ:</span>
                                <span class="profile-value"><?php echo htmlspecialchars($user['full_name'] ?? 'N/A'); ?></span>
                            </div>

                            <div class="profile-item">
                                <span class="profile-label">Tên đăng nhập:</span>
                                <span class="profile-value">@<?php echo htmlspecialchars($user['username']); ?></span>
                            </div>

                            <div class="profile-item">
                                <span class="profile-label">Email:</span>
                                <span class="profile-value"><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>

                            <div class="profile-item">
                                <span class="profile-label">Số điện thoại:</span>
                                <span class="profile-value"><?php echo htmlspecialchars($user['phone'] ?? 'Chưa cập nhật'); ?></span>
                            </div>

                            <div class="profile-item">
                                <span class="profile-label">Loại tài khoản:</span>
                                <span class="role-badge <?php echo strtolower($user['role']); ?>">
                                    <?php 
                                    $role_text = [
                                        'tenant' => 'Người thuê',
                                        'landlord' => 'Chủ trọ',
                                        'admin' => 'Quản trị viên'
                                    ];
                                    echo $role_text[$user['role']] ?? $user['role'];
                                    ?>
                                </span>
                            </div>

                            <div class="profile-item">
                                <span class="profile-label">Tham gia lúc:</span>
                                <span class="profile-value"><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></span>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="profile-section">
                            <h2>Hành động nhanh</h2>
                            
                            <div class="btn-group">
                                <?php if ($_SESSION['role'] === 'landlord'): ?>
                                    <a href="../user/my-posts.php" class="btn btn-primary">
                                        <i class="fas fa-list"></i> Bài đăng của tôi
                                    </a>
                                <?php endif; ?>
                                <a href="favorites.php" class="btn btn-primary">
                                    <i class="fas fa-heart"></i> Yêu thích
                                </a>
                                <a href="notifications.php" class="btn btn-outline">
                                    <i class="fas fa-bell"></i> Thông báo
                                </a>
                            </div>

                            <div class="btn-group">
                                <a href="../../index.php" class="btn btn-outline">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                                <a href="../../Controllers/AuthController.php?action=logout" class="btn btn-danger">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert">
                        <h2>Không tìm thấy thông tin người dùng</h2>
                        <p><a href="../../index.php" class="btn btn-primary">Quay lại trang chủ</a></p>
                    </div>
                <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Giới thiệu</h4>
                    <ul>
                        <li><a href="#">Về chúng tôi</a></li>
                        <li><a href="#">Liên hệ</a></li>
                        <li><a href="#">Điều khoản</a></li>
                        <li><a href="#">Chính sách</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Hướng dẫn</h4>
                    <ul>
                        <li><a href="#">Hướng dẫn đăng tin</a></li>
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                        <li><a href="#">Báo cáo sai phạm</a></li>
                        <li><a href="#">Góp ý</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Kết nối với chúng tôi</h4>
                    <ul>
                        <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
                        <li><a href="#"><i class="fab fa-youtube"></i> YouTube</a></li>
                        <li><a href="#"><i class="fab fa-tiktok"></i> TikTok</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Tìm Trọ Sinh Viên. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
