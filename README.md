# Oxygen Foundation

## Install

Install via Composer

``` bash
composer require elegantmedia/oxygen-foundation
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

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

Copyright (c) 2020 Elegant Media.
