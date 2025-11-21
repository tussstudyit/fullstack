<?php
require 'config.php';
$db = getDB();
echo "=== POST STATUS CHECK ===\n";
$stmt = $db->query('SELECT id, title, status FROM posts ORDER BY created_at DESC LIMIT 15');
$posts = $stmt->fetchAll();
foreach($posts as $row) {
    echo "ID: " . $row['id'] . " | Title: " . $row['title'] . " | Status: " . $row['status'] . "\n";
}
echo "\n=== STATUS SUMMARY ===\n";
$stmt = $db->query('SELECT status, COUNT(*) as count FROM posts GROUP BY status');
$summary = $stmt->fetchAll();
foreach($summary as $row) {
    echo $row['status'] . ": " . $row['count'] . "\n";
}
?>
