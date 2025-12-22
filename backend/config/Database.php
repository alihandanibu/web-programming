<?php

class Database {
    private string $host = '127.0.0.1';
    private string $port = '3307';
    private string $db_name = 'portfolio';
    private string $username = 'root';
    private string $password = '';

    private ?PDO $conn = null;

    public function getConnection(): PDO {
        if ($this->conn instanceof PDO) {
            return $this->conn;
        }

        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            return $this->conn;
        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed.");
        }
    }
}