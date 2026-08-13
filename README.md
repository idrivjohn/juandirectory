# JUANDirectory Framework v2.0

## Modern PSR-4 Compliant PHP Framework

A refactored and modernized version of the JUANDirectory MVC framework with full PSR-4 autoloading support, improved architecture, and future-proof design patterns.

## Features

✨ **Modern Architecture**
- PSR-4 Autoloading with Composer
- Dependency Injection Container
- Trait-based functionality composition
- Service locator pattern

🔒 **Security First**
- CSRF protection
- Rate limiting
- Input validation
- Secure session handling

📱 **Device Detection**
- Automatic device type detection
- Browser and OS identification
- Client IP detection
- User agent parsing

🎯 **Developer Friendly**
- Clean, readable codebase
- Comprehensive documentation
- Method chaining support
- Extensible architecture

⚡ **Performance**
- File caching
- Service instance caching
- Optimized routing
- Minimal overhead

## Directory Structure

```
juandirectory/
├── src/
│   ├── Core/              # Framework core
│   ├── Controllers/       # Application controllers
│   ├── Models/           # Data models
│   ├── Views/            # View templates
│   ├── Libraries/        # Reusable libraries
│   ├── Utilities/        # Helper utilities
│   ├── Integrations/     # Third-party integrations
│   ├── Traits/           # Reusable traits
│   └── bootstrap.php     # Application bootstrap
├── config/               # Configuration files
├── public/               # Web root
├── tests/                # Unit tests
├── logs/                 # Application logs
└── composer.json         # Composer configuration
```

## Installation

### Requirements
- PHP 8.0 or higher
- Composer
- Web server (Apache, Nginx, etc.)

### Setup

1. **Clone or download the repository**
   ```bash
   git clone https://github.com/idrivjohn/juandirectory.git
   cd juandirectory
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env with your configuration
   ```

4. **Set permissions**
   ```bash
   chmod -R 775 storage/
   chmod -R 775 logs/
   ```

## Quick Start

### Create a Controller

```php
<?php
// src/Controllers/HomeController.php

namespace JUANDirectory\Controllers;

use JUANDirectory\Core\Controller;

class HomeController extends Controller
{
    public function actionIndex()
    {
        return $this->renderView('home', [
            'title' => 'Welcome to JUANDirectory'
        ]);
    }
}
```

### Create a View

```php
<!-- src/Views/home.php -->
<div class="container">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <p>Welcome to JUANDirectory Framework!</p>
</div>
```

### Request Handling

```php
<?php
// Example controller with request handling

class APIController extends Controller
{
    public function actionGetData()
    {
        if ($this->isAjaxRequest()) {
            $data = [
                'status' => 'success',
                'data' => ['example' => 'data']
            ];
            $this->sendJson($data);
        }
    }
}
```

## Configuration

Configuration is managed through `config/app.php`:

```php
'app' => [
    'name' => 'JUANDirectory',
    'environment' => 'production',
    'debug' => false,
    'timezone' => 'UTC',
]
```

Environment variables override config values:

```bash
APP_ENV=production
APP_DEBUG=false
DB_HOST=localhost
DB_NAME=juandirectory
```

## Usage Examples

### Dependency Injection

```php
<?php
use JUANDirectory\Core\Container;

$container = new Container();
$container->singleton('database', DatabaseConnection::class);

$db = $container->make('database');
```

### Request/Response

```php
class MyController extends Controller
{
    public function actionHandle()
    {
        // Get request data
        $name = $this->getQuery('name', 'Guest');
        $method = $this->getRequestMethod();
        
        // Check request type
        if ($this->isRequestMethod('POST')) {
            // Handle POST
        }
        
        // Send response
        $this->sendJson(['message' => 'Success']);
    }
}
```

### Device Detection

```php
class DeviceAwareController extends Controller
{
    public function actionResponsive()
    {
        $device = $this->getDeviceDetector();
        
        if ($device->isMobile()) {
            return $this->renderView('mobile/home');
        } elseif ($device->isTablet()) {
            return $this->renderView('tablet/home');
        }
        
        return $this->renderView('desktop/home');
    }
}
```

### File Loading

```php
use JUANDirectory\Core\Loader;

$loader = new Loader();

if ($loader->fileExists('config/settings.php')) {
    $loader->loadFile('config/settings.php');
}

$files = $loader->getFiles('views', '*.php');
```

## Security

### CSRF Protection

```php
// Generate token
$token = $this->generateCsrfToken();

// Validate token
if ($this->validateCsrfToken($_POST['_csrf_token'])) {
    // Safe to process
}
```

### Input Validation

```php
$validator = new Validator();
$validator->validate($data, [
    'email' => 'required|email',
    'password' => 'required|min:8'
]);
```

## Testing

Run PHPUnit tests:

```bash
./vendor/bin/phpunit
```

Run specific test:

```bash
./vendor/bin/phpunit tests/Unit/CoreTest.php
```

## Performance Tips

1. **Enable Caching**
   ```php
   $app->setConfig('cache.enabled', true);
   ```

2. **Use Singletons for Heavy Services**
   ```php
   $container->singleton('database', DatabaseConnection::class);
   ```

3. **Optimize Database Queries**
   - Use indexes
   - Implement query caching
   - Use prepared statements

4. **Enable Production Mode**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   ```

## Troubleshooting

### Autoloading Issues
- Run `composer dump-autoload -o`
- Clear cache: `php -r "opcache_reset();"`

### Permission Errors
- Ensure web server has write access to storage and logs directories
- Run: `chmod -R 775 storage/ logs/`

### Database Connection
- Check credentials in `.env`
- Verify database exists
- Check user permissions

## Migration from Legacy Code

See [PSR4_MIGRATION_GUIDE.md](PSR4_MIGRATION_GUIDE.md) for detailed migration instructions.

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Write tests
5. Submit a pull request

## Code Standards

- Follow PSR-12 coding standards
- Use type hints for all parameters and return types
- Add PHPDoc comments for all public methods
- Write unit tests for new features

## License

MIT License - see LICENSE file for details

## Support

For issues and feature requests, please use GitHub Issues.

For documentation, visit: [https://juandirectory.dev/docs](https://juandirectory.dev/docs)

## Changelog

### Version 2.0.0 (Current)
- ✨ Complete PSR-4 refactoring
- ✨ Added Dependency Injection Container
- ✨ Improved Device Detection
- ✨ Enhanced Security Features
- 🐛 Fixed legacy autoloading issues
- 📚 Comprehensive documentation
- 🧪 Unit test framework

### Version 1.0.0 (Legacy)
- Initial release with custom autoloader

## Author

John Virdi V. Alfonso
- Email: jva.ipampanga@gmail.com
- GitHub: [@idrivjohn](https://github.com/idrivjohn)

## Acknowledgments

Thanks to all contributors and the PHP community for inspiration and standards.
