<?php
require_once __DIR__ . '/../../config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(BASE_PATH . 'index.php');
}

$login_error = '';
$success_message = '';
$error_type = '';

// Lấy thông báo từ GET (backward compatibility)
if (isset($_GET['message'])) {
    $success_message = htmlspecialchars($_GET['message']);
}
if (isset($_GET['error'])) {
    $login_error = htmlspecialchars($_GET['error']);
}

// Lấy thông báo từ SESSION (mới)
if (isset($_SESSION['login_error'])) {
    $login_error = htmlspecialchars($_SESSION['login_error']);
    $error_type = isset($_SESSION['login_error_type']) ? htmlspecialchars($_SESSION['login_error_type']) : '';
    
    // Xóa session sau khi lấy để không hiển thị lại lần sau
    // Nhưng delay 1 chút để JavaScript có thể access
    // unset($_SESSION['login_error']);
    // unset($_SESSION['login_error_type']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - NhaTot</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: whitesmoke;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 0;
            margin: 0;
            align-items: center;
            justify-content: center;
            overflow-y: scroll;
        }

        .auth-wrapper {
            width: 100%;
            display: flex;
            flex: 1;
            align-items: center;
            justify-content: center;
        }

        .auth-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
            max-width: 450px;
            width: 100%;
            margin: 2rem;
            overflow: hidden;
        }

        .auth-header {
            text-align: center;
            padding: 2rem 2rem 1rem;
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 50%, #2563eb 100%);
            color: white;
        }

        .auth-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .auth-header p {
            opacity: 0.9;
        }

        .auth-body {
            padding: 2rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: none;
        }

        .alert.success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            display: block;
        }

        .alert.error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            display: block;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 73%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-secondary);
        }

        .form-group-with-icon {
            position: relative;
        }

        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .form-footer p {
            margin: 0;
        }

        .form-footer a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .forgot-password {
            text-align: right;
            margin-top: 1rem;
        }

        .forgot-password a {
            color: #3b82f6;
            font-size: 0.875rem;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
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
                <li><a href="../../Views/posts/list.php" class="nav-link">Danh sách trọ</a></li>
            </ul>
            <div class="nav-actions">
                <a href="login.php" class="btn btn-outline btn-sm">Đăng nhập</a>
                <a href="register.php" class="btn btn-register btn-sm">Đăng ký</a>
            </div>
            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-header">
            <h1><i class="fas fa-home"></i> NhaTot</h1>
            <p>Đăng nhập tài khoản</p>
        </div>

        <div class="auth-body">
            <?php if ($success_message): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($login_error): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $login_error; ?>
                </div>
            <?php endif; ?>

            <form action="../../Controllers/AuthController.php" method="POST" id="loginForm">
                <input type="hidden" name="action" value="login">
                <!-- Data attribute để JavaScript access error type -->
                <input type="hidden" id="errorTypeData" value="<?php echo $error_type; ?>">

                <div class="form-group">
                    <label class="form-label" for="credential">Email / Username / Số điện thoại</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="credential" 
                        name="credential" 
                        placeholder="Nhập email, username hoặc số điện thoại"
                        required
                    >
                </div>
                
                <!-- Hidden field to track login error type -->
                <input type="hidden" id="errorType" name="errorType" value="">
                

                <div class="form-group form-group-with-icon">
                    <label class="form-label" for="password">Mật khẩu</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        name="password" 
                        placeholder="Nhập mật khẩu"
                        required
                    >
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                </div>

                <div class="forgot-password">
                    <a href="forgot-password.php">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn btn-outline btn-lg" style="width: 100%;">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </button>
            </form>

            <div class="form-footer">
                <p>Chưa có tài khoản? <a href="register.php" style="font-weight: 600;">Đăng ký ngay</a></p>
            </div>
        </div>
    </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
        // Lưu trạng thái login vào localStorage
        const LOGIN_STORAGE_KEY = 'login_credentials';
        const LOGIN_ERROR_TYPE_KEY = 'login_error_type';

        // Function to save username (khi password sai)
        function saveUsername(username) {
            const data = {
                credential: username,
                timestamp: new Date().getTime()
            };
            localStorage.setItem(LOGIN_STORAGE_KEY, JSON.stringify(data));
        }

        // Function to clear all login data (khi username sai)
        function clearLoginData() {
            localStorage.removeItem(LOGIN_STORAGE_KEY);
            localStorage.removeItem(LOGIN_ERROR_TYPE_KEY);
        }

        // Restore username
        function restoreUsername() {
            const stored = localStorage.getItem(LOGIN_STORAGE_KEY);
            console.log('Stored data:', stored);
            if (stored) {
                try {
                    const data = JSON.parse(stored);
                    document.getElementById('credential').value = data.credential;
                    console.log('Restored username:', data.credential);
                    // Tự động focus vào field password
                    document.getElementById('password').focus();
                } catch (e) {
                    console.error('Error parsing stored login data:', e);
                }
            }
        }

        // Xử lý error response từ server khi page load
        function handleLoginError() {
            const errorElement = document.querySelector('.alert.error');
            const errorTypeParam = document.getElementById('errorTypeData').value;
            
            console.log('Error type:', errorTypeParam);
            console.log('Error element:', errorElement);
            
            if (errorElement) {
                const errorText = errorElement.textContent;
                console.log('Error text:', errorText);
                
                // Xử lý dựa trên errorType từ SESSION
                if (errorTypeParam === 'password_wrong') {
                    // Nếu là lỗi password (password không đúng) -> GIỮ LẠI USERNAME
                    // Username đã được save từ form submission
                    console.log('Password wrong - keeping username');
                }
                else if (errorTypeParam === 'user_not_found') {
                    // Nếu là lỗi username (user không tồn tại) -> XÓA TOÀN BỘ
                    clearLoginData();
                    console.log('User not found - clearing all data');
                }
                else if (errorTypeParam === 'account_banned') {
                    // Nếu là lỗi account bị khóa -> XÓA TOÀN BỘ
                    clearLoginData();
                    console.log('Account banned - clearing all data');
                }
            } else {
                // KHÔNG CÓ LỖI -> XÓA TOÀN BỘ (trang load bình thường)
                clearLoginData();
                console.log('No error - cleared all login data');
            }
        }

        // Restore username khi page load
        document.addEventListener('DOMContentLoaded', function() {
            handleLoginError();
            
            // Chỉ restore username nếu còn lỗi (tức là user quay lại ngay)
            const errorElement = document.querySelector('.alert.error');
            if (errorElement) {
                restoreUsername();
            }
            
            // Clear session errors sau khi JavaScript đã xử lý xong
            // (send beacon để server clear session)
            if (errorElement) {
                navigator.sendBeacon('../../api/clear-login-errors.php');
            }
        });

        // Password toggle
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = passwordInput.nextElementSibling;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Form validation and credential save
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const credential = document.getElementById('credential').value.trim();
            const password = document.getElementById('password').value;
            
            let errorMsg = '';
            
            // Kiểm tra không để trống
            if (!credential || !password) {
                errorMsg = '❌ Vui lòng nhập email/username/số điện thoại và mật khẩu';
                // Clear data nếu có lỗi validation
                clearLoginData();
            }
            // Kiểm tra password độ dài
            else if (password.length < 6) {
                errorMsg = '❌ Mật khẩu phải có ít nhất 6 ký tự';
                // Clear data nếu có lỗi validation
                clearLoginData();
            }
            
            if (errorMsg) {
                e.preventDefault();
                alert(errorMsg);
                return false;
            }
            
            // Lưu credential trước khi submit form
            // Nếu server trả về password_wrong, nó sẽ được giữ lại
            // Nếu server trả về user_not_found, handleLoginError sẽ xóa nó
            saveUsername(credential);
            console.log('Saved username before submit:', credential);
            
            // Allow form to submit
            return true;
        });
    </script>
</body>
</html>
