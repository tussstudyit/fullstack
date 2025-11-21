<?php
require_once __DIR__ . '/config.php';

$conn = getDB();

echo "<h2>Danh sách Users</h2>";
$stmt = $conn->prepare("SELECT id, username, role FROM users");
$stmt->execute();
$users = $stmt->fetchAll();
foreach ($users as $user) {
    echo "ID: {$user['id']}, Username: {$user['username']}, Role: {$user['role']}<br>";
}

echo "<h2>Danh sách Posts</h2>";
$stmt = $conn->prepare("SELECT id, title, user_id FROM posts LIMIT 5");
$stmt->execute();
$posts = $stmt->fetchAll();
foreach ($posts as $post) {
    echo "ID: {$post['id']}, Title: {$post['title']}, User: {$post['user_id']}<br>";
}
?>
