<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$openapi = \OpenApi\Generator::scan([
    __DIR__ . '/swagger.php',
    __DIR__ . '/../../../routes'
]);

header('Content-Type: application/json');
echo $openapi->toJson();