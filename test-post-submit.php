<?php
// Simple test - just check if POST data is being received
header('Content-Type: application/json');

require_once 'config.php';

// Simulate logged-in landlord
$_SESSION['user_id'] = 2;
$_SESSION['role'] = 'landlord';

// Test data
$_POST = [
    'action' => 'create',
    'title' => 'Test Post - ' . date('H:i:s'),
    'description' => 'This is a test post to verify data transmission',
    'address' => '123 Nguyễn Chí Thanh',
    'district' => 'Quận Hải Châu',
    'city' => 'Đà Nẵng',
    'price' => '5000000',
    'area' => '30',
    'category_id' => '1',
    'room_type' => 'single',
    'max_people' => '1',
    'gender' => 'any',
    'available_from' => date('Y-m-d')
];

$_SERVER['REQUEST_METHOD'] = 'POST';

// Include and test controller
require_once 'Controllers/PostController.php';

$action = sanitize($_POST['action'] ?? '');
$controller = new PostController();
$result = $controller->handleAction($action);

// Return result
echo json_encode([
    'test_result' => $result,
    'timestamp' => date('Y-m-d H:i:s')
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
