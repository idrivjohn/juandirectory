<?php
/**
 * JUANDirectory Framework - Base Controller
 *
 * @package JUANDirectory\Core
 * @author John Virdi V. Alfonso
 * @license MIT
 * @version 2.0.0
 */

namespace JUANDirectory\Core;

use JUANDirectory\Core\Traits\RequestTrait;
use JUANDirectory\Core\Traits\ResponseTrait;

/**
 * Abstract Base Controller Class
 *
 * Provides core controller functionality including request handling,
 * response management, and view rendering.
 *
 * @category Framework
 * @package JUANDirectory\Core
 * @abstract
 */
abstract class Controller extends Loader
{
    use RequestTrait;
    use ResponseTrait;

    /**
     * Controller name
     *
     * @var string
     */
    protected string $controllerName = '';

    /**
     * Action name
     *
     * @var string
     */
    protected string $actionName = '';

    /**
     * View data
     *
     * @var array<string, mixed>
     */
    protected array $viewData = [];

    /**
     * Constructor
     *
     * @param string|null $rootPath Optional root path
     */
    public function __construct(?string $rootPath = null)
    {
        parent::__construct($rootPath);
        $this->initializeController();
    }

    /**
     * Initialize controller
     *
     * Called after construction. Override in child classes for custom initialization.
     *
     * @return void
     */
    protected function initializeController(): void
    {
        // Override in child classes
    }

    /**
     * Render view file
     *
     * @param string $viewPath Path to view file
     * @param array<string, mixed> $data Data to pass to view
     * @return string Rendered view content
     * @throws \Exception If view file not found
     */
    protected function renderView(string $viewPath, array $data = []): string
    {
        $mergedData = array_merge($this->viewData, $data);
        $viewFile = 'views' . DIRECTORY_SEPARATOR . $viewPath . '.php';

        if (!$this->fileExists($viewFile)) {
            throw new \Exception("View file not found: {$viewPath}");
        }

        ob_start();
        extract($mergedData, EXTR_PROTECTED);
        $this->loadFile($viewFile, $mergedData);
        return ob_get_clean() ?: '';
    }

    /**
     * Set view data
     *
     * @param string $key Data key
     * @param mixed $value Data value
     * @return self For method chaining
     */
    protected function setViewData(string $key, mixed $value): self
    {
        $this->viewData[$key] = $value;
        return $this;
    }

    /**
     * Get view data
     *
     * @param string|null $key Optional key to get specific data
     * @return mixed View data
     */
    protected function getViewData(?string $key = null): mixed
    {
        if ($key === null) {
            return $this->viewData;
        }
        return $this->viewData[$key] ?? null;
    }

    /**
     * Check if request is AJAX
     *
     * @return bool True if request is AJAX
     */
    protected function isAjaxRequest(): bool
    {
        return ($this->getHeader('X-Requested-With') === 'XMLHttpRequest');
    }

    /**
     * Set controller name
     *
     * @param string $name Controller name
     * @return self For method chaining
     */
    public function setControllerName(string $name): self
    {
        $this->controllerName = $name;
        return $this;
    }

    /**
     * Get controller name
     *
     * @return string Controller name
     */
    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    /**
     * Set action name
     *
     * @param string $name Action name
     * @return self For method chaining
     */
    public function setActionName(string $name): self
    {
        $this->actionName = $name;
        return $this;
    }

    /**
     * Get action name
     *
     * @return string Action name
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }
}
