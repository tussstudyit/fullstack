<?php
require_once __DIR__ . '/config.php';

echo "=== EMAIL CHECKER ===\n\n";

// Danh sách email từ sample data
$sample_emails = [
    'admin@timtro.com',
    'landlord1@gmail.com',
    'landlord2@gmail.com',
    'tenant1@gmail.com',
    'tenant2@gmail.com'
];

try {
    $db = getDB();
    
    echo "Checking sample data emails:\n";
    foreach ($sample_emails as $email) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        
        $status = $result['count'] > 0 ? "✓ EXISTS" : "✗ NOT FOUND";
        echo "  $email - $status\n";
    }
    
    echo "\n=== ALL USERS IN DATABASE ===\n";
    $stmt = $db->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
    
    foreach ($users as $user) {
        echo "ID: {$user['id']}\n";
        echo "  Username: {$user['username']}\n";
        echo "  Email: {$user['email']}\n";
        echo "  Role: {$user['role']}\n";
        echo "  Created: {$user['created_at']}\n\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
