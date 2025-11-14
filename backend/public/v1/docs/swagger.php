<?php
require_once '/../../../vendor/autoload.php';

use OpenApi\Generator;

$openapi = Generator::scan([
    __DIR__ . '/../../..',
])->toJson();

header('Content-Type: application/json');
echo $openapi;
?>
