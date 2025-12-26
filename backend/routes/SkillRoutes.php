<?php

use OpenApi\Annotations as OA;


/**
 * @OA\Get(
 *     path="/users/{userId}/skills",
 *     summary="Get skills (owner or admin)",
 *     tags={"Skills"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer"))
 * )
 */
Flight::route('GET /users/@userId/skills', function ($userId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    $currentRole = strtolower(trim((string)($currentUser['role'] ?? '')));
    if ($currentRole !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $service = Flight::SkillService();
    Flight::json($service->getSkillsByUser((int)$userId));
});


/**
 * @OA\Post(
 *     path="/users/{userId}/skills",
 *     summary="Add skill (owner or admin)",
 *     tags={"Skills"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer"))
 * )
 */
Flight::post('/users/@userId/skills', function ($userId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    $currentRole = strtolower(trim((string)($currentUser['role'] ?? '')));
    if ($currentRole !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $data = Flight::request()->data->getData();
    $service = Flight::SkillService();
    Flight::json($service->addSkill((int)$userId, $data));
});


/**
 * @OA\Put(
 *     path="/users/{userId}/skills/{skillId}",
 *     summary="Update skill (owner or admin)",
 *     tags={"Skills"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Parameter(name="skillId", in="path", required=true, @OA\Schema(type="integer"))
 * )
 */
Flight::put('/users/@userId/skills/@skillId', function ($userId, $skillId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    $currentRole = strtolower(trim((string)($currentUser['role'] ?? '')));
    if ($currentRole !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $data = Flight::request()->data->getData();
    $service = Flight::SkillService();
    Flight::json($service->updateSkill((int)$userId, (int)$skillId, $data));
});


/**
 * @OA\Delete(
 *     path="/users/{userId}/skills/{skillId}",
 *     summary="Delete skill (owner or admin)",
 *     tags={"Skills"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Parameter(name="skillId", in="path", required=true, @OA\Schema(type="integer"))
 * )
 */
Flight::delete('/users/@userId/skills/@skillId', function ($userId, $skillId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    $currentRole = strtolower(trim((string)($currentUser['role'] ?? '')));
    if ($currentRole !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $service = Flight::SkillService();
    Flight::json($service->deleteSkill((int)$userId, (int)$skillId));
});