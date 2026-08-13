<?php
/**
 * JUANDirectory Framework - Request Trait
 *
 * @package JUANDirectory\Core\Traits
 * @author John Virdi V. Alfonso
 * @license MIT
 * @version 2.0.0
 */

namespace JUANDirectory\Core\Traits;

/**
 * Request Handling Trait
 *
 * Provides methods for handling HTTP requests, headers, and query parameters.
 *
 * @category Framework
 * @package JUANDirectory\Core\Traits
 */
trait RequestTrait
{
    /**
     * Get request method (GET, POST, etc.)
     *
     * @return string HTTP method
     */
    protected function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Check if request is specific method
     *
     * @param string $method HTTP method to check
     * @return bool True if request matches method
     */
    protected function isRequestMethod(string $method): bool
    {
        return strtoupper($this->getRequestMethod()) === strtoupper($method);
    }

    /**
     * Get HTTP header value
     *
     * @param string $header Header name
     * @param mixed $default Default value if not found
     * @return mixed Header value or default
     */
    protected function getHeader(string $header, mixed $default = null): mixed
    {
        $headerKey = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        return $_SERVER[$headerKey] ?? $default;
    }

    /**
     * Get all headers
     *
     * @return array<string, string> All HTTP headers
     */
    protected function getAllHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerName = str_replace(['HTTP_', '_'], ['', '-'], $key);
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }

    /**
     * Get GET parameter
     *
     * @param string $key Parameter key
     * @param mixed $default Default value
     * @return mixed Parameter value or default
     */
    protected function getQuery(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Get POST parameter
     *
     * @param string $key Parameter key
     * @param mixed $default Default value
     * @return mixed Parameter value or default
     */
    protected function getPost(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get REQUEST parameter (GET or POST)
     *
     * @param string $key Parameter key
     * @param mixed $default Default value
     * @return mixed Parameter value or default
     */
    protected function getRequest(string $key, mixed $default = null): mixed
    {
        return $_REQUEST[$key] ?? $default;
    }

    /**
     * Get all query parameters
     *
     * @return array<string, mixed> All GET parameters
     */
    protected function getAllQueries(): array
    {
        return $_GET;
    }

    /**
     * Get all POST parameters
     *
     * @return array<string, mixed> All POST parameters
     */
    protected function getAllPost(): array
    {
        return $_POST;
    }

    /**
     * Check if parameter exists
     *
     * @param string $key Parameter key
     * @param string $source Source (GET, POST, REQUEST)
     * @return bool True if parameter exists
     */
    protected function hasParameter(string $key, string $source = 'REQUEST'): bool
    {
        $source = strtoupper($source);
        $array = match($source) {
            'GET' => $_GET,
            'POST' => $_POST,
            'REQUEST' => $_REQUEST,
            default => $_REQUEST
        };
        return isset($array[$key]);
    }

    /**
     * Get client IP address
     *
     * @return string Client IP address
     */
    protected function getClientIp(): string
    {
        $ips = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ips as $ip) {
            if (!empty($_SERVER[$ip])) {
                foreach (explode(',', $_SERVER[$ip]) as $clientIp) {
                    $clientIp = trim($clientIp);
                    if (filter_var($clientIp, FILTER_VALIDATE_IP)) {
                        return $clientIp;
                    }
                }
            }
        }

        return '0.0.0.0';
    }

    /**
     * Get request URI
     *
     * @return string Request URI
     */
    protected function getRequestUri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    /**
     * Get HTTP referrer
     *
     * @return string|null HTTP referrer or null
     */
    protected function getReferrer(): ?string
    {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }

    /**
     * Get user agent
     *
     * @return string User agent
     */
    protected function getUserAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    }
}
