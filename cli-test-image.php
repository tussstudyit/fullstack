<?php
// Test lưu ảnh đơn giản
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Controllers/ImageController.php';

echo "=== TEST LƯU ẢNH ===\n\n";

// 1. Tạo file test PNG đơn giản (1x1 pixel)
$testImagePath = __DIR__ . '/test-image.png';
// Smallest valid PNG (1x1 transparent pixel)
$pngData = hex2bin('89504e470d0a1a0a0000000d494844520000000100000001080600000000' . 
                    '1f15c4890000000a4944415408d76260000000020001e211e2330' .
                    '0000000049454e44ae426082');
file_put_contents($testImagePath, $pngData);
echo "1. Tạo file test: $testImagePath (" . round(filesize($testImagePath)/1024, 2) . " KB)\n";

// 2. Kiểm tra folder uploads
$imageController = new ImageController();
$uploadDir = $imageController->getUploadDir();
echo "2. Upload dir: $uploadDir\n";

$permCheck = $imageController->checkUploadPermissions();
echo "3. Quyền ghi folder: " . ($permCheck['writable'] ? 'OK' : 'LỖI') . " - " . $permCheck['message'] . "\n\n";

if (!$permCheck['writable']) {
    die("STOP: Folder không có quyền ghi!\n");
}

// 3. Tạo mock $_FILES array
$_FILES = [
    'images' => [
        'name' => ['test-image.png'],
        'type' => ['image/png'],
        'tmp_name' => [$testImagePath],
        'error' => [0],
        'size' => [filesize($testImagePath)]
    ]
];

// 4. Upload
echo "=== UPLOADING ===\n";
$result = $imageController->uploadMultipleImages(1, $_FILES['images']);
echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

// 5. Kiểm tra file trong folder
echo "=== FILES IN UPLOADS FOLDER ===\n";
$files = scandir($uploadDir);
$imageFiles = array_filter($files, function($f) {
    return $f !== '.' && $f !== '..' && $f !== '.gitignore';
});

echo "Số file: " . count($imageFiles) . "\n";
foreach ($imageFiles as $file) {
    $path = $uploadDir . $file;
    echo "- " . basename($file) . " (" . round(filesize($path)/1024, 2) . " KB)\n";
}

// 6. Cleanup
unlink($testImagePath);
echo "\n✓ Test hoàn tất!\n";
?>
