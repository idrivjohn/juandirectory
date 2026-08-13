<?php
/**
 * JUANDirectory Framework - Application Core
 *
 * @package JUANDirectory\Core
 * @author John Virdi V. Alfonso
 * @license MIT
 * @version 2.0.0
 */

namespace JUANDirectory\Core;

use JUANDirectory\Core\Traits\RequestTrait;
use JUANDirectory\Core\Traits\ResponseTrait;
use JUANDirectory\Utilities\DeviceDetector;
use Exception;

/**
 * Main Application Class
 *
 * Serves as the central point for application initialization,
 * routing, and request handling.
 *
 * @category Framework
 * @package JUANDirectory\Core
 */
class Application extends Loader
{
    use RequestTrait;
    use ResponseTrait;

    /**
     * Default controller
     *
     * @var string
     */
    private string $defaultController = 'home';

    /**
     * Default action
     *
     * @var string
     */
    private string $defaultAction = 'index';

    /**
     * Application configuration
     *
     * @var array<string, mixed>
     */
    private array $config = [];

    /**
     * Service container
     *
     * @var array<string, mixed>
     */
    private array $services = [];

    /**
     * Device detector instance
     *
     * @var DeviceDetector|null
     */
    private ?DeviceDetector $deviceDetector = null;

    /**
     * Constructor
     *
     * @param array<string, mixed> $config Application configuration
     * @param string|null $rootPath Optional root path
     */
    public function __construct(array $config = [], ?string $rootPath = null)
    {
        parent::__construct($rootPath);
        $this->config = $config;
        $this->initialize();
    }

    /**
     * Initialize application
     *
     * @return void
     */
    private function initialize(): void
    {
        // Set error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', !$this->isProduction());

        // Set default timezone
        date_default_timezone_set($this->config['timezone'] ?? 'UTC');

        // Initialize device detector
        $this->deviceDetector = new DeviceDetector($this->getUserAgent());
    }

    /**
     * Run the application
     *
     * @return void
     * @throws Exception If routing fails
     */
    public function run(): void
    {
        try {
            $this->handleRequest();
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Handle incoming request
     *
     * @return void
     * @throws Exception
     */
    private function handleRequest(): void
    {
        $uri = $this->parseUri();
        $parts = array_filter(explode('/', $uri));
        
        $controller = !empty($parts[0]) ? array_shift($parts) : $this->defaultController;
        $action = !empty($parts[0]) ? array_shift($parts) : $this->defaultAction;

        $this->executeController($controller, $action, $parts);
    }

    /**
     * Parse request URI
     *
     * @return string Parsed URI
     */
    private function parseUri(): string
    {
        $uri = $_GET['url'] ?? $this->getRequestUri();
        
        // Remove base path and query string
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = trim($uri, '/');
        
        // Remove entry point if present
        $scriptName = basename($_SERVER['SCRIPT_NAME']);
        if (strpos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        }

        return trim($uri, '/');
    }

    /**
     * Execute controller action
     *
     * @param string $controllerName Controller name
     * @param string $actionName Action name
     * @param array<int, string> $params URL parameters
     * @return void
     * @throws Exception
     */
    private function executeController(string $controllerName, string $actionName, array $params = []): void
    {
        $controllerClass = $this->resolveController($controllerName);
        
        if (!class_exists($controllerClass)) {
            throw new Exception("Controller not found: {$controllerClass}");
        }

        $controller = new $controllerClass($this->getRootPath());
        
        if (!($controller instanceof Controller)) {
            throw new Exception("Invalid controller: {$controllerClass}");
        }

        $methodName = $this->resolveAction($actionName);
        
        if (!method_exists($controller, $methodName)) {
            throw new Exception("Action not found: {$methodName}");
        }

        $controller->setControllerName($controllerName);
        $controller->setActionName($actionName);

        call_user_func_array([$controller, $methodName], $params);
    }

    /**
     * Resolve controller class name
     *
     * @param string $controllerName Controller name
     * @return string Fully qualified controller class name
     */
    private function resolveController(string $controllerName): string
    {
        $controllerName = ucfirst(strtolower(trim($controllerName, '-')));
        return "JUANDirectory\\Controllers\\{$controllerName}Controller";
    }

    /**
     * Resolve action method name
     *
     * @param string $actionName Action name
     * @return string Method name in camelCase
     */
    private function resolveAction(string $actionName): string
    {
        $parts = array_map('ucfirst', explode('-', strtolower($actionName)));
        return 'action' . implode('', $parts);
    }

    /**
     * Handle exceptions
     *
     * @param Exception $exception Exception to handle
     * @return void
     */
    private function handleException(Exception $exception): void
    {
        error_log($exception->getMessage());
        
        if ($this->isProduction()) {
            $this->sendJson(
                ['error' => 'An error occurred. Please try again later.'],
                500
            );
        } else {
            $this->sendJson(
                [
                    'error' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTraceAsString()
                ],
                500
            );
        }
    }

    /**
     * Check if application is in production
     *
     * @return bool True if production environment
     */
    public function isProduction(): bool
    {
        return ($this->config['environment'] ?? 'development') === 'production';
    }

    /**
     * Get configuration value
     *
     * @param string $key Configuration key (dot notation supported)
     * @param mixed $default Default value
     * @return mixed Configuration value
     */
    public function config(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $keyPart) {
            if (is_array($value) && isset($value[$keyPart])) {
                $value = $value[$keyPart];
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Set configuration value
     *
     * @param string $key Configuration key
     * @param mixed $value Configuration value
     * @return self For method chaining
     */
    public function setConfig(string $key, mixed $value): self
    {
        $this->config[$key] = $value;
        return $this;
    }

    /**
     * Register service in container
     *
     * @param string $name Service name
     * @param callable|object $service Service instance or factory
     * @return self For method chaining
     */
    public function registerService(string $name, callable|object $service): self
    {
        $this->services[$name] = $service;
        return $this;
    }

    /**
     * Get service from container
     *
     * @param string $name Service name
     * @return mixed Service instance
     * @throws Exception If service not found
     */
    public function getService(string $name): mixed
    {
        if (!isset($this->services[$name])) {
            throw new Exception("Service not found: {$name}");
        }

        $service = $this->services[$name];
        return is_callable($service) ? $service() : $service;
    }

    /**
     * Get device detector
     *
     * @return DeviceDetector Device detector instance
     */
    public function getDeviceDetector(): DeviceDetector
    {
        return $this->deviceDetector ?? new DeviceDetector($this->getUserAgent());
    }

    /**
     * Get base URL
     *
     * @return string Base URL
     */
    public function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $port = $_SERVER['SERVER_PORT'] ?? 80;
        
        $baseUrl = "{$protocol}://{$host}";
        
        if (($protocol === 'http' && $port != 80) || ($protocol === 'https' && $port != 443)) {
            $baseUrl .= ":{$port}";
        }

        return $baseUrl . dirname($_SERVER['SCRIPT_NAME']);
    }
}
