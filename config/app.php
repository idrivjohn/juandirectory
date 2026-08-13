<?php
/**
 * JUANDirectory Framework - Configuration
 *
 * @package JUANDirectory
 * @version 2.0.0
 */

return [
    /**
     * Application Settings
     */
    'app' => [
        'name' => 'JUANDirectory',
        'version' => '2.0.0',
        'environment' => $_ENV['APP_ENV'] ?? 'development',
        'debug' => $_ENV['APP_DEBUG'] ?? true,
        'timezone' => $_ENV['APP_TIMEZONE'] ?? 'UTC',
    ],

    /**
     * Database Configuration
     */
    'database' => [
        'driver' => $_ENV['DB_DRIVER'] ?? 'mysql',
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => $_ENV['DB_PORT'] ?? 3306,
        'database' => $_ENV['DB_NAME'] ?? 'juandirectory',
        'username' => $_ENV['DB_USER'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => 'jd_',
    ],

    /**
     * Routing Configuration
     */
    'routing' => [
        'base_path' => $_ENV['BASE_PATH'] ?? '/',
        'default_controller' => 'home',
        'default_action' => 'index',
        'case_sensitive' => false,
    ],

    /**
     * Cache Configuration
     */
    'cache' => [
        'driver' => $_ENV['CACHE_DRIVER'] ?? 'file',
        'default_ttl' => 3600,
        'path' => ROOTDIR . 'storage/cache/',
    ],

    /**
     * Session Configuration
     */
    'session' => [
        'driver' => $_ENV['SESSION_DRIVER'] ?? 'file',
        'lifetime' => 120,
        'secure' => false,
        'http_only' => true,
        'same_site' => 'Lax',
    ],

    /**
     * Security Configuration
     */
    'security' => [
        'csrf_enabled' => true,
        'csrf_token_name' => '_csrf_token',
        'rate_limiting' => [
            'enabled' => true,
            'requests' => 100,
            'window' => 3600,
        ],
    ],

    /**
     * Logging Configuration
     */
    'logging' => [
        'channels' => [
            'default' => 'single',
            'single' => [
                'driver' => 'single',
                'path' => ROOTDIR . 'logs/app.log',
                'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
            ],
            'error' => [
                'driver' => 'single',
                'path' => ROOTDIR . 'logs/error.log',
                'level' => 'error',
            ],
        ],
    ],

    /**
     * Mail Configuration
     */
    'mail' => [
        'driver' => $_ENV['MAIL_DRIVER'] ?? 'smtp',
        'host' => $_ENV['MAIL_HOST'] ?? 'smtp.mailtrap.io',
        'port' => $_ENV['MAIL_PORT'] ?? 587,
        'username' => $_ENV['MAIL_USERNAME'] ?? '',
        'password' => $_ENV['MAIL_PASSWORD'] ?? '',
        'from' => [
            'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@juandirectory.com',
            'name' => $_ENV['MAIL_FROM_NAME'] ?? 'JUANDirectory',
        ],
    ],

    /**
     * File Upload Configuration
     */
    'uploads' => [
        'path' => PUBLICDIR . 'uploads/',
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
        'create_directory' => true,
    ],

    /**
     * API Configuration
     */
    'api' => [
        'enabled' => true,
        'version' => 'v1',
        'rate_limit' => 1000,
        'timeout' => 30,
    ],

    /**
     * Feature Flags
     */
    'features' => [
        'api_enabled' => true,
        'analytics_enabled' => true,
        'social_login_enabled' => true,
    ],
];
