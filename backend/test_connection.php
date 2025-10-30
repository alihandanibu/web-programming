<?php
require_once 'config/Database.php';

$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    echo "✅ Database connection SUCCESSFUL!\n";
    
    // Test if we can query
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Found tables: " . implode(', ', $tables) . "\n";
} else {
    echo "❌ Database connection FAILED!\n";
}
?>