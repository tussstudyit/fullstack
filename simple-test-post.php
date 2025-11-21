<?php
session_start();
require_once 'config.php';
require_once 'Models/Post.php';

// Simulate landlord
$_SESSION['user_id'] = 2;
$_SESSION['role'] = 'landlord';

echo "Testing Post Creation\n";
echo "====================\n\n";

// Create post object
$post_model = new Post();

// Test data
$data = [
    'user_id' => 2,
    'category_id' => 1,
    'title' => 'Test Post ' . date('H:i:s'),
    'description' => 'This is a test post',
    'address' => '123 Nguyễn Chí Thanh',
    'district' => 'Quận Hải Châu',
    'city' => 'Đà Nẵng',
    'price' => 5000000,
    'area' => 30,
    'room_type' => 'single',
    'max_people' => 1,
    'gender' => 'any',
    'available_from' => date('Y-m-d'),
    'status' => 'approved'
];

echo "Input Data:\n";
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Create post
$result = $post_model->create($data);

echo "Result:\n";
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Check database
echo "Posts in Database:\n";
$db = getDB();
$stmt = $db->query('SELECT id, title, user_id, status, created_at FROM posts ORDER BY created_at DESC LIMIT 5');
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
?>
