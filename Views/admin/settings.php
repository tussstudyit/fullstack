<?php
require_once __DIR__ . '/../../config.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('/fullstack/index.php');
    exit;
}

$message = '';
$error = '';
$conn = getDB();

// Xử lý cập nhật cài đặt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $setting_key = sanitize($_POST['key'] ?? '');
    $setting_value = sanitize($_POST['value'] ?? '');
    
    try {
        // Kiểm tra xem cài đặt đã tồn tại chưa
        $stmt = $conn->prepare("SELECT id FROM settings WHERE setting_key = ?");
        $stmt->execute([$setting_key]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$setting_value, $setting_key]);
        } else {
            $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
            $stmt->execute([$setting_key, $setting_value]);
        }
        
        $message = "Cài đặt đã được cập nhật thành công!";
    } catch (PDOException $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

// Lấy các cài đặt hiện tại
$settings = [];
try {
    $stmt = $conn->prepare("SELECT setting_key, setting_value FROM settings");
    $stmt->execute();
    $settings_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($settings_data as $s) {
        $settings[$s['setting_key']] = $s['setting_value'];
    }
} catch (PDOException $e) {
    error_log("Error fetching settings: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài Đặt - Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
        }

        .admin-sidebar {
            background: #1f2937;
            color: white;
            padding: 2rem 0;
        }

        .admin-logo {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 2rem;
        }

        .admin-logo h2 {
            color: white;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .admin-menu {
            list-style: none;
        }

        .admin-menu li a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .admin-menu li a:hover,
        .admin-menu li a.active {
            background: rgba(59, 130, 246, 0.1);
            color: #60a5fa;
            border-left: 3px solid #3b82f6;
        }

        .admin-main {
            background: #f3f4f6;
        }

        .admin-header {
            background: white;
            padding: 1.5rem 2rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-content {
            padding: 2rem;
        }

        .settings-container {
            max-width: 800px;
        }

        .settings-card {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .settings-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .settings-header h3 {
            margin-bottom: 0.5rem;
        }

        .settings-header p {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .settings-body {
            padding: 2rem;
        }

        .setting-item {
            margin-bottom: 2rem;
        }

        .setting-item:last-child {
            margin-bottom: 0;
        }

        .setting-item label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius-lg);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        @media (max-width: 1024px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <h2><i class="fas fa-shield-alt"></i> Admin Panel</h2>
            </div>

            <ul class="admin-menu">
                <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="posts.php"><i class="fas fa-home"></i> Quản lý bài đăng</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Quản lý người dùng</a></li>
                <li><a href="reports.php"><i class="fas fa-flag"></i> Báo cáo vi phạm</a></li>
                <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Cài đặt</a></li>
                <li><a href="../../Controllers/AuthController.php?action=logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <h1>Cài Đặt Hệ Thống</h1>
                    <p style="color: var(--text-secondary); margin-top: 0.25rem;">Quản lý các cài đặt chung của ứng dụng</p>
                </div>
            </div>

            <div class="admin-content">
                <div class="settings-container">
                    <?php if (!empty($message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo htmlspecialchars($message); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                    <?php endif; ?>

                    <!-- General Settings -->
                    <div class="settings-card">
                        <div class="settings-header">
                            <h3><i class="fas fa-cogs"></i> Cài Đặt Chung</h3>
                            <p>Cấu hình cơ bản cho hệ thống</p>
                        </div>
                        <div class="settings-body">
                            <form method="POST">
                                <div class="setting-item">
                                    <label for="site_name">Tên Website</label>
                                    <input type="text" id="site_name" name="key" value="site_name" style="display: none;">
                                    <input type="text" class="form-control" placeholder="Tên trang web" value="<?php echo htmlspecialchars($settings['site_name'] ?? 'Tìm Trọ Sinh Viên'); ?>">
                                </div>

                                <div class="setting-item">
                                    <label for="site_description">Mô Tả Website</label>
                                    <textarea class="form-control" rows="3" placeholder="Mô tả ngắn gọn về trang web">
<?php echo htmlspecialchars($settings['site_description'] ?? ''); ?>
                                    </textarea>
                                </div>

                                <div class="setting-item">
                                    <label for="contact_email">Email Liên Hệ</label>
                                    <input type="email" class="form-control" placeholder="email@example.com" value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>">
                                </div>

                                <div class="setting-item">
                                    <label for="contact_phone">Số Điện Thoại</label>
                                    <input type="tel" class="form-control" placeholder="+84..." value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>">
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Lưu Cài Đặt
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Email Settings -->
                    <div class="settings-card">
                        <div class="settings-header">
                            <h3><i class="fas fa-envelope"></i> Cài Đặt Email</h3>
                            <p>Tùy chỉnh thông số email thông báo</p>
                        </div>
                        <div class="settings-body">
                            <form method="POST">
                                <div class="setting-item">
                                    <label>
                                        <input type="checkbox" checked>
                                        Gửi email thông báo khi bài đăng được duyệt
                                    </label>
                                </div>

                                <div class="setting-item">
                                    <label>
                                        <input type="checkbox" checked>
                                        Gửi email thông báo khi có báo cáo vi phạm
                                    </label>
                                </div>

                                <div class="setting-item">
                                    <label>
                                        <input type="checkbox" checked>
                                        Gửi email xác nhận đăng ký tài khoản
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Lưu Cài Đặt
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- System Settings -->
                    <div class="settings-card">
                        <div class="settings-header">
                            <h3><i class="fas fa-server"></i> Cài Đặt Hệ Thống</h3>
                            <p>Tùy chỉnh những thông số kỹ thuật</p>
                        </div>
                        <div class="settings-body">
                            <form method="POST">
                                <div class="setting-item">
                                    <label>Kích Thước File Tối Đa (MB)</label>
                                    <input type="number" class="form-control" min="1" max="100" value="<?php echo htmlspecialchars($settings['max_file_size'] ?? '5'); ?>">
                                </div>

                                <div class="setting-item">
                                    <label>Thời Gian Hết Hạn Session (phút)</label>
                                    <input type="number" class="form-control" min="5" value="<?php echo htmlspecialchars($settings['session_timeout'] ?? '30'); ?>">
                                </div>

                                <div class="setting-item">
                                    <label>Số Bài Đăng Trên Một Trang</label>
                                    <input type="number" class="form-control" min="5" max="50" value="<?php echo htmlspecialchars($settings['posts_per_page'] ?? '10'); ?>">
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Lưu Cài Đặt
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
