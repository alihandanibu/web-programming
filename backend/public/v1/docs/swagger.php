<?php

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *   openapi="3.0.0",
 *   @OA\Info(
 *     title="Handan Portfolio API",
 *     version="1.0.0",
 *     description="RESTful API za portfolio",
 *     @OA\Contact(email="alihandan@stu.ibu.edu.ba")
 *   ),
 *   @OA\Server(
 *     url="http://localhost/mojnoviprojekat/web-programming/backend",
 *     description="Lokalni razvojni server"
 *   )
 * )
 *
 * @OA\Get(
 *   path="/health",
 *   summary="Health check",
 *   tags={"System"},
 *   @OA\Response(response=200, description="API radi")
 * )
 *
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   bearerFormat="JWT",
 *   description="JWT Bearer token"
 * )
 */