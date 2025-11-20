<?php
require_once __DIR__ . '/../../config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('/fullstack/index.php');
}

$login_error = '';
$success_message = '';

// Lấy thông báo từ GET
if (isset($_GET['message'])) {
    $success_message = htmlspecialchars($_GET['message']);
}
if (isset($_GET['error'])) {
    $login_error = htmlspecialchars($_GET['error']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Tìm Trọ Sinh Viên</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            top: 50%;
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
            color: var(--primary-color);
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
            color: var(--primary-color);
            font-size: 0.875rem;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1><i class="fas fa-home"></i> Tìm Trọ</h1>
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

                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </button>
            </form>

            <div class="form-footer">
                <p>Chưa có tài khoản? <a href="register.php" style="font-weight: 600;">Đăng ký ngay</a></p>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
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

        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const credential = document.getElementById('credential').value.trim();
            const password = document.getElementById('password').value;
            
            let errorMsg = '';
            
            // Kiểm tra không để trống
            if (!credential || !password) {
                errorMsg = '❌ Vui lòng nhập email/username/số điện thoại và mật khẩu';
            }
            // Kiểm tra password độ dài
            else if (password.length < 6) {
                errorMsg = '❌ Mật khẩu phải có ít nhất 6 ký tự';
            }
            
            if (errorMsg) {
                e.preventDefault();
                alert(errorMsg);
                return false;
            }
            
            // Allow form to submit
            return true;
        });
    </script>
</body>
</html>
