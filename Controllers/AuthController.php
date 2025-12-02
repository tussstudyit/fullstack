<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Xử lý đăng nhập
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $credential = sanitize($_POST['credential'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($credential) || empty($password)) {
            redirect('/fullstack/Views/auth/login.php?error=' . urlencode('❌ Vui lòng nhập email/username/số điện thoại và mật khẩu'));
        }

        $result = $this->userModel->login($credential, $password);

        if ($result['success']) {
            // Redirect dựa vào role
            if ($_SESSION['role'] === 'admin') {
                redirect('/fullstack/Views/admin/dashboard.php');
            } else {
                redirect('/fullstack/index.php');
            }
        } else {
            redirect('/fullstack/Views/auth/login.php?error=' . urlencode('❌ ' . $result['message']));
        }

        return $result;
    }

    /**
     * Xử lý đăng ký
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        // Validate input
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $full_name = sanitize($_POST['fullname'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $role = sanitize($_POST['role'] ?? 'tenant');

        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
            redirect('/fullstack/Views/auth/register.php?error=' . urlencode('❌ Vui lòng điền đầy đủ thông tin bắt buộc'));
        }

        if (strlen($username) < 4 || strlen($username) > 20) {
            redirect('/fullstack/Views/auth/register.php?error=' . urlencode('❌ Tên đăng nhập phải từ 4-20 ký tự'));
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            redirect('/fullstack/Views/auth/register.php?error=' . urlencode('❌ Tên đăng nhập chỉ chứa chữ cái, số và dấu gạch dưới'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirect('/fullstack/Views/auth/register.php?error=' . urlencode('❌ Email không hợp lệ'));
        }

        if (strlen($password) < 6) {
            redirect('/fullstack/Views/auth/register.php?error=' . urlencode('❌ Mật khẩu phải có ít nhất 6 ký tự'));
        }

        if ($password !== $confirm_password) {
            redirect('/fullstack/Views/auth/register.php?error=' . urlencode('❌ Mật khẩu xác nhận không khớp'));
        }

        if (!in_array($role, ['tenant', 'landlord'])) {
            redirect('/fullstack/Views/auth/register.php?error=' . urlencode('❌ Vai trò không hợp lệ'));
        }

        if (!empty($phone) && !preg_match('/^[0-9]{10,11}$/', $phone)) {
            redirect('/fullstack/Views/auth/register.php?error=' . urlencode('❌ Số điện thoại phải từ 10-11 chữ số'));
        }

        $data = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'full_name' => $full_name,
            'phone' => $phone ?? null,
            'role' => $role
        ];

        $result = $this->userModel->register($data);

        if ($result['success']) {
            // Tự động đăng nhập sau khi đăng ký thành công
            $loginResult = $this->userModel->login($username, $password);
            if ($loginResult['success']) {
                // Redirect dựa vào role
                if ($_SESSION['role'] === 'admin') {
                    redirect('/fullstack/Views/admin/dashboard.php?success=' . urlencode('✓ Đăng ký và đăng nhập thành công'));
                } else {
                    redirect('/fullstack/index.php?success=' . urlencode('✓ Đăng ký và đăng nhập thành công'));
                }
            } else {
                // Nếu auto-login thất bại, redirect đến login page
                redirect('/fullstack/Views/auth/login.php?message=' . urlencode('✓ Đăng ký thành công. Vui lòng đăng nhập'));
            }
        } else {
            redirect('/fullstack/Views/auth/register.php?error=' . urlencode('❌ ' . $result['message']));
        }

        return $result;
    }

    /**
     * Xử lý đăng xuất
     */
    public function logout() {
        $_SESSION = [];
        session_destroy();
        redirect('/fullstack/index.php');
    }

    /**
     * Xử lý hành động
     */
    public function handleAction($action) {
        switch ($action) {
            case 'login':
                return $this->login();
            case 'register':
                return $this->register();
            case 'logout':
                return $this->logout();
            default:
                return ['success' => false, 'message' => 'Action not found'];
        }
    }
}

// Xử lý request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize($_POST['action'] ?? '');
    $controller = new AuthController();
    $result = $controller->handleAction($action);
    
    // Nếu không redirect, trả về JSON
    if (is_array($result)) {
        header('Content-Type: application/json');
        echo json_encode($result);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = sanitize($_GET['action'] ?? '');
    $controller = new AuthController();
    $result = $controller->handleAction($action);
} else {
    header('HTTP/1.0 404 Not Found');
}
?>
