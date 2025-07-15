# Upgrade Guide: OxygenFoundation v4 to v5

This guide provides step-by-step instructions for upgrading from OxygenFoundation v4 to v5.

## Overview

OxygenFoundation v5 is a major release with breaking changes. This version targets Laravel 12+ exclusively and requires PHP 8.2 or higher. The upgrade focuses on modernization, improved architecture, and enhanced developer experience.

## Requirements

- PHP 8.2 or higher (previously 8.1+)
- Laravel 12.x (no backward compatibility)
- Updated dependencies:
  - elegantmedia/laravel-simple-repository: ^5.0
  - elegantmedia/php-toolkit: ^2.0
  - laravel/scout: ^11.0

## Breaking Changes

### 1. Namespace Changes

#### Repository Classes
```php
// Old (v4)
use ElegantMedia\OxygenFoundation\Entities\OxygenRepository;

// New (v5)
use ElegantMedia\OxygenFoundation\Repository\BaseRepository;
```

#### HTTP Traits
```php
// Old (v4)
use ElegantMedia\OxygenFoundation\Http\Traits\Web\CanCRUD;
use ElegantMedia\OxygenFoundation\Http\Traits\Web\CanBrowse;
use ElegantMedia\OxygenFoundation\Http\Traits\Web\CanCreate;
use ElegantMedia\OxygenFoundation\Http\Traits\Web\CanEdit;
use ElegantMedia\OxygenFoundation\Http\Traits\Web\CanDestroy;

// New (v5)
use ElegantMedia\OxygenFoundation\Http\Traits\Controllers\HasCrudOperations;
use ElegantMedia\OxygenFoundation\Http\Traits\Controllers\HasBrowseOperation;
use ElegantMedia\OxygenFoundation\Http\Traits\Controllers\HasCreateOperation;
use ElegantMedia\OxygenFoundation\Http\Traits\Controllers\HasEditOperation;
use ElegantMedia\OxygenFoundation\Http\Traits\Controllers\HasDeleteOperation;
```

#### Database Traits
```php
// Old (v4)
use ElegantMedia\OxygenFoundation\Database\Eloquent\Traits\AssignsUuid;
use ElegantMedia\OxygenFoundation\Database\Eloquent\Traits\CreatesUniqueTokens;

// New (v5)
use ElegantMedia\OxygenFoundation\Database\Eloquent\Traits\HasUuid;
use ElegantMedia\OxygenFoundation\Database\Eloquent\Traits\HasSecureToken;
```

### 2. Base Repository Changes

Your repository classes need to extend the new `BaseRepository`:

```php
// Old (v4)
use ElegantMedia\OxygenFoundation\Entities\OxygenRepository;

class UserRepository extends OxygenRepository
{
    // ...
}

// New (v5)
use ElegantMedia\OxygenFoundation\Repository\BaseRepository;

class UserRepository extends BaseRepository
{
    // ...
}
```

### 3. Scout Engine Security Fix

The KeywordSearchEngine has been replaced with SecureKeywordEngine to prevent SQL injection:

```php
// config/scout.php
'engines' => [
    'keyword' => [
        'driver' => 'keyword',
    ],
],
```

No code changes required - the service provider automatically registers the secure version.

### 4. Global Functions Removal

The `functions.php` file has been removed. Any global functions you were using need to be replaced:

```php
// Old (v4)
$value = some_global_function();

// New (v5)
use ElegantMedia\OxygenFoundation\Support\Helpers\SomeHelper;
$value = SomeHelper::someMethod();
```

## Step-by-Step Upgrade Process

### Step 1: Update Composer Dependencies

Update your `composer.json`:

```json
{
    "require": {
        "php": "^8.2",
        "elegantmedia/oxygen-foundation": "^5.0"
    }
}
```

### Step 2: Update Repository Classes

1. Find all classes extending `OxygenRepository`
2. Change the parent class to `BaseRepository`
3. Update the namespace import

```bash
# Find all repository files
find app -name "*Repository.php" -type f
```

### Step 3: Update Controller Traits

1. Find all controllers using the old traits
2. Replace trait names according to the mapping above

```bash
# Find controllers using old traits
grep -r "use CanCRUD" app/Http/Controllers
grep -r "use CanBrowse" app/Http/Controllers
# ... etc
```

### Step 4: Update Model Traits

Replace database traits in your models:

```php
// Old
use ElegantMedia\OxygenFoundation\Database\Eloquent\Traits\AssignsUuid;

class User extends Model
{
    use AssignsUuid;
}

// New
use ElegantMedia\OxygenFoundation\Database\Eloquent\Traits\HasUuid;

class User extends Model
{
    use HasUuid;
}
```

### Step 5: Update Configuration

Run the publish command to get the latest configuration files:

```bash
php artisan vendor:publish --tag=oxygen-config --force
```

### Step 6: Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

### Step 7: Run Tests

Ensure all your tests pass:

```bash
php artisan test
```

## New Features in v5

### 1. Enhanced Type Safety

All methods now have proper type declarations:

```php
public function fillModelFromRequest(Request $request, ?int $id = null): Model
```

### 2. Improved Security

- SQL injection prevention in Scout integration
- Cryptographically secure token generation
- Better input validation

### 3. Modern PHP Features

- Constructor property promotion
- Readonly properties
- Enums for constants
- Union types

### 4. Better Testing Support

- Comprehensive test suite
- PHPUnit 11 support
- GitHub Actions CI/CD

## Common Issues and Solutions

### Issue: Method not found after upgrade

**Solution**: Check if you're using the correct trait names and that all imports are updated.

### Issue: Type errors after upgrade

**Solution**: Update method signatures to include proper type declarations.

### Issue: Scout search not working

**Solution**: Clear Scout indices and re-import:

```bash
php artisan scout:flush "App\Models\YourModel"
php artisan scout:import "App\Models\YourModel"
```

## Getting Help

If you encounter issues during the upgrade:

1. Check the [GitHub Issues](https://github.com/elegantmedia/oxygen-foundation/issues)
2. Review the comprehensive test suite for usage examples
3. Ensure all dependencies are properly updated

## Summary

The v5 upgrade brings significant improvements in security, type safety, and modern PHP practices. While the upgrade requires some code changes, the benefits include:

- Better IDE support with proper type hints
- Improved security with SQL injection prevention
- Modern PHP 8.2 features
- Enhanced testing capabilities
- Better performance

Take time to test thoroughly after upgrading, especially around repository operations and controller CRUD functionality.