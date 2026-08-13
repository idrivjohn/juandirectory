# PSR-4 Refactoring - Implementation Guide

## Overview

This document provides a step-by-step guide for implementing the PSR-4 refactored codebase in your JUANDirectory installation.

## Changes Made

### 1. **Namespace Structure**
   - All classes now use `JUANDirectory\*` namespace
   - Organized by functional domain (Core, Controllers, Models, etc.)
   - Full PSR-4 compliance

### 2. **Core Classes Refactored**

#### `Loader` Class
- **Location**: `src/Core/Loader.php`
- **Namespace**: `JUANDirectory\Core\Loader`
- **Improvements**:
  - Type-safe parameter handling
  - Exception-based error handling
  - File cache to prevent duplicate loading
  - Path traversal attack prevention
  - Comprehensive PHPDoc comments

#### `Controller` Class
- **Location**: `src/Core/Controller.php`
- **Namespace**: `JUANDirectory\Core\Controller`
- **Improvements**:
  - Uses `RequestTrait` and `ResponseTrait`
  - View rendering with data extraction
  - AJAX detection
  - Better initialization hooks

#### `Application` Class
- **Location**: `src/Core/Application.php`
- **Namespace**: `JUANDirectory\Core\Application`
- **Features**:
  - Service container integration
  - Advanced routing with controller/action resolution
  - Configuration management with dot notation
  - Exception handling with detailed logging
  - Device detection
  - Environment-aware behavior

### 3. **New Trait System**

#### `RequestTrait`
- Handles HTTP request information
- Methods for GET, POST, headers, client IP
- Request method detection

#### `ResponseTrait`
- Manages HTTP responses
- JSON response handling
- Redirect management
- Cache headers
- File downloads

### 4. **New Utility Classes**

#### `Container` (Dependency Injection)
- Service registration and resolution
- Singleton support
- Alias management
- ArrayAccess implementation

#### `DeviceDetector`
- Device type detection (Mobile, Tablet, Desktop)
- Browser identification
- OS detection
- Client IP extraction

### 5. **Configuration System**

- Environment-based configuration
- `config/app.php` for all settings
- Support for environment variables
- Database, cache, session, mail configuration
- Feature flags

### 6. **Bootstrap Improvements**

- Composer autoloader integration
- Comprehensive error handling
- Exception handling with logging
- Session management
- Directory constants definition

## Implementation Checklist

### Phase 1: Preparation
- [ ] Backup current installation
- [ ] Review PSR4_MIGRATION_GUIDE.md
- [ ] Install Composer dependencies: `composer install`
- [ ] Review new directory structure

### Phase 2: File Migration
- [ ] Create `src/` directory structure
- [ ] Copy refactored core classes
- [ ] Move configuration files to `config/`
- [ ] Update `public/index.php` entry point
- [ ] Verify file permissions

### Phase 3: Application Update
- [ ] Update controller namespace declarations
- [ ] Update model namespace declarations
- [ ] Update view loading paths
- [ ] Update configuration file paths
- [ ] Update autoloading in bootstrap

### Phase 4: Testing
- [ ] Test routing and controllers
- [ ] Test request/response handling
- [ ] Test view rendering
- [ ] Test device detection
- [ ] Test error handling
- [ ] Run unit tests: `./vendor/bin/phpunit`

### Phase 5: Deployment
- [ ] Test on staging server
- [ ] Verify all functionality
- [ ] Check error logs
- [ ] Deploy to production
- [ ] Monitor for issues

## Usage Instructions

### Creating a Controller

**Old Way**:
```php
<?php
class HomeController extends Controller {
    // ...
}
```

**New Way**:
```php
<?php
namespace JUANDirectory\Controllers;

use JUANDirectory\Core\Controller;

class HomeController extends Controller {
    public function actionIndex() {
        // Method must start with 'action' prefix
    }
}
```

### Using Traits

```php
<?php
class MyController extends Controller {
    // RequestTrait and ResponseTrait are automatically available
    
    public function actionHandle() {
        // Request methods
        $name = $this->getQuery('name');
        $method = $this->getRequestMethod();
        
        // Response methods
        $this->sendJson(['status' => 'ok']);
    }
}
```

### Service Container

```php
<?php
use JUANDirectory\Core\Container;

$container = new Container();

// Register services
$container->singleton('config', function() {
    return require 'config/app.php';
});

// Retrieve services
$config = $container->make('config');
```

### Device Detection

```php
<?php
class ResponsiveController extends Controller {
    public function actionView() {
        $device = $this->getDeviceDetector();
        
        $data = [
            'device_type' => $device->getType(),
            'browser' => $device->getBrowser(),
            'os' => $device->getOperatingSystem(),
            'is_mobile' => $device->isMobile(),
        ];
        
        return $this->renderView('responsive', $data);
    }
}
```

## Configuration

### Environment Variables

Create `.env` file in project root:

```env
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=UTC

DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_NAME=juandirectory
DB_USER=root
DB_PASSWORD=

CACHE_DRIVER=file
SESSION_DRIVER=file

LOG_LEVEL=debug

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
```

### Configuration Access

```php
<?php
$app = new Application(require 'config/app.php');

// Access config
$appName = $app->config('app.name');
$dbHost = $app->config('database.host');

// Set config
$app->setConfig('cache.enabled', true);
```

## Performance Optimization

### 1. Optimize Autoloader
```bash
composer dump-autoload -o
```

### 2. Enable Caching
```php
// Cache configuration
'cache' => [
    'driver' => 'file',
    'path' => ROOTDIR . 'storage/cache/',
]
```

### 3. Use Singletons
```php
$container->singleton('database', DatabaseConnection::class);
```

## Troubleshooting

### Issue: Class Not Found
**Solution**: Run `composer dump-autoload -o`

### Issue: Permission Denied
**Solution**: `chmod -R 775 storage/ logs/`

### Issue: Views Not Loading
**Solution**: Verify path format and ensure files exist in `src/Views/`

### Issue: Configuration Not Applied
**Solution**: Verify `.env` variables are set and match configuration keys

## Breaking Changes

1. **Namespace Changes**: All classes use `JUANDirectory\*` namespace
2. **Method Naming**: Controller actions must start with `action` prefix
3. **Config Path**: Configuration now in `config/` directory
4. **Entry Point**: Update `public/index.php` to use new bootstrap
5. **Autoloader**: Now uses Composer PSR-4 autoloader

## Backward Compatibility

To maintain backward compatibility during transition:

```php
// In src/bootstrap.php
class_alias('JUANDirectory\Core\Application', 'MVC\Application');
class_alias('JUANDirectory\Core\Controller', 'MVC\Controller');
```

## Support

For issues or questions:
1. Check this guide
2. Review PSR4_MIGRATION_GUIDE.md
3. Check GitHub Issues
4. Contact maintainer

## Next Steps

1. Review the new code structure
2. Update your application code
3. Run tests thoroughly
4. Deploy to staging
5. Monitor for issues
6. Deploy to production

---

**Happy refactoring! 🚀**
