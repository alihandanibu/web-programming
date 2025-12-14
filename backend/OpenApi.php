<?php

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *   openapi="3.0.0",
 *   @OA\Info(
 *     title="Handan Portfolio API",
 *     version="1.0.0",
 *     description="IBU Web Programming – SPA Portfolio Backend"
 *   ),
 *   @OA\Server(
 *     url="http://localhost/mojnoviprojekat/web-programming/backend",
 *     description="Local development server"
 *   )
 * )
 *
 * @OA\Get(
 *   path="/health",
 *   summary="Health check",
 *   tags={"System"},
 *   @OA\Response(
 *     response=200,
 *     description="API is alive"
 *   )
 * )
 */