# Oxygen Foundation

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

### Components

#### Menu Navigator

You can use the `Navigator` to create Navigation Menus that can be used across the application. The `Navigator` only stores the data and doesn't actually render a menu.

The `Navigator` can be used by any package to register navigation menu items. By default, all menu items will be added to a `NavBar` named `default`.

``` php
use \ElegantMedia\OxygenFoundation\Navigation\NavItem;
use \ElegantMedia\OxygenFoundation\Facades\Navigator;

// Create a NavItem
$navItem = new NavItem('Profile');
$navItem->setResource('admin.users.index')
        ->setOrder(2)
        ->setClass('fas fa-users');

// Add to default NavBar
Navigator::addItem($navItem);

// Add to a custom navbar
Navigator::addItem($navItem, 'second-navbar');
```

Fetching the Menu Items
``` php
// Get the Navigator instance
$navigator = \Navigator::get();

// Get all NavItems for the default NavBar
$items = \Navigator::items();

// Get all NavItems for a custom NavBar
$items = \Navigator::items('second-navbar');
```

NavItem Properties

These are a few examples of available properties.

``` php
$navItem = new NavItem();

// Set displayed text
$navItem->setText('My Profile');

// Set URL
$navItem->setUrl('/profile');

// Set Route Resource By Name
$navItem->setResource('view.profile');

// Set Class
$navItem->setClass('menu-lg');

// Set Icon Class
$navItem->setIconClass('fas fa-users');

// Set Item Order
$navItem->setOrder(2);
```

Example on how to render Navigation within a Blade template

``` php
<ul>
    @foreach (\Navigator::getNavBar('default')->items() as $item)
        @if ($item->userAllowedToSee())
            <li>
                @if ($item->hasUrl())
                    <a href="{{ $item->getUrl() }}">
                        @endif
                        @if ($item->hasIcon())
                            <i class="{{ $item->icon_class }}"></i>
                        @endif
                        <span>{{ $item->text }}</span>
                        @if ($item->hasUrl())
                    </a>
                @endif
            </li>
        @endif
    @endforeach
</ul>
```



## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

Copyright (c) 2020 Elegant Media.
