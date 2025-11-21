<?php
session_start();
require_once 'config.php';
require_once 'Models/Post.php';

$_SESSION['user_id'] = 2;

echo "Testing Post::getByUserId()\n";
echo "===========================\n\n";

$post_model = new Post();

// Get posts for user 2
$posts = $post_model->getByUserId(2);

echo "Posts for User ID 2:\n";
echo json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "Count: " . count($posts) . "\n";
?>
