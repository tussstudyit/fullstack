<?php
require_once __DIR__ . '/config.php';

echo "=== DATABASE DEBUG ===\n\n";

try {
    $db = getDB();
    
    // Kiểm tra users
    echo "1. Danh sách Users:\n";
    $stmt = $db->query("SELECT id, username, email, role FROM users");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "   - Không có users nào\n";
    } else {
        foreach ($users as $user) {
            echo "   - ID: {$user['id']}, Username: {$user['username']}, Email: {$user['email']}, Role: {$user['role']}\n";
        }
    }
    
    // Kiểm tra categories
    echo "\n2. Danh sách Categories:\n";
    $stmt = $db->query("SELECT id, name FROM categories");
    $categories = $stmt->fetchAll();
    
    if (empty($categories)) {
        echo "   - Không có categories nào\n";
    } else {
        foreach ($categories as $cat) {
            echo "   - ID: {$cat['id']}, Name: {$cat['name']}\n";
        }
    }
    
    // Kiểm tra posts
    echo "\n3. Danh sách Posts:\n";
    $stmt = $db->query("SELECT id, title, user_id, status FROM posts");
    $posts = $stmt->fetchAll();
    
    if (empty($posts)) {
        echo "   - Không có posts nào\n";
    } else {
        foreach ($posts as $post) {
            echo "   - ID: {$post['id']}, Title: {$post['title']}, User ID: {$post['user_id']}, Status: {$post['status']}\n";
        }
    }
    
    echo "\n=== SUCCESS ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>
