<?php
// Debug script to test multiple image upload
require_once __DIR__ . '/config.php';

echo "<h2>Test: Check uploaded images count</h2>";

// Check latest posts with their image counts
$db = getDB();
$stmt = $db->prepare("
    SELECT p.id, p.title, COUNT(pi.id) as image_count
    FROM posts p
    LEFT JOIN post_images pi ON p.id = pi.post_id
    GROUP BY p.id
    ORDER BY p.id DESC
    LIMIT 10
");
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Post ID</th><th>Title</th><th>Image Count</th></tr>";
foreach ($posts as $post) {
    echo "<tr>";
    echo "<td>" . $post['id'] . "</td>";
    echo "<td>" . htmlspecialchars($post['title']) . "</td>";
    echo "<td><strong>" . $post['image_count'] . "</strong></td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>Files in uploads folder</h2>";
$files = scandir(__DIR__ . '/uploads');
$imageFiles = array_filter($files, function($f) {
    return $f !== '.' && $f !== '..' && $f !== '.gitignore' && strpos($f, 'post_') === 0;
});

echo "Total image files: " . count($imageFiles) . "<br>";
foreach ($imageFiles as $f) {
    echo "- $f<br>";
}

?>
