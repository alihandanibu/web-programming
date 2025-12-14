<?php
namespace app\middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Flight;

class AuthMiddleware {

    private string $secretKey;
    private string $algorithm = 'HS256';

    public function __construct() {
        // Load secret key from config
        $this->secretKey = $_ENV['JWT_SECRET'] ?? 'dev_secret_change_me';
    }

    // Generate JWT token
    public function generateToken(int $userId, string $role): string {
        $issuedAt = time();
        $expire = $issuedAt + 3600; // 1 hour

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'user_id' => $userId,
            'role' => $role
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    // Check if user is authenticated
    public function requireAuth(): void {
        $token = $this->extractToken();

        if (!$token) {
            Flight::json(['error' => 'Missing token'], 401);
            exit;
        }

        try {
            $decoded = JWT::decode(
                $token,
                new Key($this->secretKey, $this->algorithm)
            );

            // Store authenticated user globally
            Flight::set('user', (array) $decoded);

        } catch (\Exception $e) {
            Flight::json(['error' => 'Invalid or expired token'], 401);
            exit;
        }
    }

    /* =========================
       ADMIN CHECK
    ========================== */
    public function requireAdmin(): void {
        $user = Flight::get('user');

        if (!$user || !isset($user['role']) || $user['role'] !== 'admin') {
            Flight::json(['error' => 'Forbidden'], 403);
            exit;
        }
    }

    /* =========================
       TOKEN EXTRACTOR
    ========================== */
    private function extractToken(): ?string {
        $headers = getallheaders();

        if (isset($headers['Authorization'])) {
            $parts = explode(' ', $headers['Authorization'], 2);
            if (count($parts) === 2 && $parts[0] === 'Bearer') {
                return $parts[1];
            }
        }

        return null;
    }
}