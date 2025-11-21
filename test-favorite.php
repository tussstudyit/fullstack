<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Models/Favorite.php';

// Giả lập session
$_SESSION['user_id'] = 4;  // tenant1 user ID (verified in database)
$_SESSION['username'] = 'tenant1';

$favoriteModel = new Favorite();

// Test 1: Add favorite
echo "=== Test Add Favorite ===\n";
$result = $favoriteModel->add($_SESSION['user_id'], 1);
var_dump($result);

// Test 2: Check if favorited
echo "\n=== Test Check Favorited ===\n";
$isFav = $favoriteModel->isFavorited($_SESSION['user_id'], 1);
var_dump($isFav);

// Test 3: Get user favorites
echo "\n=== Test Get User Favorites ===\n";
$favs = $favoriteModel->getByUserId($_SESSION['user_id']);
var_dump($favs);

// Test 4: Remove favorite
echo "\n=== Test Remove Favorite ===\n";
$result = $favoriteModel->remove($_SESSION['user_id'], 1);
var_dump($result);

// Test 5: Count favorites
echo "\n=== Test Count Favorites ===\n";
$count = $favoriteModel->countByUserId($_SESSION['user_id']);
var_dump($count);
?>
