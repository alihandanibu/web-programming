<?php

// Load composer libraries
require_once __DIR__ . '/vendor/autoload.php';

// Register services
Flight::register('UserService', 'app\\services\\UserService');
Flight::register('SkillService', 'app\\services\\SkillService');
Flight::register('ExperienceService', 'app\\services\\ExperienceService');
Flight::register('ProjectService', 'app\\services\\ProjectService');
Flight::register('ContactService', 'app\\services\\ContactService');
Flight::register('AuthMiddleware', 'app\\middleware\\AuthMiddleware');

// Include routes
require_once __DIR__ . '/routes/AuthRoutes.php';
require_once __DIR__ . '/routes/UserRoutes.php';
require_once __DIR__ . '/routes/SkillRoutes.php';
require_once __DIR__ . '/routes/ExperienceRoutes.php';
require_once __DIR__ . '/routes/ProjectRoutes.php';
require_once __DIR__ . '/routes/ContactRoutes.php';

// API documentation routes
Flight::route('/docs', function () {
    header('Location: /mojnoviprojekat/web-programming/backend/public/v1/docs/');
    exit;
});

Flight::route('/api/docs.json', function () {
    require_once __DIR__ . '/public/v1/docs/swagger.php';
});

// Start the app
Flight::start();