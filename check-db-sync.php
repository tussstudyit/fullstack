<?php
require_once 'config.php';

echo "=== DATABASE SYNCHRONIZATION CHECK ===\n";
echo "Host: " . DB_HOST . "\n";
echo "Database: " . DB_NAME . "\n";
echo "User: " . DB_USER . "\n";
echo "Port: 3306\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

try {
    $db = getDB();
    echo "✓ Database connection successful!\n\n";
    
    // Get all tables
    $tables = $db->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "' ORDER BY TABLE_NAME")->fetchAll(PDO::FETCH_ASSOC);
    
    $expectedTables = [
        'categories',
        'comment_votes',
        'comments',
        'conversations',
        'favorites',
        'messages',
        'notifications',
        'post_images',
        'post_likes',
        'posts',
        'reviews',
        'users'
    ];
    
    echo "📊 TABLE STATUS:\n";
    echo str_repeat("-", 60) . "\n";
    
    $foundTables = [];
    foreach ($tables as $table) {
        $foundTables[] = $table['TABLE_NAME'];
    }
    
    $missingTables = array_diff($expectedTables, $foundTables);
    $extraTables = array_diff($foundTables, $expectedTables);
    
    echo "Expected tables: " . count($expectedTables) . "\n";
    echo "Found tables: " . count($foundTables) . "\n\n";
    
    if (empty($missingTables) && empty($extraTables)) {
        echo "✓ All tables are present and correct!\n\n";
    } else {
        if (!empty($missingTables)) {
            echo "❌ MISSING TABLES:\n";
            foreach ($missingTables as $table) {
                echo "   - $table\n";
            }
            echo "\n";
        }
        
        if (!empty($extraTables)) {
            echo "⚠ EXTRA TABLES (not in schema):\n";
            foreach ($extraTables as $table) {
                echo "   - $table\n";
            }
            echo "\n";
        }
    }
    
    // Check table details
    echo "📋 DETAILED TABLE INFORMATION:\n";
    echo str_repeat("-", 60) . "\n";
    
    foreach ($expectedTables as $table) {
        if (in_array($table, $foundTables)) {
            $result = $db->query("SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '" . DB_NAME . "' AND TABLE_NAME = '$table' ORDER BY ORDINAL_POSITION")->fetchAll(PDO::FETCH_ASSOC);
            $rowCount = $db->query("SELECT COUNT(*) as count FROM $table")->fetch(PDO::FETCH_ASSOC)['count'];
            
            echo "\n✓ $table\n";
            echo "   Columns: " . count($result) . " | Rows: " . $rowCount . "\n";
            echo "   Columns:\n";
            foreach ($result as $col) {
                $nullable = $col['IS_NULLABLE'] === 'YES' ? 'NULL' : 'NOT NULL';
                echo "     - {$col['COLUMN_NAME']}: {$col['COLUMN_TYPE']} ($nullable)\n";
            }
        } else {
            echo "\n❌ $table - NOT FOUND\n";
        }
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    
    // Summary
    if (empty($missingTables) && empty($extraTables)) {
        echo "✅ DATABASE IS FULLY SYNCHRONIZED\n";
    } else {
        echo "⚠ DATABASE IS NOT FULLY SYNCHRONIZED\n";
        echo "\nTo fix missing tables, run: mysql -u " . DB_USER . " " . DB_NAME . " < database.sql\n";
    }
    
} catch (PDOException $e) {
    echo "❌ DATABASE CONNECTION FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "1. MySQL server is running on port 3306\n";
    echo "2. Database '" . DB_NAME . "' exists\n";
    echo "3. User '" . DB_USER . "' has proper permissions\n";
}
?>
