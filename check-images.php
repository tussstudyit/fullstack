<?php
require_once __DIR__ . '/config.php';

$conn = getDB();

// Check last 5 posts and their images
$stmt = $conn->prepare("
    SELECT p.id, p.title, COUNT(pi.id) as image_count
    FROM posts p
    LEFT JOIN post_images pi ON p.id = pi.post_id
    GROUP BY p.id
    ORDER BY p.id DESC
    LIMIT 5
");
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "=== Posts with image count ===\n";
foreach ($posts as $post) {
    echo "Post ID: {$post['id']}, Title: {$post['title']}, Images: {$post['image_count']}\n";
    
    // Get images for this post
    $stmt = $conn->prepare("SELECT id, post_id, image_url, is_primary FROM post_images WHERE post_id = ? ORDER BY id ASC");
    $stmt->execute([$post['id']]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($images as $img) {
        $primary = $img['is_primary'] ? "PRIMARY" : "secondary";
        echo "  - Image ID: {$img['id']}, URL: {$img['image_url']}, Status: {$primary}\n";
    }
}

echo "\n=== Check if upload worked for latest post ===\n";
$stmt = $conn->prepare("SELECT id FROM posts ORDER BY id DESC LIMIT 1");
$stmt->execute();
$lastPost = $stmt->fetch(PDO::FETCH_ASSOC);

if ($lastPost) {
    echo "Latest post ID: {$lastPost['id']}\n";
    $stmt = $conn->prepare("SELECT * FROM post_images WHERE post_id = ? ORDER BY id ASC");
    $stmt->execute([$lastPost['id']]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Images for this post: " . count($images) . "\n";
    foreach ($images as $img) {
        print_r($img);
        echo "\n";
    }
}
?>
