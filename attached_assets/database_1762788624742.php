<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Xử lý preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$host = 'localhost';    
$dbname = 'baitaplon';       
$username = 'root';      
$password = '';           

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Kết nối thất bại: ' . $conn->connect_error
    ]));
}

$conn->set_charset("utf8mb4");
?>
