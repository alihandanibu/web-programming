<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/contact",
 *     summary="Submit contact form (public)",
 *     tags={"Contact"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"name","email","message"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="subject", type="string", example="Project inquiry"),
 *             @OA\Property(property="message", type="string", example="Hi, I'd like to discuss a project...")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Submitted"),
 *     @OA\Response(response=400, description="Validation error")
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
 *     summary="Get contacts (admin only). Optional ?status=unread|read|replied filter",
 *     tags={"Contact"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         description="User ID (kept for route compatibility; contacts are global)",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=false,
 *         description="Optional status filter",
 *         @OA\Schema(type="string", enum={"unread","read","replied"})
 *     ),
 *     @OA\Response(response=200, description="List of contacts"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
Flight::route('GET /users/@userId/contacts', function ($userId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();
    $auth->requireAdmin();

    $status = Flight::request()->query['status'] ?? null;
    $service = Flight::ContactService();
    Flight::json($service->getContactsByUser((int)$userId, $status));
});


/**
 * @OA\Patch(
 *     path="/users/{userId}/contacts/{contactId}/status",
 *     summary="Update contact status (admin only)",
 *     tags={"Contact"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         description="User ID (kept for route compatibility; contacts are global)",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="contactId",
 *         in="path",
 *         required=true,
 *         description="Contact ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"status"},
 *             @OA\Property(property="status", type="string", enum={"unread","read","replied"}, example="read")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Updated"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
Flight::route('PATCH /users/@userId/contacts/@contactId/status', function ($userId, $contactId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();
    $auth->requireAdmin();

    $data = Flight::request()->data->getData();
    $status = $data['status'] ?? null;

    $service = Flight::ContactService();
    Flight::json($service->updateContactStatus((int)$contactId, $status));
});


/**
 * @OA\Delete(
 *     path="/users/{userId}/contacts/{contactId}",
 *     summary="Delete contact (admin only)",
 *     tags={"Contact"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         description="User ID (kept for route compatibility; contacts are global)",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="contactId",
 *         in="path",
 *         required=true,
 *         description="Contact ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Deleted"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
Flight::delete('/users/@userId/contacts/@contactId', function ($userId, $contactId) {
    $auth = Flight::AuthMiddleware();
    $auth->requireAuth();
    $auth->requireAdmin();

    $service = Flight::ContactService();
    Flight::json($service->deleteContact((int)$userId, (int)$contactId));
});