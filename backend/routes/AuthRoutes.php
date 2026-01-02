<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/auth/register",
 *     summary="Register a new user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="user_id", type="integer")
 *         )
 *     )
 * )
 */
\Flight::post('/auth/register', function() {
    $input = \Flight::request()->data->getData();
    $userService = \Flight::UserService();
    $result = $userService->register($input);
    \Flight::json($result);
});

/**
 * @OA\Post(
 *     path="/auth/login",
 *     summary="Login user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="token", type="string"),
 *             @OA\Property(property="user", type="object")
 *         )
 *     )
 * )
 */
\Flight::post('/auth/login', function() {
    $input = \Flight::request()->data->getData();

    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    if (empty($email) || empty($password)) {
        \Flight::json(['success' => false, 'message' => 'Email and password are required'], 400);
        return;
    }

    $userService = \Flight::UserService();
    $result = $userService->login((string)$email, (string)$password);
    \Flight::json($result, $result['success'] ? 200 : 401);
});

/**
 * @OA\Post(
 *     path="/auth/verify",
 *     summary="Verify JWT token",
 *     tags={"Authentication"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Token is valid",
 *         @OA\JsonContent(
 *             @OA\Property(property="valid", type="boolean"),
 *             @OA\Property(property="user_id", type="integer")
 *         )
 *     )
 * )
 */
\Flight::post('/auth/verify', function() {
    $auth = \Flight::AuthMiddleware();
    $auth->requireAuth();

    $user = \Flight::get('user');
    \Flight::json([
        'valid' => true,
        'user' => $user
    ]);
});
?>
