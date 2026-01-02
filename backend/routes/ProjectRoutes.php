<?php

use OpenApi\Annotations as OA;


/**
 * @OA\Get(
 *     path="/users/{userId}/projects",
 *     summary="Get projects for a user (owner or admin)",
 *     tags={"Projects"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="List of projects"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
Flight::route('GET /users/@userId/projects', function ($userId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    $currentRole = strtolower(trim((string)($currentUser['role'] ?? '')));
    if ($currentRole !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $service = Flight::ProjectService();
    Flight::json($service->getProjectsByUser((int)$userId));
});


/**
 * @OA\Post(
 *     path="/users/{userId}/projects",
 *     summary="Add project (owner or admin)",
 *     tags={"Projects"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"title"},
 *             @OA\Property(property="title", type="string", example="E-commerce Website"),
 *             @OA\Property(property="description", type="string", nullable=true, example="Full-stack e-commerce platform..."),
 *             @OA\Property(property="technologies", type="string", nullable=true, example="PHP,MySQL,JavaScript"),
 *             @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/image.png"),
 *             @OA\Property(property="project_url", type="string", nullable=true, example="https://example.com"),
 *             @OA\Property(property="link", type="string", nullable=true, description="Alias for project_url"),
 *             @OA\Property(property="github_url", type="string", nullable=true, example="https://github.com/user/repo")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Project added"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
Flight::post('/users/@userId/projects', function ($userId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    $currentRole = strtolower(trim((string)($currentUser['role'] ?? '')));
    if ($currentRole !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $data = Flight::request()->data->getData();
    $service = Flight::ProjectService();
    Flight::json($service->addProject((int)$userId, $data));
});


/**
 * @OA\Put(
 *     path="/users/{userId}/projects/{projectId}",
 *     summary="Update project (owner or admin)",
 *     tags={"Projects"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Parameter(name="projectId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="title", type="string", example="E-commerce Website"),
 *             @OA\Property(property="description", type="string", nullable=true, example="Updated description"),
 *             @OA\Property(property="technologies", type="string", nullable=true, example="PHP,MySQL"),
 *             @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/image.png"),
 *             @OA\Property(property="project_url", type="string", nullable=true, example="https://example.com"),
 *             @OA\Property(property="link", type="string", nullable=true, description="Alias for project_url"),
 *             @OA\Property(property="github_url", type="string", nullable=true, example="https://github.com/user/repo")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Updated"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
Flight::put('/users/@userId/projects/@projectId', function ($userId, $projectId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    $currentRole = strtolower(trim((string)($currentUser['role'] ?? '')));
    if ($currentRole !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $data = Flight::request()->data->getData();
    $service = Flight::ProjectService();
    Flight::json($service->updateProject((int)$userId, (int)$projectId, $data));
});


/**
 * @OA\Delete(
 *     path="/users/{userId}/projects/{projectId}",
 *     summary="Delete project (owner or admin)",
 *     tags={"Projects"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Parameter(name="projectId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Deleted"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
Flight::delete('/users/@userId/projects/@projectId', function ($userId, $projectId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    $currentRole = strtolower(trim((string)($currentUser['role'] ?? '')));
    if ($currentRole !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $service = Flight::ProjectService();
    Flight::json($service->deleteProject((int)$userId, (int)$projectId));
});