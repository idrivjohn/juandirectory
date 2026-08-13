<?php
/**
 * JUANDirectory Framework - Bootstrap
 *
 * @package JUANDirectory
 * @author John Virdi V. Alfonso
 * @license MIT
 * @version 2.0.0
 */

// Load Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Define directory constants
define('DS', DIRECTORY_SEPARATOR);
define('ROOTDIR', dirname(__DIR__) . DS);
define('SRCDIR', ROOTDIR . 'src' . DS);
define('CONFIGDIR', ROOTDIR . 'config' . DS);
define('PUBLICDIR', ROOTDIR . 'public' . DS);

// Error and exception handling
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', ROOTDIR . 'logs' . DS . 'error.log');

// Set default timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log(sprintf(
        "[%s] %s in %s on line %d",
        date('Y-m-d H:i:s'),
        $errstr,
        $errfile,
        $errline
    ));

    if (defined('WP_DEBUG') && WP_DEBUG) {
        echo htmlspecialchars($errstr, ENT_QUOTES, 'UTF-8');
    }
});

// Custom exception handler
set_exception_handler(function($exception) {
    error_log(sprintf(
        "[%s] %s in %s on line %d",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    ));
});

// Shutdown handler
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR])) {
        error_log(sprintf(
            "[%s] Fatal Error: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $error['message'],
            $error['file'],
            $error['line']
        ));
    }
});

// Session handling
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
