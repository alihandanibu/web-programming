<?php
// Enable CORS for AJAX requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: text/html; charset=UTF-8');

// Get the requested file
$view = isset($_GET['view']) ? $_GET['view'] : 'home.html';

// Remove any directory traversal attempts
$view = str_replace(['../', '..\\'], '', $view);
$view = basename($view);

// Ensure .html extension
if (!str_ends_with($view, '.html')) {
    $view .= '.html';
}

$file = __DIR__ . '/views/' . $view;

// Check if file exists
if (!file_exists($file)) {
    http_response_code(404);
    echo '<section id="error_404" class="section-padding text-center">
      <div class="container">
        <h2 class="display-4">404 - Page Not Found</h2>
        <p class="lead">The requested view "' . htmlspecialchars($view) . '" does not exist.</p>
        <a href="#home" class="btn btn-primary">Go Home</a>
      </div>
    </section>';
    exit;
}

// Load and output the view
echo file_get_contents($file);

