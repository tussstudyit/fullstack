<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'tanloz');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_URL', 'http://localhost');
define('SITE_NAME', 'Tìm Trọ Sinh Viên');

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

date_default_timezone_set('Asia/Ho_Chi_Minh');

session_start();

function getDB() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            // Use the DB_* constants defined above to build the DSN
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log the detailed error and show a generic message to the user
            error_log('Database connection failed: ' . $e->getMessage());
            die('Database connection failed.');
        }
    }
    
    return $conn;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isLandlord() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'landlord';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>
