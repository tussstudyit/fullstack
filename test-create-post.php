<?php
// Start session BEFORE any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config.php';

echo "Step 1: Config loaded\n";
echo "Step 2: Session started\n";

// Simulate a logged-in landlord
$_SESSION['user_id'] = 2;
$_SESSION['username'] = 'landlord1';
$_SESSION['role'] = 'landlord';

echo "Step 3: Session set\n";

// Simulate POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'action' => 'create',
    'title' => 'Test Post - ' . date('H:i:s'),
    'description' => 'This is a test post',
    'address' => '123 Test Street',
    'district' => 'Quận Hải Châu',
    'city' => 'Đà Nẵng',
    'price' => 5000000,
    'area' => 30,
    'category_id' => '1',
    'room_type' => 'single',
    'max_people' => 1,
    'gender' => 'any',
    'available_from' => date('Y-m-d')
];

echo "Step 4: POST data prepared\n";

// Include controller
require 'Controllers/PostController.php';

echo "Step 5: Controller included\n";

// Handle request
$action = sanitize($_POST['action'] ?? '');
$controller = new PostController();
$result = $controller->handleAction($action);

echo "=== POST CREATION TEST ===\n";
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Check database
echo "=== POSTS IN DATABASE ===\n";
$db = getDB();
$stmt = $db->query('SELECT id, title, user_id, status FROM posts ORDER BY created_at DESC LIMIT 5');
$posts = $stmt->fetchAll();
foreach($posts as $post) {
    echo "ID: " . $post['id'] . " | Title: " . $post['title'] . " | User: " . $post['user_id'] . " | Status: " . $post['status'] . "\n";
}
?>
