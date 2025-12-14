<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *   path="/users/{userId}/experiences",
 *   summary="Lista iskustava",
 *   tags={"Experiences"},
 *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 * @OA\Post(
 *   path="/users/{userId}/experiences",
 *   summary="Dodaj iskustvo",
 *   tags={"Experiences"},
 *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="Kreirano")
 * )
 * @OA\Put(
 *   path="/users/{userId}/experiences/{id}",
 *   summary="Ažuriraj iskustvo",
 *   tags={"Experiences"},
 *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="Ažurirano")
 * )
 * @OA\Delete(
 *   path="/users/{userId}/experiences/{id}",
 *   summary="Obriši iskustvo",
 *   tags={"Experiences"},
 *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="Obrisano")
 * )
 */

/**
 * @OA\Get(
 *     path="/users/{userId}/experiences",
 *     summary="Get experiences (owner or admin)",
 *     tags={"Experiences"},
 *     security={{"bearerAuth":{}}}
 * )
 */
Flight::get('/users/@userId/experiences', function ($userId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    if ($currentUser['role'] !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $service = Flight::ExperienceService();
    Flight::json($service->getExperienceByUser((int)$userId));
});


Flight::post('/users/@userId/experiences', function ($userId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    if ($currentUser['role'] !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $data = Flight::request()->data->getData();
    $service = Flight::ExperienceService();
    Flight::json($service->addExperience((int)$userId, $data));
});


Flight::put('/users/@userId/experiences/@experienceId', function ($userId, $experienceId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    if ($currentUser['role'] !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $data = Flight::request()->data->getData();
    $service = Flight::ExperienceService();
    Flight::json($service->updateExperience((int)$userId, (int)$experienceId, $data));
});


Flight::delete('/users/@userId/experiences/@experienceId', function ($userId, $experienceId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();

    $currentUser = Flight::get('user');
    if ($currentUser['role'] !== 'admin' && $currentUser['user_id'] != $userId) {
        Flight::json(['error' => 'Forbidden'], 403);
        return;
    }

    $service = Flight::ExperienceService();
    Flight::json($service->deleteExperience((int)$userId, (int)$experienceId));
});