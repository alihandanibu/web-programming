<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

// Ensure warnings/notices don't corrupt JSON output
ini_set('display_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: application/json; charset=utf-8');

try {
	$openapi = \OpenApi\Generator::scan([
		__DIR__ . '/../../../OpenApi.php',
		__DIR__ . '/../../../ApiDocs.php'
	], [
		'analyser' => new \OpenApi\Analysers\TokenAnalyser()
	]);
	echo $openapi->toJson();
} catch (Throwable $e) {
	http_response_code(500);
	echo json_encode([
		'error' => 'Failed to generate OpenAPI spec',
		'message' => $e->getMessage()
	], JSON_PRETTY_PRINT);
}