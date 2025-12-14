<?php

namespace app\services;

use app\dao\UserDAO;
use app\middleware\AuthMiddleware;

class UserService {

    private UserDAO $userDAO;
    private AuthMiddleware $authMiddleware;

    public function __construct() {
        $this->userDAO = new UserDAO();
        $this->authMiddleware = new AuthMiddleware();
    }

    /* =========================
       REGISTER
    ========================== */
    public function register(array $data): array {
        if (
            empty($data['name']) ||
            empty($data['email']) ||
            empty($data['password'])
        ) {
            return [
                'success' => false,
                'message' => 'Name, email and password are required'
            ];
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format'
            ];
        }

        // default role = user
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'role' => $data['role'] ?? 'user',
            'bio' => $data['bio'] ?? null,
            'location' => $data['location'] ?? null
        ];

        $userId = $this->userDAO->create($userData);

        if ($userId) {
            return [
                'success' => true,
                'message' => 'User registered successfully',
                'user_id' => $userId
            ];
        }

        return [
            'success' => false,
            'message' => 'Registration failed'
        ];
    }

    /* =========================
       LOGIN
    ========================== */
    public function login(string $email, string $password): array {
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
            $user['role']
        );

        return [
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => (int)$user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ];
    }

    /* =========================
       GET USER BY ID
    ========================== */
    public function getUserById(int $userId): array {
        $user = $this->userDAO->read($userId);

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
        $users = $this->userDAO->readAll();

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
    ========================== */
    public function updateUser(int $userId, array $data): array {
        $user = $this->userDAO->read($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $updated = $this->userDAO->update($userId, $data);

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
        $deleted = $this->userDAO->delete($userId);

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