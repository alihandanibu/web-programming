<?php
require_once __DIR__ . '/../config/Database.php';

$db = (new Database())->getConnection();

$email = $argv[1] ?? 'admin@portfolio.com';
$newPassword = $argv[2] ?? 'password';

$hash = password_hash($newPassword, PASSWORD_BCRYPT);

$stmt = $db->prepare('UPDATE users SET password = ? WHERE email = ?');
$stmt->execute([$hash, $email]);

echo 'UPDATED_ROWS=' . $stmt->rowCount() . PHP_EOL;
