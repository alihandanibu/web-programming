<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *   title="Handan Portfolio API",
 *   version="1.0.0",
 *   description="IBU Web Programming – SPA Portfolio Backend"
 * )
 *
 * @OA\Server(
 *   url="http://localhost/mojnoviprojekat/web-programming/backend",
 *   description="Apache/XAMPP (project path)"
 * )
 *
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   bearerFormat="JWT",
 *   description="Paste token as: Bearer {token}"
 * )
 */
class OpenApiSpec {}