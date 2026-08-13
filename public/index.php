<?php
/**
 * Public Entry Point
 *
 * @package JUANDirectory
 * @version 2.0.0
 */

// Bootstrap the application
require_once dirname(__DIR__) . '/src/bootstrap.php';

use JUANDirectory\Core\Application;

try {
    // Load application configuration
    $config = require dirname(__DIR__) . '/config/app.php';
    
    // Create application instance
    $app = new Application($config);
    
    // Run application
    $app->run();
} catch (Exception $e) {
    // Log error
    error_log(sprintf(
        "[%s] Application Error: %s in %s on line %d",
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
    
    // Display error to user
    http_response_code(500);
    header('Content-Type: application/json');
    
    echo json_encode([
        'error' => 'An error occurred. Please try again later.',
        'debug' => $_ENV['APP_DEBUG'] ? $e->getMessage() : null
    ], JSON_PRETTY_PRINT);
}
