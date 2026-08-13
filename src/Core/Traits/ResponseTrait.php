<?php
/**
 * JUANDirectory Framework - Response Trait
 *
 * @package JUANDirectory\Core\Traits
 * @author John Virdi V. Alfonso
 * @license MIT
 * @version 2.0.0
 */

namespace JUANDirectory\Core\Traits;

/**
 * Response Handling Trait
 *
 * Provides methods for sending HTTP responses, redirects, and headers.
 *
 * @category Framework
 * @package JUANDirectory\Core\Traits
 */
trait ResponseTrait
{
    /**
     * HTTP status codes
     *
     * @var array<int, string>
     */
    private static array $httpCodes = [
        200 => 'OK',
        201 => 'Created',
        204 => 'No Content',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        409 => 'Conflict',
        410 => 'Gone',
        422 => 'Unprocessable Entity',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
    ];

    /**
     * Set HTTP status code
     *
     * @param int $code HTTP status code
     * @return self For method chaining
     */
    protected function setStatusCode(int $code): self
    {
        $message = self::$httpCodes[$code] ?? 'Unknown';
        http_response_code($code);
        return $this;
    }

    /**
     * Set response header
     *
     * @param string $name Header name
     * @param string $value Header value
     * @param bool $replace Replace existing header
     * @return self For method chaining
     */
    protected function setHeader(string $name, string $value, bool $replace = true): self
    {
        header("{$name}: {$value}", $replace);
        return $this;
    }

    /**
     * Set content type header
     *
     * @param string $contentType Content type
     * @param string $charset Character set
     * @return self For method chaining
     */
    protected function setContentType(string $contentType, string $charset = 'utf-8'): self
    {
        $this->setHeader('Content-Type', "{$contentType}; charset={$charset}");
        return $this;
    }

    /**
     * Send JSON response
     *
     * @param mixed $data Data to encode as JSON
     * @param int $statusCode HTTP status code
     * @param int $options JSON encoding options
     * @return void
     */
    protected function sendJson(mixed $data, int $statusCode = 200, int $options = JSON_PRETTY_PRINT): void
    {
        $this->setStatusCode($statusCode);
        $this->setContentType('application/json');
        echo json_encode($data, $options);
        exit;
    }

    /**
     * Redirect to URL
     *
     * @param string $url URL to redirect to
     * @param int $statusCode HTTP status code (301 or 302)
     * @return void
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        if (headers_sent()) {
            $this->redirectViaJavaScript($url);
            return;
        }

        $this->setStatusCode($statusCode);
        header("Location: {$url}");
        exit;
    }

    /**
     * Redirect via JavaScript (fallback for when headers sent)
     *
     * @param string $url URL to redirect to
     * @param int $delay Delay in milliseconds
     * @return void
     */
    protected function redirectViaJavaScript(string $url, int $delay = 0): void
    {
        $url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        
        if ($delay > 0) {
            echo "<script>setTimeout(function() { window.location.href = '{$url}'; }, {$delay});</script>";
        } else {
            echo "<script>window.location.href = '{$url}';</script>";
        }
        exit;
    }

    /**
     * Set cache headers
     *
     * @param int $seconds Cache duration in seconds
     * @return self For method chaining
     */
    protected function setCacheHeaders(int $seconds): self
    {
        $this->setHeader('Cache-Control', "public, max-age={$seconds}");
        $this->setHeader('Pragma', 'cache');
        $this->setHeader('Expires', gmdate('r', time() + $seconds));
        return $this;
    }

    /**
     * Disable caching
     *
     * @return self For method chaining
     */
    protected function disableCaching(): self
    {
        $this->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $this->setHeader('Pragma', 'no-cache');
        $this->setHeader('Expires', '0');
        return $this;
    }

    /**
     * Send file download
     *
     * @param string $filePath Path to file
     * @param string|null $downloadName Optional download filename
     * @return void
     * @throws \Exception If file not found
     */
    protected function sendFile(string $filePath, ?string $downloadName = null): void
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \Exception("File not found or not readable: {$filePath}");
        }

        $downloadName = $downloadName ?? basename($filePath);
        $fileSize = filesize($filePath);

        $this->setStatusCode(200);
        $this->setHeader('Content-Type', 'application/octet-stream');
        $this->setHeader('Content-Disposition', "attachment; filename=\"" . addslashes($downloadName) . "\"");
        $this->setHeader('Content-Length', (string)$fileSize);
        $this->setHeader('Pragma', 'public');

        readfile($filePath);
        exit;
    }

    /**
     * Output message and exit
     *
     * @param string $message Message to output
     * @param int $statusCode HTTP status code
     * @return void
     */
    protected function abort(string $message, int $statusCode = 500): void
    {
        $this->setStatusCode($statusCode);
        echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        exit;
    }
}
