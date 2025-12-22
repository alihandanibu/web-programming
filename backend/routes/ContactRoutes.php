<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/contact",
 *     summary="Submit contact form (public)",
 *     tags={"Contact"}
 * )
 */
Flight::post('/contact', function () {
    $data = Flight::request()->data->getData();
    $service = Flight::ContactService();
    Flight::json($service->submitContact($data));
});


/**
 * @OA\Get(
 *     path="/users/{userId}/contacts",
 *     summary="Get contacts (admin only)",
 *     tags={"Contact"},
 *     security={{"bearerAuth":{}}}
 * )
 */
Flight::route('GET /users/@userId/contacts', function ($userId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();
    $auth->requireAdmin();

    $service = Flight::ContactService();
    Flight::json($service->getContactsByUser((int)$userId));
});


/**
 * @OA\Delete(
 *     path="/users/{userId}/contacts/{contactId}",
 *     summary="Delete contact (admin only)",
 *     tags={"Contact"},
 *     security={{"bearerAuth":{}}}
 * )
 */
Flight::delete('/users/@userId/contacts/@contactId', function ($userId, $contactId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();
    $auth->requireAdmin();

    $service = Flight::ContactService();
    Flight::json($service->deleteContact((int)$userId, (int)$contactId));
});