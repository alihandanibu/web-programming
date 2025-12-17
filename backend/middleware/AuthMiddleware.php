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
            Flight::jsonHalt(['error' => 'Missing token'], 401);
        }

        try {
            $decoded = JWT::decode(
                $token,
                new Key($this->secretKey, $this->algorithm)
            );

            // Store authenticated user globally
            Flight::set('user', (array) $decoded);

        } catch (\Exception $e) {
            Flight::jsonHalt(['error' => 'Invalid or expired token'], 401);
        }
    }

    /* =========================
       ADMIN CHECK
    ========================== */
    public function requireAdmin(): void {
        $user = Flight::get('user');

        if (!$user || !isset($user['role']) || $user['role'] !== 'admin') {
            Flight::jsonHalt(['error' => 'Forbidden'], 403);
        }
    }

    /* =========================
       TOKEN EXTRACTOR
    ========================== */
    private function extractToken(): ?string {
        $headers = function_exists('getallheaders') ? getallheaders() : [];

        // Some environments use different header keys
        $authHeader =
            $headers['Authorization'] ??
            $headers['authorization'] ??
            ($_SERVER['HTTP_AUTHORIZATION'] ?? null) ??
            ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null);

        if (!$authHeader || !is_string($authHeader)) {
            return null;
        }

        $authHeader = trim($authHeader);
        $parts = preg_split('/\s+/', $authHeader, 2);

        if (count($parts) === 2 && strcasecmp($parts[0], 'Bearer') === 0) {
            return trim($parts[1]);
        }

        return null;
    }
}