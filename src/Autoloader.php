<?php
/**
 * JUANDirectory Framework - Manual PSR-4 Autoloader
 * No Composer required - standalone implementation
 *
 * @package JUANDirectory
 * @version 2.0.0
 */

namespace JUANDirectory;

/**
 * PSR-4 Compliant Manual Autoloader
 *
 * Simply include this file and it will automatically load all classes.
 * No Composer required!
 *
 * @category Framework
 */
class Autoloader
{
    /**
     * Base namespace
     *
     * @var string
     */
    private static string $baseNamespace = 'JUANDirectory';

    /**
     * Base path for classes
     *
     * @var string
     */
    private static string $basePath = '';

    /**
     * Namespace to directory mapping
     *
     * @var array<string, string>
     */
    private static array $namespaceMap = [
        'JUANDirectory\\Core' => 'src/Core',
        'JUANDirectory\\Controllers' => 'src/Controllers',
        'JUANDirectory\\Models' => 'src/Models',
        'JUANDirectory\\Views' => 'src/Views',
        'JUANDirectory\\Libraries' => 'src/Libraries',
        'JUANDirectory\\Utilities' => 'src/Utilities',
        'JUANDirectory\\Integrations' => 'src/Integrations',
        'JUANDirectory\\Traits' => 'src/Traits',
    ];

    /**
     * Loaded classes cache
     *
     * @var array<string, string>
     */
    private static array $loadedClasses = [];

    /**
     * Initialize autoloader
     *
     * @param string|null $basePath Base path for file resolution
     * @return void
     */
    public static function init(?string $basePath = null): void
    {
        if (empty($basePath)) {
            $basePath = dirname(__DIR__);
        }

        self::$basePath = rtrim($basePath, DIRECTORY_SEPARATOR);

        // Register autoloader
        spl_autoload_register([self::class, 'loadClass'], true, true);
    }

    /**
     * Load a class file
     *
     * @param string $className Full class name with namespace
     * @return bool True if class was loaded
     */
    public static function loadClass(string $className): bool
    {
        // Check cache first
        if (isset(self::$loadedClasses[$className])) {
            return true;
        }

        // Only handle JUANDirectory classes
        if (strpos($className, self::$baseNamespace) !== 0) {
            return false;
        }

        $filePath = self::resolveFilePath($className);

        if (!file_exists($filePath)) {
            return false;
        }

        if (!is_readable($filePath)) {
            return false;
        }

        require_once $filePath;
        self::$loadedClasses[$className] = $filePath;

        return true;
    }

    /**
     * Resolve class name to file path
     *
     * @param string $className Full class name with namespace
     * @return string File path to class
     */
    private static function resolveFilePath(string $className): string
    {
        // Remove leading backslash
        $className = ltrim($className, '\\');

        // Check namespace mappings
        foreach (self::$namespaceMap as $namespace => $directory) {
            $namespace = ltrim($namespace, '\\');
            if (strpos($className, $namespace) === 0) {
                $relativePath = substr($className, strlen($namespace));
                $relativePath = ltrim($relativePath, '\\');
                $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $relativePath);
                return self::$basePath . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . $fileName . '.php';
            }
        }

        // Default PSR-4 resolution
        $parts = explode('\\', $className);
        $className = array_pop($parts);
        $namespace = implode(DIRECTORY_SEPARATOR, $parts);

        return self::$basePath . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR . $className . '.php';
    }

    /**
     * Register a custom namespace mapping
     *
     * @param string $namespace Namespace to map
     * @param string $directory Directory path
     * @return void
     */
    public static function registerNamespace(string $namespace, string $directory): void
    {
        self::$namespaceMap[$namespace] = $directory;
    }

    /**
     * Get loaded classes
     *
     * @return array<string, string> Loaded classes and their paths
     */
    public static function getLoadedClasses(): array
    {
        return self::$loadedClasses;
    }

    /**
     * Check if class is loaded
     *
     * @param string $className Class name
     * @return bool True if loaded
     */
    public static function isLoaded(string $className): bool
    {
        return isset(self::$loadedClasses[$className]);
    }

    /**
     * Clear loaded classes cache
     *
     * @return void
     */
    public static function clearCache(): void
    {
        self::$loadedClasses = [];
    }
}

// Auto-initialize when this file is included
Autoloader::init();
