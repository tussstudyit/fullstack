<?php
session_start();
$_SESSION['user_id'] = 2;
$_SESSION['user_role'] = 'landlord';
$_SESSION['role'] = 'landlord';
$_SESSION['username'] = 'landlord_test';

$_SERVER['REQUEST_METHOD'] = 'POST';

$_POST = [
    'action' => 'create',
    'title' => 'Test Post - ' . date('H:i:s'),
    'category_id' => '1',
    'description' => 'Test description',
    'price' => '5000000',
    'area' => '50',
    'address' => 'Test Address',
    'district' => 'District 1',
    'city' => 'HCMC',
    'room_type' => 'apartment',
    'max_people' => '2',
    'gender' => 'any',
    'amenities' => ['wifi', 'parking']
];

echo "=== TEST: PostController JSON Response ===\n\n";

// Capture output
ob_start();

// Include controller (will output JSON)
require_once 'Controllers/PostController.php';

$output = ob_get_clean();

echo "Response output:\n";
echo $output . "\n\n";

// Try to parse as JSON
$decoded = json_decode($output, true);
if ($decoded) {
    echo "✓ Valid JSON:\n";
    echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "✗ INVALID JSON!\n";
    echo "First 200 chars: " . substr($output, 0, 200) . "\n";
}
?>
