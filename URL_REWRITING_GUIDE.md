# JUANDirectory URL Rewriting Guide

## How URL Rewriting Works

### Before (Without Rewriting)
```
http://yoursite.com/public/index.php?url=home/index
http://yoursite.com/public/product/list
```

### After (With Rewriting)
```
http://yoursite.com/
http://yoursite.com/home/index
http://yoursite.com/product/list
```

## Setup Instructions

### 1. Enable mod_rewrite in Apache

```bash
a2enmod rewrite
sudo service apache2 restart
```

### 2. Allow .htaccess Override

Edit Apache configuration (`/etc/apache2/apache2.conf` or virtual host config):

```apache
<Directory /var/www/your-project>
    AllowOverride All
</Directory>
```

### 3. Verify Files Are in Place

```
your-project/
├── .htaccess              ← Root .htaccess (rewrite /public)
├── public/
│   ├── .htaccess          ← Public .htaccess (rewrite index.php)
│   └── index.php
└── src/
    └── Controllers/
```

### 4. Test Rewriting

#### Test 1: Root URL
```
Visit: http://yoursite.com/
Should load: HomeController::actionIndex()
```

#### Test 2: Controller/Action
```
Visit: http://yoursite.com/product/list
Should load: ProductController::actionList()
```

#### Test 3: With Parameters
```
Visit: http://yoursite.com/product/view?id=5
Should load: ProductController::actionView() with id=5
```

#### Test 4: Direct File Access (should still work)
```
Visit: http://yoursite.com/public/css/style.css
Should load: public/css/style.css
```

## URL Structure After Rewriting

```
http://yoursite.com/controller/action/param1/param2?query=value
```

### Examples

| URL | Controller | Action | Params |
|-----|-----------|--------|--------|
| `/` | home | index | none |
| `/home` | home | index | none |
| `/home/index` | home | index | none |
| `/product/list` | product | list | none |
| `/product/view/5` | product | view | `['id' => '5']` |
| `/api/data?type=json` | api | data | `['type' => 'json']` |
| `/user/edit/123?save=1` | user | edit | `['id' => '123', 'save' => '1']` |

## How It Works (Step by Step)

### Request: `http://yoursite.com/product/list`

1. **Root .htaccess** rewrites `/product/list` → `/public/product/list`
2. **Public .htaccess** rewrites `/product/list` → `/public/index.php?url=product/list`
3. **index.php** passes `url=product/list` to Application
4. **Application** routes to:
   - Controller: `ProductController`
   - Action: `actionList`
5. **ProductController** executes `actionList()` method
6. **Response** sent to browser

## Testing Rewriting

### Create Test Script

Create `public/test-rewrite.php`:

```php
<?php
echo "URL: " . ($_GET['url'] ?? 'not set') . "<br>";
echo "Server: " . $_SERVER['SERVER_NAME'] . "<br>";
echo "Request: " . $_SERVER['REQUEST_URI'] . "<br>";
```

Then visit:
- `http://yoursite.com/test/rewrite` - should show `test/rewrite`
- `http://yoursite.com/public/test-rewrite.php` - should work directly

## Troubleshooting

### Issue: Getting 404 errors

**Solution 1: Check mod_rewrite is enabled**
```bash
apache2ctl -M | grep rewrite
```
Should output: `rewrite_module (shared)`

**Solution 2: Check AllowOverride**
```apache
<Directory /var/www/your-project>
    AllowOverride All  ← Must be All, not None
</Directory>
```

**Solution 3: Restart Apache**
```bash
sudo service apache2 restart
```

### Issue: .htaccess files not being read

**Symptoms:**
- Rewriting not working
- 404 errors on clean URLs

**Solutions:**
1. Check file exists: `ls -la .htaccess`
2. Check permissions: `chmod 644 .htaccess`
3. Check syntax: `apache2ctl configtest`
4. Check location: Must be in root AND public directories

### Issue: CSS/JS/Images not loading

**Problem:** Static files are being rewritten

**Solution:** Check public/.htaccess has:
```apache
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
```

These conditions prevent rewriting actual files/directories.

### Issue: Getting blank page

**Solution:**
1. Check logs: `tail -f logs/error.log`
2. Enable debug mode in config
3. Check `src/Controllers/HomeController.php` exists

## For Different Servers

### Nginx

Add to `server` block in nginx config:

```nginx
location / {
    if (!-f $request_filename) {
        if (!-d $request_filename) {
            rewrite ^(.*)$ /public/index.php?url=$1 last;
        }
    }
}
```

### IIS

Create `web.config` in root:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="Rewrite to public">
                    <match url="^(.*)$" />
                    <conditions>
                        <add input="{REQUEST_URI}" pattern="^/public/" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="/public/{R:1}" />
                </rule>
                <rule name="Rewrite index.php">
                    <match url="^(.*)$" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="/public/index.php?url={R:1}" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
```

### Local Development (PHP Built-in Server)

Create `router.php` in public directory:

```php
<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

require __DIR__ . '/index.php';
```

Run:
```bash
php -S localhost:8000 -t public router.php
```

Then visit: `http://localhost:8000/`

## Verifying URL Rewriting Works

### Quick Test

1. Create test controller:
```php
// src/Controllers/TestController.php
namespace JUANDirectory\Controllers;
use JUANDirectory\Core\Controller;

class TestController extends Controller {
    public function actionRewrite() {
        echo "URL rewriting works!";
    }
}
```

2. Visit: `http://yoursite.com/test/rewrite`

3. If you see "URL rewriting works!" - ✅ Success!

## Best Practices

### 1. Always Use Relative URLs
```php
// Good
echo '<a href="/product/list">Products</a>';

// Also good
echo '<a href="' . $app->getBaseUrl() . '/product/list">Products</a>';

// Bad - absolute path
echo '<a href="/public/product/list">Products</a>';
```

### 2. Generate URLs Dynamically
```php
<?php
// Create helper function
function url($controller, $action, $params = []) {
    $url = "/" . strtolower($controller) . "/" . strtolower($action);
    foreach ($params as $key => $value) {
        $url .= "/" . $value;
    }
    return $url;
}

// Use in views
echo '<a href="' . url('product', 'view', ['5']) . '">View</a>';
// Outputs: <a href="/product/view/5">View</a>
```

### 3. Use BASE_PATH Constant
```php
// In bootstrap or config
define('BASE_PATH', '/');

// In templates
echo '<link rel="stylesheet" href="' . BASE_PATH . 'css/style.css">';
```

## Security Notes

### 1. Protect Sensitive Directories

The .htaccess already blocks:
- `/logs/`
- `/config/`

### 2. Disable Directory Listing

Already included:
```apache
Options -Indexes
```

### 3. Add Security Headers

Already included in public/.htaccess

## Summary

✅ Root `.htaccess` - Routes requests to /public  
✅ Public `.htaccess` - Routes to index.php  
✅ Clean URLs - No /public or /index.php in address bar  
✅ Static files - Still load directly  
✅ Secure - Blocks access to logs and config  
✅ Professional - Looks like a real website  

**Result:** `http://yoursite.com/` instead of `http://yoursite.com/public/index.php` ✨
