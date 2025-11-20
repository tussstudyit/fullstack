<?php
require_once __DIR__ . '/../../config.php';

if (isLoggedIn()) {
    redirect('/fullstack/index.php');
}

$error_message = '';
$success_message = '';

if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
}
if (isset($_GET['success'])) {
    $success_message = htmlspecialchars($_GET['success']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Tìm Trọ Sinh Viên</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .auth-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
            max-width: 600px;
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

        .role-selection {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .role-option {
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .role-option:hover {
            border-color: var(--primary-color);
            background: var(--light-color);
        }

        .role-option input[type="radio"] {
            display: none;
        }

        .role-option input[type="radio"]:checked + .role-content {
            color: var(--primary-color);
        }

        .role-option input[type="radio"]:checked ~ .role-icon {
            color: var(--primary-color);
        }

        .role-option.active {
            border-color: var(--primary-color);
            background: rgba(59, 130, 246, 0.05);
        }

        .role-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .role-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .role-description {
            font-size: 0.875rem;
            color: var(--text-secondary);
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .password-strength {
            height: 4px;
            background: var(--border-color);
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }

        .strength-weak { width: 33%; background: var(--danger-color); }
        .strength-medium { width: 66%; background: var(--warning-color); }
        .strength-strong { width: 100%; background: var(--success-color); }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: none;
            border-left: 4px solid;
        }

        .alert.success {
            background-color: #d4edda;
            border-color: #28a745;
            color: #155724;
            display: block;
        }

        .alert.error {
            background-color: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
            display: block;
        }

        .alert i {
            margin-right: 0.5rem;
        }

        @media (max-width: 640px) {
            .role-selection {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1><i class="fas fa-home"></i> Tìm Trọ</h1>
            <p>Tạo tài khoản mới</p>
        </div>

        <div class="auth-body">
            <?php if ($success_message): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form id="registerForm" action="../../Controllers/AuthController.php" method="POST">
                <input type="hidden" name="action" value="register">

                <div class="form-group">
                    <label class="form-label">Bạn là:</label>
                    <div class="role-selection">
                        <label class="role-option" data-role="tenant">
                            <input type="radio" name="role" value="tenant" required>
                            <div class="role-content">
                                <div class="role-icon"><i class="fas fa-user"></i></div>
                                <div class="role-title">Người thuê</div>
                                <div class="role-description">Tìm kiếm phòng trọ</div>
                            </div>
                        </label>
                        <label class="role-option" data-role="landlord">
                            <input type="radio" name="role" value="landlord" required>
                            <div class="role-content">
                                <div class="role-icon"><i class="fas fa-building"></i></div>
                                <div class="role-title">Người cho thuê</div>
                                <div class="role-description">Đăng tin cho thuê</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="fullname">Họ và tên</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="fullname" 
                            name="fullname" 
                            placeholder="Nhập họ và tên"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Số điện thoại</label>
                        <input 
                            type="tel" 
                            class="form-control" 
                            id="phone" 
                            name="phone" 
                            placeholder="Nhập số điện thoại"
                            pattern="[0-9]{10,11}"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="email" 
                        name="email" 
                        placeholder="Nhập địa chỉ email"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="username">Tên đăng nhập</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="username" 
                        name="username" 
                        placeholder="Nhập tên đăng nhập"
                        pattern="[a-zA-Z0-9_]{4,20}"
                        title="Tên đăng nhập phải từ 4-20 ký tự, chỉ bao gồm chữ cái, số và dấu gạch dưới"
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
                        minlength="6"
                        required
                    >
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                </div>

                <div class="form-group form-group-with-icon">
                    <label class="form-label" for="confirm_password">Xác nhận mật khẩu</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="confirm_password" 
                        name="confirm_password" 
                        placeholder="Nhập lại mật khẩu"
                        required
                    >
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: start; gap: 0.5rem;">
                        <input type="checkbox" name="agree_terms" required style="margin-top: 0.25rem;">
                        <span>Tôi đồng ý với <a href="#" class="text-primary">Điều khoản dịch vụ</a> và <a href="#" class="text-primary">Chính sách bảo mật</a></span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus"></i> Đăng ký
                </button>
            </form>

            <div class="form-footer">
                <p>Đã có tài khoản? <a href="login.php" class="text-primary" style="font-weight: 600;">Đăng nhập ngay</a></p>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
        // Role selection
        document.querySelectorAll('.role-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.role-option').forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                this.querySelector('input[type="radio"]').checked = true;
            });
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

        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z\d]/.test(password)) strength++;
            
            strengthBar.className = 'password-strength-bar';
            if (strength <= 2) strengthBar.classList.add('strength-weak');
            else if (strength <= 4) strengthBar.classList.add('strength-medium');
            else strengthBar.classList.add('strength-strong');
        });

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const fullname = document.getElementById('fullname').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const roleSelected = document.querySelector('input[name="role"]:checked');
            
            let errorMsg = '';
            
            // Kiểm tra đầy đủ thông tin
            if (!username || !email || !password || !confirmPassword || !fullname) {
                errorMsg = '❌ Vui lòng điền đầy đủ thông tin bắt buộc';
            }
            // Kiểm tra độ dài username
            else if (username.length < 4 || username.length > 20) {
                errorMsg = '❌ Tên đăng nhập phải từ 4-20 ký tự';
            }
            // Kiểm tra format username
            else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
                errorMsg = '❌ Tên đăng nhập chỉ chứa chữ cái, số và dấu gạch dưới';
            }
            // Kiểm tra email
            else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errorMsg = '❌ Email không hợp lệ';
            }
            // Kiểm tra độ dài password
            else if (password.length < 6) {
                errorMsg = '❌ Mật khẩu phải có ít nhất 6 ký tự';
            }
            // Kiểm tra password khớp
            else if (password !== confirmPassword) {
                errorMsg = '❌ Mật khẩu xác nhận không khớp';
            }
            // Kiểm tra số điện thoại
            else if (phone && !/^[0-9]{10,11}$/.test(phone)) {
                errorMsg = '❌ Số điện thoại phải từ 10-11 chữ số';
            }
            // Kiểm tra role
            else if (!roleSelected) {
                errorMsg = '❌ Vui lòng chọn vai trò của bạn';
            }
            
            if (errorMsg) {
                alert(errorMsg);
                return false;
            }
            
            // Nếu hợp lệ, submit form
            this.submit();
        });
    </script>
</body>
</html>
