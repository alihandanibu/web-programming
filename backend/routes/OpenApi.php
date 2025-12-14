<?php

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *   openapi="3.0.0",
 *   @OA\Info(
 *     title="Handan Portfolio API",
 *     version="1.0.0",
 *     description="Web Programming – REST API (Milestone 4)"
 *   )
 * )
 *
 * @OA\Get(
 *   path="/openapi-check",
 *   summary="OpenAPI check endpoint",
 *   tags={"System"},
 *   @OA\Response(response=200, description="OK")
 * )
 */