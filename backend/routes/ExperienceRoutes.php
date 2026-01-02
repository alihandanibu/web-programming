<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/users/{userId}/experiences",
 *     summary="Get experiences (owner or admin)",
 *     tags={"Experiences"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="List of experiences"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
Flight::route('GET /users/@userId/experiences', function ($userId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    $currentRole = strtolower(trim((string)($currentUser['role'] ?? '')));
    if ($currentRole !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $service = Flight::ExperienceService();
    Flight::json($service->getExperienceByUser((int)$userId));
});

/**
 * @OA\Post(
 *     path="/users/{userId}/experiences",
 *     summary="Add experience (owner or admin)",
 *     tags={"Experiences"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent()),
 *     @OA\Response(response=200, description="Created"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
Flight::post('/users/@userId/experiences', function ($userId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    $currentRole = strtolower(trim((string)($currentUser['role'] ?? '')));
    if ($currentRole !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $data = Flight::request()->data->getData();
    $service = Flight::ExperienceService();
    Flight::json($service->addExperience((int)$userId, $data));
});

/**
 * @OA\Put(
 *     path="/users/{userId}/experiences/{experienceId}",
 *     summary="Update experience (owner or admin)",
 *     tags={"Experiences"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Parameter(name="experienceId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent()),
 *     @OA\Response(response=200, description="Updated"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
Flight::put('/users/@userId/experiences/@experienceId', function ($userId, $experienceId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    $currentRole = strtolower(trim((string)($currentUser['role'] ?? '')));
    if ($currentRole !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $data = Flight::request()->data->getData();
    $service = Flight::ExperienceService();
    Flight::json($service->updateExperience((int)$userId, (int)$experienceId, $data));
});

/**
 * @OA\Delete(
 *     path="/users/{userId}/experiences/{experienceId}",
 *     summary="Delete experience (owner or admin)",
 *     tags={"Experiences"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Parameter(name="experienceId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Deleted"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
Flight::delete('/users/@userId/experiences/@experienceId', function ($userId, $experienceId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    $currentRole = strtolower(trim((string)($currentUser['role'] ?? '')));
    if ($currentRole !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $service = Flight::ExperienceService();
    Flight::json($service->deleteExperience((int)$userId, (int)$experienceId));
});