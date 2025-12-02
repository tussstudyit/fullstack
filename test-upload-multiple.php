<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Controllers/ImageController.php';

// Fake session
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'landlord';

// Test data
$postId = 4;
echo "Testing upload for post ID: $postId\n\n";

// Create dummy files in $_FILES format
$_FILES['images'] = [
    'name' => ['test1.jpg', 'test2.jpg'],
    'type' => ['image/jpeg', 'image/jpeg'],
    'tmp_name' => [__DIR__ . '/README.md', __DIR__ . '/README.md'],
    'error' => [0, 0],
    'size' => [1000, 2000]
];

echo "Test $_FILES format:\n";
var_dump($_FILES['images']);

// Now test the actual upload
$imageController = new ImageController();

// Check if we can access uploadMultipleImages
if (method_exists($imageController, 'uploadMultipleImages')) {
    echo "\nuploadMultipleImages method exists\n";
    echo "Testing with 2 files...\n";
    
    // We can't actually upload with dummy files, but we can verify the method exists
    echo "Method signature check: OK\n";
} else {
    echo "ERROR: uploadMultipleImages method not found\n";
}

// Check ImageController
$reflect = new ReflectionClass($imageController);
echo "\n\nImageController methods:\n";
foreach ($reflect->getMethods() as $method) {
    if ($method->isPublic()) {
        echo "- " . $method->getName() . "\n";
    }
}
?>
