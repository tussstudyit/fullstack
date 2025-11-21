<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Models/Favorite.php';

// Simulate a logged-in tenant
$_SESSION['user_id'] = 4;
$_SESSION['username'] = 'tenant1';
$_SESSION['role'] = 'tenant';

// Test remove action
echo "<h2>Testing Remove Favorite</h2>\n";

$_POST['action'] = 'remove';
$_POST['post_id'] = 1;
$_SERVER['REQUEST_METHOD'] = 'POST';

// Test directly with Favorite model
$fav = new Favorite();
$result = $fav->remove($_SESSION['user_id'], 1);
echo "<pre>";
var_dump($result);
echo "</pre>";

// Now add it back
echo "<h2>Testing Add Favorite Again</h2>\n";
$result = $fav->add($_SESSION['user_id'], 1);
echo "<pre>";
var_dump($result);
echo "</pre>";
?>
