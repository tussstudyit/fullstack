<?php
require_once __DIR__ . '/config.php';

$db = getDB();

// Check post_4 images
echo "<h2>Post 4 Images:</h2>";
$stmt = $db->prepare("SELECT * FROM post_images WHERE post_id = 4");
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($images);
echo "</pre>";

// Check post_8 images
echo "<h2>Post 8 Images:</h2>";
$stmt = $db->prepare("SELECT * FROM post_images WHERE post_id = 8");
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($images);
echo "</pre>";

// List all uploads
echo "<h2>Files in uploads folder:</h2>";
$files = scandir(__DIR__ . '/uploads');
foreach ($files as $f) {
    if ($f !== '.' && $f !== '..' && $f !== '.gitignore') {
        echo "- $f<br>";
    }
}

// Check if file exists
$testFile = __DIR__ . '/uploads/post_4_1764613181_692ddc3d719d0.png';
echo "<h2>Check file: post_4_1764613181_692ddc3d719d0.png</h2>";
echo "File exists: " . (file_exists($testFile) ? 'YES' : 'NO') . "<br>";

?>
