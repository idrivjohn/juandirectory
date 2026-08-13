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
/**\n * JUANDirectory - Entry Point\n * Public index.php - Standalone Implementation (No Composer)\n *\n * @package JUANDirectory\n * @version 2.0.0\n */\n\n// Load the standalone bootstrap\nrequire_once dirname(__DIR__) . '/src/standalone-bootstrap.php';\n\nuse JUANDirectory\\Core\\Application;\n\ntry {\n    // Load configuration\n    $config = require dirname(__DIR__) . '/config/app.php';\n    \n    // Create and run application\n    $app = new Application($config);\n    $app->run();\n} catch (Exception $e) {\n    http_response_code(500);\n    header('Content-Type: application/json');\n    echo json_encode([\n        'error' => $e->getMessage(),\n        'file' => $e->getFile(),\n        'line' => $e->getLine(),\n        'debug' => $_ENV['APP_DEBUG'] ?? false\n    ], JSON_PRETTY_PRINT);\n    exit(1);\n}\n
