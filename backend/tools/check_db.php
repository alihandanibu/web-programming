<?php
require_once __DIR__ . '/../config/Database.php';

try {
    $db = (new Database())->getConnection();
    $row = $db->query("SELECT 1 AS ok")->fetch(PDO::FETCH_ASSOC);
    echo "DB connection OK: " . ($row['ok'] ?? 'no') . PHP_EOL;

    $dbName = $db->query("SELECT DATABASE() AS db")->fetch(PDO::FETCH_ASSOC);
    echo "Current DB: " . ($dbName['db'] ?? 'unknown') . PHP_EOL;
} catch (Throwable $e) {
    echo "DB connection FAILED: " . $e->getMessage() . PHP_EOL;
    exit(1);
}