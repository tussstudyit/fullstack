<?php
require_once __DIR__ . '/config.php';

try {
    $db = getDB();
    
    // Add deposit_amount column
    $db->exec("ALTER TABLE posts ADD COLUMN IF NOT EXISTS deposit_amount DECIMAL(10, 2) AFTER available_from");
    echo "✓ Added deposit_amount column\n";
    
    // Add electric_price column
    $db->exec("ALTER TABLE posts ADD COLUMN IF NOT EXISTS electric_price DECIMAL(10, 2) AFTER deposit_amount");
    echo "✓ Added electric_price column\n";
    
    // Add water_price column
    $db->exec("ALTER TABLE posts ADD COLUMN IF NOT EXISTS water_price DECIMAL(10, 2) AFTER electric_price");
    echo "✓ Added water_price column\n";
    
    echo "\nMigration completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
