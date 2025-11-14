<?php
require_once '/../vendor/autoload.php';

use Flight;
use app\services\UserService;
use app\services\SkillService;
use app\services\ExperienceService;
use app\services\ProjectService;
use app\services\ContactService;
use app\middleware\AuthMiddleware;

// Register services in Flight's DI container
Flight::register('UserService', UserService::class);
Flight::register('SkillService', SkillService::class);
Flight::register('ExperienceService', ExperienceService::class);
Flight::register('ProjectService', ProjectService::class);
Flight::register('ContactService', ContactService::class);
Flight::register('AuthMiddleware', AuthMiddleware::class);

// Load routes
require_once 'routes/AuthRoutes.php';
require_once 'routes/UserRoutes.php';
require_once 'routes/SkillRoutes.php';
require_once 'routes/ExperienceRoutes.php';
require_once 'routes/ProjectRoutes.php';
require_once 'routes/ContactRoutes.php';

// Swagger documentation routes
Flight::route('/docs', function() {
    header('Location: /mojnoviprojekat/web-programming/backend/public/v1/docs/');
    exit;
});

Flight::route('/api/docs.json', function() {
    require_once 'public/v1/docs/swagger.php';
});

Flight::start();
?>
