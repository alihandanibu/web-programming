<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/users",
 *     summary="Get all users (admin only)",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of all users",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(
 *                 property="users",
 *                 type="array",
 *                 @OA\Items(type="object")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden"
 *     )
 * )
 */
Flight::route('GET /users', function () {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();
    $auth->requireAdmin();

    $userService = Flight::UserService();
    Flight::json($userService->getAllUsers());
});


/**
 * @OA\Get(
 *     path="/users/{id}",
 *     summary="Get user by ID (owner or admin)",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User details",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="user", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden"
 *     )
 * )
 */
Flight::route('GET /users/@id', function ($id) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');

    if (
        $currentUser['role'] !== 'admin' &&
        (int)$currentUser['user_id'] !== (int)$id
    ) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $userService = Flight::UserService();
    Flight::json($userService->getUserById((int)$id));
});


/**
 * @OA\Put(
 *     path="/users/{id}",
 *     summary="Update user (owner or admin)",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="password", type="string", format="password"),
 *             @OA\Property(property="bio", type="string"),
 *             @OA\Property(property="location", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden"
 *     )
 * )
 */
Flight::put('/users/@id', function ($id) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');

    if (
        $currentUser['role'] !== 'admin' &&
        (int)$currentUser['user_id'] !== (int)$id
    ) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $data = Flight::request()->data->getData();
    $userService = Flight::UserService();
    $isAdmin = ($currentUser['role'] === 'admin');

    // Prevent role tampering for normal users
    if (!$isAdmin) {
        unset($data['role']);
    }

    Flight::json($userService->updateUser((int)$id, $data, $isAdmin));
});


/**
 * @OA\Delete(
 *     path="/users/{id}",
 *     summary="Delete user (admin only)",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden"
 *     )
 * )
 */
Flight::delete('/users/@id', function ($id) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();
    $auth->requireAdmin();

    $userService = Flight::UserService();
    Flight::json($userService->deleteUser((int)$id));
});