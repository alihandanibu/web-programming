<?php
require_once __DIR__ . '/../config/Database.php';

$db = (new Database())->getConnection();

$emails = ['admin@portfolio.com', 'user@portfolio.com', 'testadmin@example.com'];
$in = "'" . implode("','", array_map('addslashes', $emails)) . "'";

$sql = "SELECT id, name, email, role FROM users WHERE email IN ($in) ORDER BY id";
$rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    echo "No seed users found. Did you import sql/seed_users.sql ?" . PHP_EOL;
    exit(1);
}

foreach ($rows as $r) {
    echo "{$r['id']} | {$r['name']} | {$r['email']} | {$r['role']}" . PHP_EOL;
}