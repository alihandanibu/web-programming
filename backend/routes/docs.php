<?php

// Serve Swagger UI
Flight::route('GET /v1/docs', function () {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Content-Type: text/html; charset=utf-8');
    readfile(__DIR__ . '/../app/docs/swagger.html');
});

// Serve OpenAPI JSON
Flight::route('GET /v1/openapi', function () {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Content-Type: application/json');
    readfile(__DIR__ . '/../app/docs/openapi.json');
});
