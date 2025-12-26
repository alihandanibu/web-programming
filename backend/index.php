// Redirect /docs to /v1/docs for compatibility
Flight::route('/docs', function () {
    header('Location: /v1/docs');
    exit;
});
<?php

require_once __DIR__ . '/vendor/autoload.php';

// XAMPP base path
$baseUrl = '';

$docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
$backendDir = realpath(__DIR__);
if ($docRoot && $backendDir) {
    $docRootNorm = rtrim(str_replace('\\', '/', $docRoot), '/');
    $backendNorm = str_replace('\\', '/', $backendDir);
    if ($docRootNorm !== '' && str_starts_with($backendNorm, $docRootNorm)) {
        $baseUrl = substr($backendNorm, strlen($docRootNorm));
        $baseUrl = '/' . ltrim($baseUrl, '/');
        if ($baseUrl === '/') {
            $baseUrl = '';
        }
    }
}

if ($baseUrl === '') {
    $scriptName = str_replace('\\', '/', ($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($scriptName !== '' && str_ends_with($scriptName, '.php')) {
        $baseUrl = rtrim(dirname($scriptName), '/');
    } else {
        $baseUrl = rtrim($scriptName, '/');
    }
    if ($baseUrl === '/' || $baseUrl === '.') {
        $baseUrl = '';
    }
}

Flight::set('flight.base_url', $baseUrl);

$request = Flight::request();
$request->base = $baseUrl;

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if (!is_string($requestPath) || $requestPath === '') {
    $requestPath = '/';
}
if ($requestPath[0] !== '/') {
    $requestPath = '/' . $requestPath;
}

if ($baseUrl !== '' && strpos($requestPath, $baseUrl) === 0) {
    $requestPath = substr($requestPath, strlen($baseUrl));
    if ($requestPath === '') {
        $requestPath = '/';
    }
    if ($requestPath[0] !== '/') {
        $requestPath = '/' . $requestPath;
    }
}

$request->url = $requestPath;
/*
 |------------------------------------------------------------
 | CORS
 |------------------------------------------------------------
 | For Milestone 4 this permissive setup prevents "CORS blocked"
 | errors during development/testing.
 */
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigins = [
    'http://localhost',
    'http://127.0.0.1',
    'http://localhost:80',
    'http://127.0.0.1:80',
];

if ($origin && in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: {$origin}");
    header('Vary: Origin');
} else {
    header('Access-Control-Allow-Origin: *');
}

header('Access-Control-Allow-Headers: Authorization, Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

Flight::map('notFound', function () {
    Flight::json(['success' => false, 'error' => 'Not Found'], 404);
});

Flight::map('error', function (\Throwable $ex) {
    // Log the real error server-side; return a safe message to the client
    error_log($ex);
    Flight::json(['success' => false, 'error' => 'Server error'], 500);
});

Flight::register('UserService', 'app\\services\\UserService');
Flight::register('SkillService', 'app\\services\\SkillService');
Flight::register('ExperienceService', 'app\\services\\ExperienceService');
Flight::register('ProjectService', 'app\\services\\ProjectService');
Flight::register('ContactService', 'app\\services\\ContactService');
Flight::register('AuthMiddleware', 'app\\middleware\\AuthMiddleware');

require_once __DIR__ . '/routes/AuthRoutes.php';
require_once __DIR__ . '/routes/UserRoutes.php';
require_once __DIR__ . '/routes/SkillRoutes.php';
require_once __DIR__ . '/routes/ExperienceRoutes.php';
require_once __DIR__ . '/routes/ProjectRoutes.php';
require_once __DIR__ . '/routes/ContactRoutes.php';
require_once __DIR__ . '/routes/SystemRoutes.php';


// Serve Swagger UI for /v1/docs and /v1/docs/
Flight::route('/v1/docs', function () {
    $docsIndex = __DIR__ . '/public/v1/docs/index.html';
    if (file_exists($docsIndex)) {
        header('Content-Type: text/html; charset=utf-8');
        readfile($docsIndex);
        exit;
    } else {
        Flight::json(['success' => false, 'error' => 'Swagger UI not found'], 404);
    }
});
Flight::route('/v1/docs/', function () {
    $docsIndex = __DIR__ . '/public/v1/docs/index.html';
    if (file_exists($docsIndex)) {
        header('Content-Type: text/html; charset=utf-8');
        readfile($docsIndex);
        exit;
    } else {
        Flight::json(['success' => false, 'error' => 'Swagger UI not found'], 404);
    }
});

Flight::route('/api/docs.json', function () {
    require_once __DIR__ . '/public/v1/docs/swagger.php';
});

// Start the app
Flight::start();