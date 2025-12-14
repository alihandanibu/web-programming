<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *   path="/users/{userId}/skills",
 *   summary="Lista vještina",
 *   tags={"Skills"},
 *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Post(
 *   path="/users/{userId}/skills",
 *   summary="Dodaj vještinu",
 *   tags={"Skills"},
 *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="Kreirano")
 * )
 * @OA\Put(
 *   path="/users/{userId}/skills/{id}",
 *   summary="Ažuriraj vještinu",
 *   tags={"Skills"},
 *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="Ažurirano")
 * )
 * @OA\Delete(
 *   path="/users/{userId}/skills/{id}",
 *   summary="Obriši vještinu",
 *   tags={"Skills"},
 *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="Obrisano")
 * )
 */

/**
 * @OA\Get(
 *     path="/users/{userId}/skills",
 *     summary="Get skills (owner or admin)",
 *     tags={"Skills"},
 *     security={{"bearerAuth":{}}}
 * )
 */
Flight::get('/users/@userId/skills', function ($userId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    if ($currentUser['role'] !== 'admin' && $currentUser['user_id'] != $userId) {
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
 *     security={{"bearerAuth":{}}}
 * )
 */
Flight::post('/users/@userId/skills', function ($userId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    if ($currentUser['role'] !== 'admin' && $currentUser['user_id'] != $userId) {
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
 *     security={{"bearerAuth":{}}}
 * )
 */
Flight::put('/users/@userId/skills/@skillId', function ($userId, $skillId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    if ($currentUser['role'] !== 'admin' && $currentUser['user_id'] != $userId) {
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
 *     security={{"bearerAuth":{}}}
 * )
 */
Flight::delete('/users/@userId/skills/@skillId', function ($userId, $skillId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    if ($currentUser['role'] !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $service = Flight::SkillService();
    Flight::json($service->deleteSkill((int)$userId, (int)$skillId));
});