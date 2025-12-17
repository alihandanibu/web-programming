<?php
// Usage: php tools/make_hash.php "Test123!"
$password = $argv[1] ?? "Test123!";
echo password_hash($password, PASSWORD_BCRYPT) . PHP_EOL;