<?php
/**
 * JUANDirectory Framework - Standalone Bootstrap
 * No Composer required - works out of the box!
 *
 * @package JUANDirectory
 * @version 2.0.0
 */

// Define directory constants
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('ROOTDIR')) {
    define('ROOTDIR', dirname(__DIR__) . DS);
}

if (!defined('SRCDIR')) {
    define('SRCDIR', ROOTDIR . 'src' . DS);
}

if (!defined('CONFIGDIR')) {
    define('CONFIGDIR', ROOTDIR . 'config' . DS);
}

if (!defined('PUBLICDIR')) {
    define('PUBLICDIR', ROOTDIR . 'public' . DS);
}

// Load autoloader
require_once SRCDIR . 'Autoloader.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Create logs directory if it doesn't exist
$logsDir = ROOTDIR . 'logs';
if (!is_dir($logsDir)) {
    @mkdir($logsDir, 0755, true);
}

ini_set('error_log', $logsDir . DS . 'error.log');

// Set timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $message = sprintf(
        "[%s] %s in %s on line %d",
        date('Y-m-d H:i:s'),
        $errstr,
        $errfile,
        $errline
    );
    error_log($message);
});

// Custom exception handler
set_exception_handler(function($exception) {
    $message = sprintf(
        "[%s] Exception: %s in %s on line %d",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    );
    error_log($message);
    
    // Display error
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred. Please check logs.']);
});

// Shutdown handler for fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR])) {
        $message = sprintf(
            "[%s] Fatal: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $error['message'],
            $error['file'],
            $error['line']
        );
        error_log($message);
    }
});
