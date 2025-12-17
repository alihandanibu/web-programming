<?php

namespace app\services;

use app\middleware\AuthMiddleware;

require_once __DIR__ . '/../dao/UserDAO.php';

class UserService {

    private \UserDAO $userDAO;
    private AuthMiddleware $authMiddleware;

    public function __construct() {
        $this->userDAO = new \UserDAO();
        $this->authMiddleware = new AuthMiddleware();
    }

    /* =========================
       REGISTER (PUBLIC)
       - Always creates a normal "user"
       - Prevents role escalation via request payload
    ========================== */
    public function register(array $data): array {
        $name = trim((string)($data['name'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $password = (string)($data['password'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            return [
                'success' => false,
                'message' => 'Name, email and password are required'
            ];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format'
            ];
        }

        // Email uniqueness check (better UX than a raw DB error)
        if ($this->userDAO->findByEmail($email)) {
            return [
                'success' => false,
                'message' => 'Email already exists'
            ];
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $userData = [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            // IMPORTANT: do NOT accept role from the client
            'role' => 'user'
        ];

        try {
            $userId = $this->userDAO->create($userData);
        } catch (\Throwable $e) {
            error_log($e);
            return [
                'success' => false,
                'message' => 'Registration failed'
            ];
        }

        if ($userId) {
            return [
                'success' => true,
                'message' => 'User registered successfully',
                'user_id' => (int)$userId
            ];
        }

        return [
            'success' => false,
            'message' => 'Registration failed'
        ];
    }

    /* =========================
       LOGIN (PUBLIC)
    ========================== */
    public function login(string $email, string $password): array {
        $email = trim($email);

        if ($email === '' || $password === '') {
            return [
                'success' => false,
                'message' => 'Email and password are required'
            ];
        }

        $user = $this->userDAO->findByEmail($email);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Invalid password'
            ];
        }

        // JWT with ROLE (Milestone 4 requirement)
        $token = $this->authMiddleware->generateToken(
            (int)$user['id'],
            (string)$user['role']
        );

        return [
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => (int)$user['id'],
                'name' => $user['name'] ?? '',
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ];
    }

    /* =========================
       GET USER BY ID
    ========================== */
    public function getUserById(int $userId): array {
        $user = $this->userDAO->findById($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        unset($user['password']);

        return [
            'success' => true,
            'user' => $user
        ];
    }

    /* =========================
       GET ALL USERS (ADMIN)
    ========================== */
    public function getAllUsers(): array {
        $users = $this->userDAO->findAll();

        foreach ($users as &$user) {
            unset($user['password']);
        }

        return [
            'success' => true,
            'users' => $users
        ];
    }

    /* =========================
       UPDATE USER
       - Owner can update: name/email/password
       - Admin can also update: role
    ========================== */
    public function updateUser(int $userId, array $data, bool $isAdmin = false): array {
        $user = $this->userDAO->findById($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        // Validate & normalize fields
        $updateData = [];

        if (array_key_exists('name', $data)) {
            $name = trim((string)$data['name']);
            if ($name === '') {
                return ['success' => false, 'message' => 'Name cannot be empty'];
            }
            $updateData['name'] = $name;
        }

        if (array_key_exists('email', $data)) {
            $email = trim((string)$data['email']);
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email format'];
            }

            $existing = $this->userDAO->findByEmail($email);
            if ($existing && (int)$existing['id'] !== (int)$userId) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            $updateData['email'] = $email;
        }

        if (!empty($data['password'])) {
            $updateData['password'] = password_hash((string)$data['password'], PASSWORD_BCRYPT);
        }

        // Role updates are ADMIN-only
        if ($isAdmin && array_key_exists('role', $data)) {
            $role = (string)$data['role'];
            if (!in_array($role, ['admin', 'user'], true)) {
                return ['success' => false, 'message' => 'Invalid role'];
            }
            $updateData['role'] = $role;
        }

        if (empty($updateData)) {
            return [
                'success' => false,
                'message' => 'No valid fields to update'
            ];
        }

        try {
            $updated = $this->userDAO->update($userId, $updateData);
        } catch (\Throwable $e) {
            error_log($e);
            return [
                'success' => false,
                'message' => 'Update failed'
            ];
        }

        if ($updated) {
            return [
                'success' => true,
                'message' => 'User updated successfully'
            ];
        }

        return [
            'success' => false,
            'message' => 'Update failed'
        ];
    }

    /* =========================
       DELETE USER (ADMIN)
    ========================== */
    public function deleteUser(int $userId): array {
        try {
            $deleted = $this->userDAO->delete($userId);
        } catch (\Throwable $e) {
            error_log($e);
            return [
                'success' => false,
                'message' => 'Delete failed'
            ];
        }

        if ($deleted) {
            return [
                'success' => true,
                'message' => 'User deleted successfully'
            ];
        }

        return [
            'success' => false,
            'message' => 'Delete failed'
        ];
    }
}
