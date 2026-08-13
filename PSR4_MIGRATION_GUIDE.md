# PSR-4 Namespace Structure Migration Guide

## Overview
This document outlines the PSR-4 namespace structure for the JUANDirectory framework, migrating from the legacy class-based autoloader to a standards-compliant PSR-4 autoloader.

## New Directory Structure

```
juandirectory/
├── src/
│   ├── Core/                    # Core framework classes
│   │   ├── Application.php      # Main application class
│   │   ├── Loader.php           # File loader and registry
│   │   ├── Controller.php       # Base controller class
│   │   └── Router.php           # URL routing (new)
│   │
│   ├── Database/                # Database-related classes
│   │   ├── Connection.php       # Database connection (renamed from class.database.php)
│   │   ├── Query.php            # Query builder (new)
│   │   └── Manager.php          # Database manager (new)
│   │
│   ├── Controllers/             # Application controllers
│   │   └── HomeController.php   # Example controller
│   │
│   ├── Models/                  # Application models
│   │   └── BaseModel.php        # Base model class
│   │
│   ├── Views/                   # View templates
│   │   └── .gitkeep
│   │
│   ├── Libraries/               # Third-party and utility libraries
│   │   └── Vendor/              # Third-party libraries
│   │       ├── MobileDetect.php # Renamed from class.mobile-detect.php
│   │       └── ...
│   │
│   ├── Utilities/               # Utility functions and helpers
│   │   ├── Token.php            # Token handling (renamed from class.token.php)
│   │   ├── Helper.php           # General helpers (new)
│   │   └── Validator.php        # Validation helpers (new)
│   │
│   ├── Integrations/            # Social media and external integrations
│   │   ├── Facebook.php         # Facebook integration (renamed from class.facebook.php)
│   │   ├── Instagram.php        # Instagram integration (renamed from class.instagram.php)
│   │   └── StaticPage.php       # Static page handling (renamed from class.staticpage.php)
│   │
│   └── Traits/                  # Reusable traits
│       ├── RequestTrait.php     # Request handling trait
│       └── ResponseTrait.php    # Response handling trait
│
├── config/
│   ├── MVCConfig.php            # Configuration (moved from mvc/configs/)
│   └── database.php             # Database configuration
│
├── public/
│   ├── index.php                # Entry point
│   ├── htaccess                 # Rewrite rules
│   └── assets/                  # Static files
│
├── tests/                       # Unit and feature tests
│   └── .gitkeep
│
├── composer.json                # Composer configuration
├── .htaccess                    # Root rewrite rules
└── README.md
```

## Namespace Mappings

### Original → New Structure

| Original File | New Location | Namespace |
|---|---|---|
| `mvc/libraries/cores/class.application.php` | `src/Core/Application.php` | `JUANDirectory\Core\Application` |
| `mvc/libraries/cores/class.controller.php` | `src/Core/Controller.php` | `JUANDirectory\Core\Controller` |
| `mvc/libraries/cores/class.loader.php` | `src/Core/Loader.php` | `JUANDirectory\Core\Loader` |
| `mvc/libraries/cores/class.database.php` | `src/Database/Connection.php` | `JUANDirectory\Database\Connection` |
| `mvc/libraries/cores/class.token.php` | `src/Utilities/Token.php` | `JUANDirectory\Utilities\Token` |
| `mvc/libraries/cores/class.facebook.php` | `src/Integrations/Facebook.php` | `JUANDirectory\Integrations\Facebook` |
| `mvc/libraries/cores/class.instagram.php` | `src/Integrations/Instagram.php` | `JUANDirectory\Integrations\Instagram` |
| `mvc/libraries/cores/class.staticpage.php` | `src/Integrations/StaticPage.php` | `JUANDirectory\Integrations\StaticPage` |
| `mvc/libraries/vendors/class.mobile-detect.php` | `src/Libraries/Vendor/MobileDetect.php` | `JUANDirectory\Libraries\Vendor\MobileDetect` |

## Implementation Steps

### Step 1: Update Bootstrap
The bootstrap file will be updated to use Composer's autoloader:

```php
<?php
// src/bootstrap.php

// Load Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Define root constants
define('DS', DIRECTORY_SEPARATOR);
define('ROOTDIR', dirname(__DIR__) . DS);
define('SRCDIR', ROOTDIR . 'src' . DS);
define('CONFIGDIR', ROOTDIR . 'config' . DS);

// Legacy support (deprecated)
if (!defined('CURRENTFOLDER')) {
    define('CURRENTFOLDER', 'default');
}
```

### Step 2: Update Entry Point
Update `public/index.php` to use PSR-4 autoloading:

```php
<?php
// public/index.php

require_once dirname(__DIR__) . '/src/bootstrap.php';

use JUANDirectory\Core\Application;

try {
    $app = new Application();
    $app->run();
} catch (Exception $e) {
    error_log($e->getMessage());
    die('An error occurred. Please try again later.');
}
```

### Step 3: Class Naming Conventions

- **Classes**: `PascalCase` (e.g., `Application`, `Database`, `Controller`)
- **Files**: Match class name exactly (e.g., `Application.php`)
- **Namespaces**: `PascalCase` matching directory structure
- **Methods**: `camelCase` (e.g., `getData()`, `parseUrl()`)
- **Properties**: `camelCase` or `snake_case` depending on context

### Step 4: Updating Existing Classes

#### Before (Legacy):
```php
<?php
class Application extends Loader {
    // ...
}
```

#### After (PSR-4):
```php
<?php
namespace JUANDirectory\Core;

use JUANDirectory\Libraries\Vendor\MobileDetect;

class Application extends Loader {
    // ...
}
```

## Benefits of PSR-4

1. **Standardization**: Follows PHP-FIG PSR-4 standard
2. **Autoloading**: No manual class loading required
3. **IDE Support**: Better code completion and refactoring in IDEs
4. **Dependency Management**: Composer handles third-party libraries
5. **Maintainability**: Clear folder structure and organization
6. **Scalability**: Easy to add new features and modules
7. **Testing**: Better separation of concerns for unit testing

## Migration Checklist

- [ ] Create new directory structure
- [ ] Update composer.json with PSR-4 configuration
- [ ] Refactor core classes with new namespaces
- [ ] Update class method names to camelCase
- [ ] Remove legacy class.*.php naming convention
- [ ] Update bootstrap to use Composer autoloader
- [ ] Update entry point (public/index.php)
- [ ] Move configuration files to config/ directory
- [ ] Update internal dependencies and use statements
- [ ] Test all functionality
- [ ] Update documentation and README
- [ ] Run composer install
- [ ] Add unit tests with PHPUnit

## Backward Compatibility

For legacy code that still uses the old structure:

```php
<?php
// Legacy compatibility layer (optional)
// This can be placed in src/bootstrap.php

// Alias old namespaces to new ones if needed
class_alias('JUANDirectory\Core\Application', 'MVC\Application');
class_alias('JUANDirectory\Core\Controller', 'MVC\Controller');
```

## Next Steps

1. Install Composer: `composer install`
2. Run tests: `./vendor/bin/phpunit`
3. Update database configuration in `config/database.php`
4. Move configuration files from `mvc/configs/` to `config/`
5. Test the application thoroughly
6. Update deployment scripts and documentation

## References

- [PSR-4 Autoloading Standard](https://www.php-fig.org/psr/psr-4/)
- [Composer Documentation](https://getcomposer.org/doc/)
- [PHP Namespaces](https://www.php.net/manual/en/language.namespaces.php)
