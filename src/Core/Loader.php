<?php
/**
 * JUANDirectory Framework - File Loader
 *
 * @package JUANDirectory\Core
 * @author John Virdi V. Alfonso
 * @license MIT
 * @version 2.0.0
 */

namespace JUANDirectory\Core;

use Exception;
use InvalidArgumentException;

/**
 * File Loader Class
 *
 * Handles file loading, path resolution, and directory checking.
 * Provides PSR-4 compatible autoloading support.
 *
 * @category Framework
 * @package JUANDirectory\Core
 */
class Loader
{
    /**
     * Directory separator constant
     *
     * @var string
     */
    protected string $directorySeparator = DIRECTORY_SEPARATOR;

    /**
     * Root directory path
     *
     * @var string
     */
    protected string $rootPath;

    /**
     * Loaded files cache
     *
     * @var array<string, string>
     */
    private array $loadedFiles = [];

    /**
     * Constructor
     *
     * @param string|null $rootPath Optional root path for file loading
     * @throws InvalidArgumentException If root path is invalid
     */
    public function __construct(?string $rootPath = null)
    {
        $this->rootPath = $rootPath ?? dirname(__DIR__, 2);
        
        if (!is_dir($this->rootPath)) {
            throw new InvalidArgumentException(
                "Invalid root path provided: {$this->rootPath}"
            );
        }
    }

    /**
     * Load a PHP file with optional data extraction
     *
     * @param string $filePath Path to file relative to root
     * @param array<string, mixed> $data Optional data to extract
     * @return bool True if file was loaded, false otherwise
     * @throws Exception If file loading fails
     */
    public function loadFile(string $filePath, array $data = []): bool
    {
        $fullPath = $this->resolvePath($filePath);

        if (!is_file($fullPath)) {
            throw new Exception("File not found: {$filePath}");
        }

        if (!is_readable($fullPath)) {
            throw new Exception("File not readable: {$filePath}");
        }

        // Check cache to avoid duplicate loading
        if (isset($this->loadedFiles[$filePath])) {
            return true;
        }

        // Extract data if provided
        if (!empty($data)) {
            extract($data, EXTR_PROTECTED);
        }

        try {
            require_once $fullPath;
            $this->loadedFiles[$filePath] = $fullPath;
            return true;
        } catch (Exception $e) {
            throw new Exception(
                "Error loading file {$filePath}: " . $e->getMessage()
            );
        }
    }

    /**
     * Check if a file exists
     *
     * @param string $filePath Path to file
     * @return bool True if file exists and is readable
     */
    public function fileExists(string $filePath): bool
    {
        try {
            $fullPath = $this->resolvePath($filePath);
            return is_file($fullPath) && is_readable($fullPath);
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Check if a directory exists
     *
     * @param string $directoryPath Path to directory
     * @return bool True if directory exists
     */
    public function directoryExists(string $directoryPath): bool
    {
        try {
            $fullPath = $this->resolvePath($directoryPath);
            return is_dir($fullPath);
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Get all files in a directory
     *
     * @param string $directoryPath Path to directory
     * @param string $pattern Optional filename pattern (glob)
     * @return array<int, string> Array of file paths
     * @throws Exception If directory doesn't exist
     */
    public function getFiles(string $directoryPath, string $pattern = '*'): array
    {
        $fullPath = $this->resolvePath($directoryPath);

        if (!is_dir($fullPath)) {
            throw new Exception("Directory not found: {$directoryPath}");
        }

        $files = glob($fullPath . $this->directorySeparator . $pattern);
        return $files ?: [];
    }

    /**
     * Get all directories in a path
     *
     * @param string $directoryPath Path to directory
     * @return array<int, string> Array of directory paths
     * @throws Exception If directory doesn't exist
     */
    public function getDirectories(string $directoryPath): array
    {
        $fullPath = $this->resolvePath($directoryPath);

        if (!is_dir($fullPath)) {
            throw new Exception("Directory not found: {$directoryPath}");
        }

        $directories = glob($fullPath . $this->directorySeparator . '*', GLOB_ONLYDIR);
        return $directories ?: [];
    }

    /**
     * Resolve relative path to full path
     *
     * @param string $relativePath Relative path from root
     * @return string Full resolved path
     * @throws InvalidArgumentException If path contains invalid sequences
     */
    protected function resolvePath(string $relativePath): string
    {
        // Normalize path separators
        $relativePath = str_replace(['/', '\\'], $this->directorySeparator, $relativePath);

        // Prevent directory traversal attacks
        if (strpos($relativePath, '..') !== false) {
            throw new InvalidArgumentException(
                "Path traversal not allowed: {$relativePath}"
            );
        }

        $fullPath = $this->rootPath . $this->directorySeparator . $relativePath;
        
        // Resolve to absolute path
        return realpath($fullPath) ?: $fullPath;
    }

    /**
     * Get root path
     *
     * @return string Root directory path
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * Get directory separator
     *
     * @return string Directory separator character
     */
    public function getDirectorySeparator(): string
    {
        return $this->directorySeparator;
    }

    /**
     * Clear file cache
     *
     * @return void
     */
    public function clearCache(): void
    {
        $this->loadedFiles = [];
    }

    /**
     * Get loaded files
     *
     * @return array<string, string> Array of loaded files
     */
    public function getLoadedFiles(): array
    {
        return $this->loadedFiles;
    }
}
