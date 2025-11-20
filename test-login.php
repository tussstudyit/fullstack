<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Models/User.php';

$userModel = new User();

// Test 1: Tìm user admin
echo "<h2>Test 1: Tìm user admin</h2>";
$user = $userModel->findByEmailOrUsernameOrPhone('admin');
if ($user) {
    echo "✓ Tìm thấy user: " . $user['username'] . " (ID: " . $user['id'] . ")<br>";
    echo "Email: " . $user['email'] . "<br>";
    echo "Hash password: " . substr($user['password'], 0, 20) . "...<br>";
} else {
    echo "✗ Không tìm thấy user admin<br>";
}

// Test 2: Verify password
echo "<h2>Test 2: Verify password</h2>";
if ($user) {
    $test_password = '123456';
    $hash = $user['password'];
    
    echo "Password để test: $test_password<br>";
    echo "Hash trong DB: $hash<br>";
    
    if (password_verify($test_password, $hash)) {
        echo "✓ Password đúng!<br>";
    } else {
        echo "✗ Password sai<br>";
        
        // Thử tạo hash mới từ password
        echo "<h3>Tạo hash mới:</h3>";
        $new_hash = password_hash($test_password, PASSWORD_BCRYPT);
        echo "Hash mới: $new_hash<br>";
        echo "<code>UPDATE users SET password = '$new_hash' WHERE username = 'admin';</code>";
    }
}

// Test 3: Login
echo "<h2>Test 3: Login</h2>";
$result = $userModel->login('admin', '123456');
echo "<pre>";
print_r($result);
echo "</pre>";
?>
