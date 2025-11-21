<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Models/Favorite.php';
require_once __DIR__ . '/Models/User.php';

// Simulate a logged-in tenant user
$userModel = new User();
$tenant = $userModel->findByUsername('tenant1');

if (!$tenant) {
    echo "Tenant1 not found!";
    exit;
}

// Set session as if user logged in
$_SESSION['user_id'] = $tenant['id'];
$_SESSION['username'] = $tenant['username'];
$_SESSION['email'] = $tenant['email'];
$_SESSION['role'] = $tenant['role'];

echo "<h3>Testing Favorite for: " . $tenant['username'] . " (ID: " . $tenant['id'] . ")</h3>";

$favoriteModel = new Favorite();

// Test 1: Add favorite for post 1
echo "<h4>Test 1: Add favorite for post 1</h4>";
$result = $favoriteModel->add($_SESSION['user_id'], 1);
echo "<pre>";
var_dump($result);
echo "</pre>";

// Test 2: Check if favorited
echo "<h4>Test 2: Check if post 1 is favorited</h4>";
$isFav = $favoriteModel->isFavorited($_SESSION['user_id'], 1);
echo "Is favorited: " . ($isFav ? 'YES' : 'NO') . "<br>";

// Test 3: Get user favorites
echo "<h4>Test 3: Get user favorites</h4>";
$favs = $favoriteModel->getByUserId($_SESSION['user_id']);
echo "Count: " . count($favs) . "<br>";
echo "<pre>";
var_dump($favs);
echo "</pre>";

// Test 4: Remove favorite
echo "<h4>Test 4: Remove favorite for post 1</h4>";
$result = $favoriteModel->remove($_SESSION['user_id'], 1);
echo "<pre>";
var_dump($result);
echo "</pre>";

// Test 5: Count favorites after remove
echo "<h4>Test 5: Count favorites after remove</h4>";
$count = $favoriteModel->countByUserId($_SESSION['user_id']);
echo "Count: " . $count . "<br>";
?>
