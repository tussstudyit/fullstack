<?php
require_once 'config.php';

try {
    $db = getDB();
    
    // Check posts table
    $result = $db->query('SELECT COUNT(*) as cnt FROM posts WHERE status="approved"')->fetch(PDO::FETCH_ASSOC);
    echo "Posts approved: " . $result['cnt'] . "\n";
    
    // Get sample posts
    $posts = $db->query('SELECT id, title, status FROM posts LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
    echo "\nSample posts:\n";
    print_r($posts);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
