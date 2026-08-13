# JUANDirectory v2.0 - Standalone Installation (No Composer Required)

## Quick Start

This guide shows how to use JUANDirectory without Composer. Everything is included and works out of the box!

## Prerequisites

- PHP 8.0 or higher
- Web server (Apache, Nginx, etc.)
- That's it! No Composer needed.

## Installation Steps

### Step 1: Copy Files

1. Copy all files from the repository to your web server
2. Ensure this directory structure exists:

```
your-project/
├── src/
│   ├── Autoloader.php          (Automatically loads classes)
│   ├── standalone-bootstrap.php (Initialize everything)
│   ├── Core/
│   │   ├── Application.php
│   │   ├── Controller.php
│   │   ├── Loader.php
│   │   ├── Container.php
│   │   └── Traits/
│   ├── Controllers/
│   ├── Models/
│   ├── Views/
│   ├── Utilities/
│   └── Integrations/
├── public/
│   ├── index.php
│   └── .htaccess
├── config/
│   └── app.php
└── logs/
```

### Step 2: Update public/index.php

Replace the content of `public/index.php` with:

```php
<?php
// Load the standalone bootstrap
require_once dirname(__DIR__) . '/src/standalone-bootstrap.php';

use JUANDirectory\Core\Application;

try {
    // Load configuration
    $config = require dirname(__DIR__) . '/config/app.php';
    
    // Create and run application
    $app = new Application($config);
    $app->run();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
```

### Step 3: Create Configuration File

Create `config/app.php`:

```php
<?php
return [
    'app' => [
        'name' => 'JUANDirectory',
        'environment' => 'development',
        'debug' => true,
        'timezone' => 'UTC',
    ],
    'routing' => [
        'default_controller' => 'home',
        'default_action' => 'index',
    ],
    // Add more config as needed
];
```

### Step 4: Create Your First Controller

Create `src/Controllers/HomeController.php`:

```php
<?php
namespace JUANDirectory\Controllers;

use JUANDirectory\Core\Controller;

class HomeController extends Controller
{
    public function actionIndex()
    {
        echo "Welcome to JUANDirectory!";
    }
}
```

### Step 5: Set Permissions

```bash
chmod -R 755 src/
chmod -R 755 public/
chmod -R 775 logs/
```

### Step 6: Test

Visit: `http://localhost/your-project/public/`

You should see: "Welcome to JUANDirectory!"

## How It Works

### Autoloader (No Composer)

The `src/Autoloader.php` file automatically loads classes based on PSR-4 naming:

- Class: `JUANDirectory\Core\Application`
- File: `src/Core/Application.php`

**How it works:**

1. When you use a class, PHP calls the autoloader
2. Autoloader converts namespace to file path
3. File is included automatically
4. No manual `require` statements needed!

### Bootstrap File

The `src/standalone-bootstrap.php` file:

1. Sets up directory constants
2. Loads the autoloader
3. Starts session
4. Sets up error handling
5. Creates logs directory

## Usage Examples

### Basic Controller

```php
<?php
namespace JUANDirectory\Controllers;

use JUANDirectory\Core\Controller;

class ProductController extends Controller
{
    public function actionList()
    {
        // Handle GET request
        $category = $this->getQuery('category');
        echo "Products in: " . htmlspecialchars($category);
    }

    public function actionCreate()
    {
        if ($this->isRequestMethod('POST')) {
            $name = $this->getPost('name');
            echo "Created: " . htmlspecialchars($name);
        }
    }
}
```

### Working with Files

```php
<?php
use JUANDirectory\Core\Loader;

$loader = new Loader();

// Check if file exists
if ($loader->fileExists('config/database.php')) {
    $loader->loadFile('config/database.php');
}

// Get all PHP files
$files = $loader->getFiles('src/Models', '*.php');
foreach ($files as $file) {
    echo $file . "<br>";
}
```

### Service Container

```php
<?php
use JUANDirectory\Core\Container;

$container = new Container();

// Register a service
$container->singleton('database', function() {
    return new DatabaseConnection();
});

// Use the service
$db = $container->make('database');
```

### Request Handling

```php
<?php
class APIController extends Controller
{
    public function actionData()
    {
        // Get request data
        $id = $this->getQuery('id', 0);
        $method = $this->getRequestMethod();
        $ip = $this->getClientIp();
        
        // Send JSON response
        $this->sendJson([
            'id' => $id,
            'method' => $method,
            'ip' => $ip
        ]);
    }
}
```

### Device Detection

```php
<?php
class ResponsiveController extends Controller
{
    public function actionView()
    {
        $device = $this->getDeviceDetector();
        
        if ($device->isMobile()) {
            echo "Mobile Device";
        } elseif ($device->isTablet()) {
            echo "Tablet Device";
        } else {
            echo "Desktop Device";
        }
    }
}
```

## Routing

Routing is automatic based on URL structure:

```
http://yoursite.com/public/controller/action/param1/param2
```

**Examples:**

- `/public/home/index` → HomeController::actionIndex()
- `/public/product/list?id=5` → ProductController::actionList()
- `/public/api/data` → APIController::actionData()

## .htaccess Configuration

Create `public/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?url=$1 [QSA,PT,L]
</IfModule>
```

Create root `.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^$ public/ [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/$1
</IfModule>
```

## File Structure

After installation, your structure should be:

```
your-project/
├── .htaccess              # Root rewrite rules
├── public/
│   ├── index.php          # Entry point
│   ├── .htaccess          # Public rewrite rules
│   └── css/, js/, etc.    # Static files
├── src/
│   ├── Autoloader.php     # Auto-loader (don't modify)
│   ├── standalone-bootstrap.php # Bootstrap (don't modify)
│   ├── Core/              # Framework core
│   ├── Controllers/       # Your controllers
│   ├── Models/            # Your models
│   ├── Views/             # Your views
│   ├── Utilities/         # Utilities
│   └── Integrations/      # API integrations
├── config/
│   └── app.php            # Configuration
├── logs/                  # Application logs
└── README.md
```

## Troubleshooting

### "Class Not Found" Error

**Problem:** `Class 'JUANDirectory\Controllers\HomeController' not found`

**Solution:**
1. Check the file exists at `src/Controllers/HomeController.php`
2. Check namespace matches: `namespace JUANDirectory\Controllers;`
3. Check class name matches filename: `class HomeController`

### "Headers Already Sent" Error

**Problem:** Error occurs when trying to set headers

**Solution:**
1. Ensure no output before `header()` calls
2. No spaces before `<?php`
3. No spaces after `?>`

### "Permission Denied" Error

**Problem:** Cannot write to logs directory

**Solution:**
```bash
chmod 775 logs/
```

### Autoloader Not Working

**Problem:** Classes not loading automatically

**Solution:**
1. Ensure `src/Autoloader.php` is included in bootstrap
2. Check file paths are correct
3. Verify PHP version is 8.0+

## Performance Tips

### 1. Disable Debug Mode in Production

```php
'app' => [
    'debug' => false,  // Disable in production
]
```

### 2. Use Singletons for Heavy Objects

```php
$container->singleton('database', DatabaseConnection::class);
```

### 3. Cache Configuration

```php
// Load config once, reuse
$config = require 'config/app.php';
```

### 4. Optimize Autoloader Cache

```php
// Get loaded classes (for debugging)
$loaded = \JUANDirectory\Autoloader::getLoadedClasses();
```

## Adding More Classes

To add new classes, simply:

1. Create file in proper directory
2. Use correct namespace
3. They load automatically!

**Example:**

Create `src/Utilities/EmailHelper.php`:

```php
<?php
namespace JUANDirectory\Utilities;

class EmailHelper
{
    public static function send($to, $subject, $body)
    {
        // Send email
    }
}
```

Use anywhere:

```php
<?php
use JUANDirectory\Utilities\EmailHelper;

EmailHelper::send('user@example.com', 'Hello', 'Message');
```

## Next Steps

1. ✅ Install files
2. ✅ Create first controller
3. ✅ Test routing
4. ✅ Build your application
5. ✅ Deploy to production

## Support

For issues:
1. Check error logs in `logs/` directory
2. Review this guide
3. Check class paths and namespaces
4. Verify file permissions

## Summary

✨ **No Composer needed** - Everything works standalone
✨ **PSR-4 compliant** - Professional structure
✨ **Copy and paste** - Just drop files in place
✨ **Works immediately** - No configuration needed
✨ **Scalable** - Build large applications

Happy coding! 🚀
