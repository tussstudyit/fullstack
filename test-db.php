<?php
// Simple DB connectivity tester using config.php constants
require __DIR__ . '/config.php';

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "OK: Connected to database '" . DB_NAME . "' as '" . DB_USER . "'.\n";
} catch (PDOException $e) {
    echo "ERROR: Connection failed: " . $e->getMessage() . "\n";
}

// Optional: show available routines count (if privileges allow)
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS c FROM information_schema.routines WHERE routine_schema = '" . addslashes(DB_NAME) . "'");
    $row = $stmt->fetch();
    echo "Routines in DB: " . ($row['c'] ?? 0) . "\n";
} catch (Exception $e) {
    // ignore if not permitted
}

?>
