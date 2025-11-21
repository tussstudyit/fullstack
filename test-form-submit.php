<?php
session_start();
$_SESSION['user_id'] = 2;
$_SESSION['user_role'] = 'landlord';
$_SESSION['role'] = 'landlord';

// Simulate form POST
$_POST = [
    'action' => 'create',
    'title' => 'Căn hộ test - ' . date('H:i:s'),
    'category_id' => '1',
    'description' => 'Mô tả test',
    'price' => '5000000',
    'area' => '50',
    'address' => 'Test Address',
    'district' => 'District 1',
    'city' => 'HCMC',
    'room_type' => 'apartment',
    'max_people' => '2',
    'gender' => 'any',
    'amenities' => ['wifi', 'parking'],
    'utilities' => ['water', 'electric'],
    'rules' => ['no_smoking'],
    'available_from' => date('Y-m-d')
];

$_SERVER['REQUEST_METHOD'] = 'POST';

echo "=== TEST: Form Submission ===\n";
echo "POST data:\n";
echo "- title: " . $_POST['title'] . "\n";
echo "- price: " . $_POST['price'] . "\n";
echo "- amenities: " . json_encode($_POST['amenities']) . "\n\n";

// Include controller
require_once 'Controllers/PostController.php';

// Execute
ob_start();
require_once 'Controllers/PostController.php';
$output = ob_get_clean();

echo "Response: " . $output . "\n";
?>
