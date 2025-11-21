<?php
require 'config.php';
$db = getDB();
echo "=== USERS IN DATABASE ===\n";
$stmt = $db->query('SELECT id, username, email, role FROM users LIMIT 10');
$users = $stmt->fetchAll();
foreach($users as $user) {
    echo "ID: " . $user['id'] . " | Username: " . $user['username'] . " | Email: " . $user['email'] . " | Role: " . $user['role'] . "\n";
}
?>
