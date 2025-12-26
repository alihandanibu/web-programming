<?php

class Database {
    private ?PDO $conn = null;

    // Helper function to get environment variable with fallback
    private static function get_env(string $name, string $default): string {
        return isset($_ENV[$name]) && trim($_ENV[$name]) !== '' ? $_ENV[$name] : $default;
    }

    private static function env_truthy(string $name): bool {
        $val = strtolower(trim((string)($_ENV[$name] ?? '')));
        return in_array($val, ['1', 'true', 'yes', 'on', 'required'], true);
    }

    // Configuration getters using environment variables
    public static function DB_HOST(): string {
        return self::get_env('DB_HOST', '127.0.0.1');
    }

    public static function DB_PORT(): string {
        return self::get_env('DB_PORT', '3307');
    }

    public static function DB_NAME(): string {
        return self::get_env('DB_NAME', 'portfolio');
    }

    public static function DB_USER(): string {
        return self::get_env('DB_USER', 'root');
    }

    public static function DB_PASSWORD(): string {
        return self::get_env('DB_PASSWORD', '');
    }

    // Some managed MySQL providers require SSL/TLS.
    // Set DB_SSL=true to enable SSL for PDO MySQL.
    public static function DB_SSL(): bool {
        return self::env_truthy('DB_SSL');
    }

    // Optional path to CA certificate file for SSL.
    public static function DB_SSL_CA(): string {
        return self::get_env('DB_SSL_CA', '');
    }

    public static function JWT_SECRET(): string {
        return self::get_env('JWT_SECRET', 'your-super-secret-jwt-key-change-in-production');
    }

    public function getConnection(): PDO {
        if ($this->conn instanceof PDO) {
            return $this->conn;
        }

        $host = self::DB_HOST();
        $port = self::DB_PORT();
        $db_name = self::DB_NAME();
        $username = self::DB_USER();
        $password = self::DB_PASSWORD();

        $dsn = "mysql:host={$host};port={$port};dbname={$db_name};charset=utf8mb4";

        try {
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            if (self::DB_SSL()) {
                // Allow SSL without strict verification by default.
                // If DB_SSL_CA is provided and exists on disk, PDO will use it.
                if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
                    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
                }

                $caPath = trim(self::DB_SSL_CA());
                if ($caPath !== '' && file_exists($caPath) && defined('PDO::MYSQL_ATTR_SSL_CA')) {
                    $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
                }
            }

            $this->conn = new PDO($dsn, $username, $password, $options);
            return $this->conn;
        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed.");
        }
    }
}