<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *   path="/health",
 *   summary="Health check",
 *   tags={"System"},
 *   @OA\Response(response=200, description="API is alive")
 * )
 */
Flight::route('GET /health', function () {
    Flight::json([
        'status' => 'ok',
        'time' => date('c')
    ]);
});

