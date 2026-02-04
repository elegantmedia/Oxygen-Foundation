# Oxygen Foundation

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)

## Version Compatibility and Upgrading

If you're upgrading or want to find an older version, please review the [CHANGELOG](CHANGELOG.md) for notable changes and upgrade notes.

## Install

Install via Composer

``` bash
composer require elegantmedia/oxygen-foundation
```

Install the Foundation
```
php artisan oxygen:foundation:install
```

## How to use

Run all extension seeders

``` php
php artisan oxygen:seed
```

### Available Functions

``` php
// Check if a feature exists
has_feature('features.name'): bool

// Convert a date to Standard date-time format
standard_datetime($date);

// Convert a date to Standard date format
standard_date($date);

// Convert a date to Standard time format
standard_time($date);
```

### Models

Make a model searchable (Laravel Scout "keyword" engine)

``` php
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

use ElegantMedia\OxygenFoundation\Scout\KeywordSearchable;

class Car extends Model implements KeywordSearchable
{
    use Searchable;

	public function getSearchableFields(): array
	{
		return [
			'make',
			'model',
		];
	}
}

// Usage
// Ensure Scout driver is set to "keyword" (in config/scout.php or at runtime):
// config(['scout.driver' => 'keyword']);
// Then perform a search:
// Car::search('tesla')->get();

Note: The package registers a secure in-database Scout engine under the `keyword` driver.
Implementing `getSearchableFields()` is required for searchable models.

### Database Traits

Add a secure UUID and token to your models with built-in traits:

``` php
use Illuminate\Database\Eloquent\Model;
use ElegantMedia\OxygenFoundation\Database\Eloquent\Traits\HasUuid;
use ElegantMedia\OxygenFoundation\Database\Eloquent\Traits\HasSecureToken;

class ApiClient extends Model
{
    use HasUuid;         // Provides a uuid column and route key
    use HasSecureToken;  // Use helper methods to generate secure tokens
}

// Examples:
// $token = ApiClient::generateUniqueToken('token');
// $token = ApiClient::generateTimestampedToken('token');
// $token = ApiClient::generateUrlSafeToken('token');
```

Deprecated: `CreatesUniqueTokens` is kept for BC but should be replaced with `HasSecureToken`.

### Components

#### Menu Navigator

[Navigation Menu Developer Guide](`docs/references/Navigation.md`)

### Schema Macros

Convenience macros are available on `Blueprint` once the service provider is loaded:

``` php
Schema::create('files', function (Blueprint $table) {
    $table->id();
    $table->file('file'); // adds file-related columns (uuid, name, path, uploaded_by_user_id, etc.)
    $table->timestamps();
});

// Drop them later
Schema::table('files', function (Blueprint $table) {
    $table->dropFile('file');
});

// Location and place helpers
Schema::table('events', function (Blueprint $table) {
    $table->location('venue'); // latitude/longitude + indexes
    $table->place('venue');    // venue/address/city/state/zip/country + location
});
```



## Testing

Run the test suite:

```bash
composer test
```

### Code Coverage

To generate code coverage reports, you need to install a coverage driver. We recommend using PCOV for better performance:

#### Installing PCOV (Recommended)

On macOS with Homebrew:
```bash
brew tap shivammathur/extensions
brew install shivammathur/extensions/pcov@8.3
```

On Ubuntu/Debian:
```bash
sudo apt-get install php-pcov
```

After installation, run tests with coverage:
```bash
composer test-coverage-pcov
```

#### Alternative: Using Xdebug

If you already have Xdebug installed, you can use it for coverage:
```bash
composer test-coverage
```

Coverage reports will be generated in the `build/coverage` directory.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

Copyright (c) Elegant Media.

[ico-version]: https://img.shields.io/packagist/v/elegantmedia/oxygen-foundation.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/elegantmedia/oxygen-foundation
