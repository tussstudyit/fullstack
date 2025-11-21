<?php
session_start();
require_once 'config.php';
require_once 'Models/Post.php';
require_once 'Models/User.php';

// Simulate logged in user (landlord)
$_SESSION['user_id'] = 2;
$_SESSION['user_role'] = 'landlord';
$_SESSION['username'] = 'landlord_test';

echo "=== TEST: Full Post Workflow ===\n\n";

// Step 1: Simulate form submission
echo "Step 1: Create new post via form submission\n";
$_POST = array(
    'title' => 'Test Post - ' . date('Y-m-d H:i:s'),
    'description' => 'This is a test post to verify the complete workflow',
    'price' => 5000000,
    'location' => 'Test District, Test City',
    'room_type' => 'apartment',
    'area' => 50,
    'bedrooms' => 2,
    'bathrooms' => 1,
    'amenities' => ['wifi', 'parking'],
    'category_id' => 1
);

// Include and execute PostController
require_once 'Controllers/PostController.php';
$postController = new PostController();

// Manually call create method
$postModel = new Post($pdo);
$postId = $postModel->create(
    $_SESSION['user_id'],
    $_POST['title'],
    $_POST['description'],
    $_POST['price'],
    $_POST['location'],
    $_POST['room_type'],
    $_POST['area'],
    $_POST['bedrooms'],
    $_POST['bathrooms'],
    json_encode($_POST['amenities']),
    $_POST['category_id']
);

echo "✓ Post created with ID: $postId\n";

// Step 2: Verify post in database
echo "\nStep 2: Verify post in database\n";
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$postId]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if ($post) {
    echo "✓ Post found in database:\n";
    echo "  - Title: {$post['title']}\n";
    echo "  - User ID: {$post['user_id']}\n";
    echo "  - Status: {$post['status']}\n";
    echo "  - Price: {$post['price']}\n";
} else {
    echo "✗ Post NOT found in database!\n";
}

// Step 3: Test retrieval from Post Model
echo "\nStep 3: Get post via Post Model (getById)\n";
$retrievedPost = $postModel->getById($postId);
if ($retrievedPost) {
    echo "✓ Post retrieved successfully\n";
    echo "  - Title: {$retrievedPost['title']}\n";
    echo "  - Status: {$retrievedPost['status']}\n";
} else {
    echo "✗ Failed to retrieve post\n";
}

// Step 4: Verify post appears in getByUserId
echo "\nStep 4: Verify post appears in user's post list (getByUserId)\n";
$userPosts = $postModel->getByUserId($_SESSION['user_id']);
echo "Total posts for user {$_SESSION['user_id']}: " . count($userPosts) . "\n";

$found = false;
foreach ($userPosts as $p) {
    if ($p['id'] == $postId) {
        $found = true;
        echo "✓ NEW POST FOUND in user's list!\n";
        echo "  - Title: {$p['title']}\n";
        echo "  - Created at: {$p['created_at']}\n";
        break;
    }
}

if (!$found) {
    echo "✗ NEW POST NOT FOUND in user's list!\n";
}

// Step 5: Test retrieval from approved posts (list.php query)
echo "\nStep 5: Verify post appears in approved posts list\n";
$stmt = $pdo->prepare("SELECT * FROM posts WHERE status = 'approved' AND id = ?");
$stmt->execute([$postId]);
$approvedPost = $stmt->fetch(PDO::FETCH_ASSOC);

if ($approvedPost) {
    echo "✓ Post found in approved list\n";
} else {
    echo "✗ Post NOT in approved list\n";
}

// Step 6: Count total approved posts
echo "\nStep 6: Check total approved posts in database\n";
$stmt = $pdo->query("SELECT COUNT(*) as total FROM posts WHERE status = 'approved'");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total approved posts: {$result['total']}\n";

echo "\n=== Workflow Test Complete ===\n";
?>
