<?php

use OpenApi\Annotations as OA;


/**
 * @OA\Get(
 *     path="/users/{userId}/skills",
 *     summary="Get skills (owner or admin)",
 *     tags={"Skills"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="List of skills"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden")
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
 *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"name","proficiency"},
 *             @OA\Property(property="name", type="string", example="PHP"),
 *             @OA\Property(property="proficiency", type="string", enum={"beginner","intermediate","advanced","expert"}, example="advanced"),
 *             @OA\Property(property="level", type="string", enum={"beginner","intermediate","advanced","expert"}, description="Alias for proficiency"),
 *             @OA\Property(property="category", type="string", example="Backend")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Created"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden")
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
 *     @OA\Parameter(name="skillId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="PHP"),
 *             @OA\Property(property="proficiency", type="string", enum={"beginner","intermediate","advanced","expert"}, example="expert"),
 *             @OA\Property(property="level", type="string", enum={"beginner","intermediate","advanced","expert"}, description="Alias for proficiency"),
 *             @OA\Property(property="category", type="string", example="Backend")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Updated"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden")
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
 *     @OA\Parameter(name="skillId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Deleted"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden")
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