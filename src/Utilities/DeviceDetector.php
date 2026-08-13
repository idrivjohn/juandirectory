<?php
/**
 * JUANDirectory Framework - Device Detector
 *
 * @package JUANDirectory\Utilities
 * @author John Virdi V. Alfonso
 * @license MIT
 * @version 2.0.0
 */

namespace JUANDirectory\Utilities;

/**
 * Device Detection Utility
 *
 * Detects device type, operating system, and browser from user agent.
 *
 * @category Framework
 * @package JUANDirectory\Utilities
 */
class DeviceDetector
{
    /**
     * User agent string
     *
     * @var string
     */
    private string $userAgent;

    /**
     * Detected device information
     *
     * @var array<string, string>
     */
    private array $device = [
        'type' => 'Unknown',
        'browser' => 'Unknown',
        'os' => 'Unknown',
        'ip' => '0.0.0.0'
    ];

    /**
     * Constructor
     *
     * @param string $userAgent User agent string
     */
    public function __construct(string $userAgent = '')
    {
        $this->userAgent = $userAgent ?: ($_SERVER['HTTP_USER_AGENT'] ?? '');
        $this->detect();
    }

    /**
     * Detect device information
     *
     * @return void
     */
    private function detect(): void
    {
        $this->device['type'] = $this->detectDeviceType();
        $this->device['browser'] = $this->detectBrowser();
        $this->device['os'] = $this->detectOperatingSystem();
        $this->device['ip'] = $this->detectClientIp();
    }

    /**
     * Detect device type
     *
     * @return string Device type (Desktop, Tablet, Mobile)
     */
    private function detectDeviceType(): string
    {
        $mobile = $this->match([
            'iphone', 'ipod', 'android', 'blackberry', 'webos',
            'windows phone', 'windows mobile'
        ]);

        $tablet = $this->match([
            'ipad', 'kindle', 'playbook', 'nexus 7', 'nexus 10',
            'xoom', 'transformer'
        ]);

        if ($tablet) {
            return 'Tablet';
        } elseif ($mobile) {
            return 'Mobile';
        }

        return 'Desktop';
    }

    /**
     * Detect browser
     *
     * @return string Browser name
     */
    private function detectBrowser(): string
    {
        $browsers = [
            'Chrome' => 'chrome',
            'Firefox' => 'firefox',
            'Safari' => 'safari',
            'Edge' => 'edg',
            'Opera' => ['opera', 'opr'],
            'Internet Explorer' => 'msie',
            'Netscape' => 'netscape',
            'Konqueror' => 'konqueror',
        ];

        foreach ($browsers as $name => $patterns) {
            if ($this->match((array)$patterns)) {
                return $name;
            }
        }

        return 'Unknown';
    }

    /**
     * Detect operating system
     *
     * @return string Operating system name
     */
    private function detectOperatingSystem(): string
    {
        $osPatterns = [
            'Windows 11' => 'windows nt 10.0; .*trident|windows nt 10.0; win64',
            'Windows 10' => 'windows nt 10.0',
            'Windows 8.1' => 'windows nt 6.3',
            'Windows 8' => 'windows nt 6.2',
            'Windows 7' => 'windows nt 6.1',
            'Windows Vista' => 'windows nt 6.0',
            'Windows XP' => ['windows nt 5.1', 'windows xp'],
            'macOS Ventura' => 'mac os x 13',
            'macOS Monterey' => 'mac os x 12',
            'macOS Big Sur' => 'mac os x 11',
            'macOS Catalina' => 'mac os x 10.15',
            'macOS' => 'mac os x',
            'Linux' => 'linux',
            'Ubuntu' => 'ubuntu',
            'iOS' => 'iphone|ipad|ipod',
            'Android' => 'android',
            'Chrome OS' => 'cros',
        ];

        foreach ($osPatterns as $name => $patterns) {
            if ($this->match((array)$patterns)) {
                return $name;
            }
        }

        return 'Unknown';
    }

    /**
     * Detect client IP address
     *
     * @return string Client IP address
     */
    private function detectClientIp(): string
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
     * Match pattern in user agent
     *
     * @param array<int, string> $patterns Patterns to match
     * @return bool True if pattern matched
     */
    private function match(array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (stripos($this->userAgent, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get device type
     *
     * @return string Device type
     */
    public function getType(): string
    {
        return $this->device['type'];
    }

    /**
     * Get browser name
     *
     * @return string Browser name
     */
    public function getBrowser(): string
    {
        return $this->device['browser'];
    }

    /**
     * Get operating system
     *
     * @return string Operating system name
     */
    public function getOperatingSystem(): string
    {
        return $this->device['os'];
    }

    /**
     * Get client IP
     *
     * @return string Client IP address
     */
    public function getIp(): string
    {
        return $this->device['ip'];
    }

    /**
     * Get all device information
     *
     * @return array<string, string> Device information
     */
    public function getAll(): array
    {
        return $this->device;
    }

    /**
     * Check if device is mobile
     *
     * @return bool True if mobile device
     */
    public function isMobile(): bool
    {
        return $this->device['type'] === 'Mobile';
    }

    /**
     * Check if device is tablet
     *
     * @return bool True if tablet device
     */
    public function isTablet(): bool
    {
        return $this->device['type'] === 'Tablet';
    }

    /**
     * Check if device is desktop
     *
     * @return bool True if desktop device
     */
    public function isDesktop(): bool
    {
        return $this->device['type'] === 'Desktop';
    }
}
