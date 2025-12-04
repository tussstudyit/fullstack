<?php
require_once __DIR__ . '/../../config.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('../../Views/auth/login.php');
}

require_once __DIR__ . '/../../Models/User.php';
require_once __DIR__ . '/../../Models/Notification.php';

$user = null;
$message = '';
$error = '';

try {
    $query = "SELECT id, username, email, phone, full_name, avatar, bio, role, created_at FROM users WHERE id = ?";
    $stmt = getDB()->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Query error: " . $e->getMessage());
}

// Handle avatar upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileSize = $file['size'];
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if ($fileSize > AVATAR_MAX_SIZE) {
            $error = 'File quá lớn, tối đa ' . (AVATAR_MAX_SIZE / 1024 / 1024) . 'MB';
        } elseif (!in_array($fileExt, ALLOWED_EXTENSIONS)) {
            $error = 'Định dạng file không được hỗ trợ';
        } else {
            $uploadDir = AVATAR_UPLOAD_DIR;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $fileExt;
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Delete old avatar if exists
                if (!empty($user['avatar']) && file_exists(AVATAR_UPLOAD_DIR . $user['avatar'])) {
                    @unlink(AVATAR_UPLOAD_DIR . $user['avatar']);
                }
                
                // Update database
                $updateQuery = "UPDATE users SET avatar = ? WHERE id = ?";
                $updateStmt = getDB()->prepare($updateQuery);
                if ($updateStmt->execute([$fileName, $_SESSION['user_id']])) {
                    $user['avatar'] = $fileName;
                    $message = 'Cập nhật ảnh đại diện thành công!';
                } else {
                    $error = 'Lỗi cập nhật database';
                    @unlink($uploadPath);
                }
            } else {
                $error = 'Lỗi tải file lên';
            }
        }
    } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
        $error = 'Lỗi upload: ' . $file['error'];
    }
}

// Handle bio update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bio'])) {
    $bio = trim($_POST['bio'] ?? '');
    $updateQuery = "UPDATE users SET bio = ? WHERE id = ?";
    $updateStmt = getDB()->prepare($updateQuery);
    if ($updateStmt->execute([$bio, $_SESSION['user_id']])) {
        $user['bio'] = $bio;
        $message = 'Cập nhật tiểu sử thành công!';
    } else {
        $error = 'Lỗi cập nhật database';
    }
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
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            text-align: center;
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

        .content-wrapper {
            padding: 3rem 0;
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
            gap: 1rem;
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

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border-color: #bbf7d0;
            text-align: left;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-color: #fecaca;
            text-align: left;
        }

        .alert h2 {
            margin-bottom: 1rem;
        }

        .footer {
            background: var(--dark-color);
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 3rem;
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
            .footer-grid {
                grid-template-columns: 1fr;
            }
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

        /* Avatar Upload Modal */
        .avatar-wrapper {
            position: relative;
            display: inline-block;
            width: 120px;
            height: 120px;
        }

        .avatar-image {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #667eea;
            display: block;
        }

        .avatar-edit-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 35px;
            height: 35px;
            background: #667eea;
            color: white;
            border: 3px solid white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .avatar-edit-btn:hover {
            background: #764ba2;
            transform: scale(1.1);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            width: 90%;
            max-width: 400px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 1rem;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.3rem;
            color: #1f2937;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
            transition: color 0.3s ease;
        }

        .modal-close:hover {
            color: #1f2937;
        }

        .modal-body {
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #1f2937;
            font-size: 0.9rem;
        }

        .form-group input[type="file"] {
            display: block;
            width: 100%;
            padding: 0.75rem;
            border: 2px dashed #667eea;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .form-group input[type="file"]:hover {
            background-color: #f3f4f6;
        }

        .file-hint {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: #6b7280;
        }

        .modal-preview {
            text-align: center;
            padding: 1rem 0;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: #f9fafb;
            margin: 1rem 0;
        }

        .modal-preview img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 50%;
            object-fit: cover;
            display: inline-block;
        }

        .modal-preview.empty {
            color: #9ca3af;
        }

        .modal-footer {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .modal-footer .btn {
            flex: 1;
            margin: 0;
        }

        .modal-footer .btn-secondary {
            background: #e5e7eb;
            color: #1f2937;
        }

        .modal-footer .btn-secondary:hover {
            background: #d1d5db;
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
                <?php if (isLoggedIn() && $_SESSION['role'] === 'tenant'): ?>
                <li><a href="favorites.php" class="nav-link">Yêu thích</a></li>
                <?php endif; ?>
                <?php if (isLoggedIn()): ?>
                <li><a href="../chat/chat.php" class="nav-link">Tin nhắn</a></li>
                <?php endif; ?>
            </ul>

            <div class="nav-actions">
                <?php if (isLoggedIn()): ?>
                    <div style="position: relative; display: inline-block;">
                        <a href="notifications.php" class="btn btn-outline btn-sm" title="Thông báo">
                            <i class="fas fa-bell"></i> Thông báo
                        </a>
                        <?php 
                        $notifModel = new Notification();
                        $unread = $notifModel->getUnreadCount($_SESSION['user_id']);
                        if ($unread > 0): 
                        ?>
                        <span style="position: absolute; top: -5px; right: -5px; background: var(--danger-color); color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">
                            <?php echo $unread > 99 ? '99+' : $unread; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <a href="profile.php" class="btn btn-outline btn-sm"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></a>
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

    <!-- Page Header -->
    <section class="page-header">
        <h1>Hồ sơ cá nhân</h1>
        <p>Quản lý thông tin tài khoản</p>
    </section>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="container">
            <?php if ($user): ?>
                <div class="profile-container">
                    <?php if ($message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Avatar Section -->
                    <div class="profile-section">
                        <h2>Ảnh đại diện</h2>
                        <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                            <div class="avatar-wrapper">
                                <img id="avatarImage" 
                                     src="<?php echo !empty($user['avatar']) ? '../../uploads/avatars/' . htmlspecialchars($user['avatar']) : 'https://via.placeholder.com/120?text=' . substr($user['username'], 0, 1); ?>" 
                                     alt="Avatar" 
                                     class="avatar-image">
                                <button type="button" class="avatar-edit-btn" onclick="openAvatarModal()">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            <div>
                                <p style="color: #6b7280; margin: 0; font-size: 0.9rem;">
                                    <strong>@<?php echo htmlspecialchars($user['username']); ?></strong>
                                </p>
                                <p style="color: #9ca3af; margin: 0.5rem 0 0; font-size: 0.85rem;">
                                    Nhấn vào biểu tượng camera để thay đổi ảnh đại diện
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Bio Section -->
                    <div class="profile-section">
                        <h2>Tiểu sử</h2>
                        <form method="POST">
                            <textarea name="bio" placeholder="Viết một chút về bản thân bạn..." 
                                      style="width: 100%; height: 100px; padding: 1rem; border: 1px solid #ddd; border-radius: 6px; font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">
                                <i class="fas fa-save"></i> Lưu tiểu sử
                            </button>
                        </form>
                    </div>
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
                                <a href="my-posts.php" class="btn btn-primary">
                                    <i class="fas fa-list"></i> Bài đăng của tôi
                                </a>
                            <?php endif; ?>
                            <?php if ($_SESSION['role'] === 'tenant'): ?>
                            <a href="favorites.php" class="btn btn-primary">
                                <i class="fas fa-heart"></i> Yêu thích
                            </a>
                            <?php endif; ?>
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
    </div>

    <!-- Avatar Edit Modal -->
    <div id="avatarModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thay đổi ảnh đại diện</h2>
                <button type="button" class="modal-close" onclick="closeAvatarModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="avatarForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="avatarInput">Chọn ảnh</label>
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" required>
                        <span class="file-hint">Tối đa 2MB, định dạng: JPG, PNG, GIF</span>
                    </div>

                    <div class="modal-preview empty" id="previewContainer">
                        <p style="margin: 1.5rem 0;">Xem trước ảnh của bạn ở đây</p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAvatarModal()">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
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
                <p>&copy; 2025 Tìm Trọ Sinh Viên. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>

    <script src="../../assets/js/main.js"></script>
    <script>
        const avatarModal = document.getElementById('avatarModal');
        const avatarInput = document.getElementById('avatarInput');
        const previewContainer = document.getElementById('previewContainer');
        const avatarForm = document.getElementById('avatarForm');

        // Open modal
        function openAvatarModal() {
            avatarModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Close modal
        function closeAvatarModal() {
            avatarModal.classList.remove('active');
            document.body.style.overflow = 'auto';
            avatarInput.value = '';
            previewContainer.innerHTML = '<p style="margin: 1.5rem 0;">Xem trước ảnh của bạn ở đây</p>';
            previewContainer.classList.add('empty');
        }

        // Preview image
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size
                if (file.size > 2 * 1024 * 1024) {
                    alert('File quá lớn, tối đa 2MB');
                    avatarInput.value = '';
                    return;
                }

                // Check file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Định dạng file không được hỗ trợ');
                    avatarInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    previewContainer.innerHTML = `<img src="${event.target.result}" alt="Preview">`;
                    previewContainer.classList.remove('empty');
                };
                reader.readAsDataURL(file);
            }
        });

        // Handle form submit
        avatarForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Reload page to show updated avatar
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Lỗi: ' + error.message);
            });
        });

        // Close modal when clicking outside
        avatarModal.addEventListener('click', function(e) {
            if (e.target === avatarModal) {
                closeAvatarModal();
            }
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && avatarModal.classList.contains('active')) {
                closeAvatarModal();
            }
        });
    </script>
</body>
</html>
