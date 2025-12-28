<?php

// Serve Swagger UI
Flight::route('GET /v1/docs', function () {
    header('Content-Type: text/html; charset=utf-8');
    readfile(__DIR__ . '/../app/docs/swagger.html');
});

// Serve OpenAPI JSON
Flight::route('GET /v1/openapi', function () {
    header('Content-Type: application/json');
    readfile(__DIR__ . '/../app/docs/openapi.json');
});
