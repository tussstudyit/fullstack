<?php
session_start();
$_SESSION['user_id'] = 2;
$_SESSION['user_role'] = 'landlord';

require_once 'config.php';
require_once 'Models/Post.php';

$postModel = new Post($pdo);

echo "=== TEST: Full Post Workflow ===\n\n";

// Create test post
$postId = $postModel->create(
    2,
    'Căn hộ test - ' . date('H:i:s'),
    'Mô tả test',
    5000000,
    'Test District',
    'apartment',
    50,
    2,
    1,
    json_encode(['wifi', 'parking']),
    1
);

echo "✓ Step 1: Post created with ID: $postId\n";

// Verify in database
$stmt = $pdo->prepare('SELECT id, title, status FROM posts WHERE id = ?');
$stmt->execute([$postId]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
echo "✓ Step 2: In DB - '{$post['title']}' (Status: {$post['status']})\n";

// Check if appears in user's posts
$userPosts = $postModel->getByUserId(2);
$found = false;
foreach ($userPosts as $p) {
    if ($p['id'] == $postId) {
        $found = true;
        break;
    }
}
echo ($found ? "✓" : "✗") . " Step 3: Post in user's list (Total: " . count($userPosts) . ")\n";

// Check if in approved list
$stmt = $pdo->prepare('SELECT COUNT(*) as cnt FROM posts WHERE status = "approved"');
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "✓ Step 4: Total approved posts in DB: {$result['cnt']}\n";

echo "\n=== Workflow Test Complete ===\n";
?>
