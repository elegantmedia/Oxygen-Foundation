# Menu Navigator

The `Navigator` component allows you to create Navigation Menus that can be used across the application. The `Navigator` only stores the data and doesn't actually render a menu - you control the rendering in your views.

The `Navigator` can be used by any package to register navigation menu items. By default, all menu items will be added to a `NavBar` named `default`.

## Basic Usage

### Creating NavItems

```php
use \ElegantMedia\OxygenFoundation\Navigation\NavItem;
use \ElegantMedia\OxygenFoundation\Facades\Navigator;

// Create a NavItem
$navItem = new NavItem('Profile');
$navItem->setResource('admin.users.index')
        ->setOrder(2)
        ->setIconClass('fas fa-users');

// Add to default NavBar
Navigator::addItem($navItem);

// Add to a custom navbar
Navigator::addItem($navItem, 'second-navbar');
```

### Fetching Menu Items

```php
// Get the Navigator instance
$navigator = \Navigator::get();

// Get all NavItems for the default NavBar
$items = \Navigator::items();

// Get all NavItems for a custom NavBar
$items = \Navigator::items('second-navbar');
```

## NavItem Properties

```php
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

// Set Item Order (affects sorting)
$navItem->setOrder(2);

// Set Item ID (useful if you need to fetch the same item later)
// By default, the ID will be the URL
$navItem->setId('my-unique-id-1234');

// Set Permission (item will only show if user has this permission)
$navItem->setPermission('view-users');
```

## Hide Menu Items

You can explicitly hide menu items from all users by marking an item as hidden.

```php
// Get the Navigator instance
$navigator = \Navigator::get();

// Hide an item from the default menu. You have to pass the Item ID, which is the URL by default.
\Navigator::hideItem('/projects');

// Hide an item from another menu
\Navigator::hideItem('/settings', 'second-menu-name');
```

## Active State

NavItems can automatically detect if they are the currently active item based on the current URL or route.

### Auto-Detection

The `isActive()` method automatically detects if the item is active by:

1. **Route matching** - If a `resource` is set, checks if the current route matches
2. **URL matching** - Compares the item's URL path with the current request path
3. **Child activation** - A parent item is considered active if any of its children are active

```php
// Check if item is active (auto-detects based on current URL/route)
if ($navItem->isActive()) {
    // Apply active styling
}
```

### Manual Override

You can manually set the active state:

```php
$navItem->setActive(true);
$navItem->setActive(false);
```

### Active CSS Class

You can set a custom CSS class to apply when the item is active:

```php
$navItem->setActiveClass('active-link');

// Get the active class
$class = $navItem->getActiveClass();
```

## Nested Menu Items (Children)

NavItems can have child items for creating hierarchical navigation menus.

### Adding Children

```php
$parent = new NavItem('Settings');
$parent->setResource('admin.settings.index')
       ->setId('settings-menu');

$general = new NavItem('General');
$general->setResource('admin.settings.general');

$security = new NavItem('Security');
$security->setResource('admin.settings.security');

// Add children to parent
$parent->addChild($general)->addChild($security);

Navigator::addItem($parent);
```

### Adding Children via Navigator

You can also add children to an existing item by ID:

```php
// First add the parent
$parent = new NavItem('Settings');
$parent->setId('settings-menu');
Navigator::addItem($parent);

// Later, add a child by parent ID
$child = new NavItem('General');
Navigator::addChildItem($child, 'settings-menu');
```

### Working with Children

```php
// Check if item has children
if ($navItem->hasChildren()) {
    // Get children (sorted by order, then alphabetically)
    $children = $navItem->getChildren();
}

// Get parent item
$parent = $navItem->getParent();

// Check if any children are visible to the user
if ($navItem->hasVisibleChildren()) {
    // Show dropdown/submenu
}
```

### Finding Nested Items

The NavBar can find items recursively through all levels of nesting:

```php
$navBar = Navigator::getNavBar();

// Find an item at any nesting level
$item = $navBar->getItemRecursive('child-item-id');
```

## Rendering in Blade Templates

### Basic Flat Menu

```php
<ul>
    @foreach (\Navigator::getNavBar('default')->items() as $item)
        @if ($item->isUserAllowedToSee())
            <li class="{{ $item->isActive() ? 'active' : '' }}">
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

### Nested Menu with Dropdowns

```php
<ul class="nav">
    @foreach (\Navigator::getNavBar('default')->items() as $item)
        @if ($item->isUserAllowedToSee())
            <li class="{{ $item->isActive() ? 'active' : '' }} {{ $item->hasChildren() ? 'has-dropdown' : '' }}">
                @if ($item->hasUrl() && !$item->hasChildren())
                    <a href="{{ $item->getUrl() }}">
                        @if ($item->hasIcon())
                            <i class="{{ $item->icon_class }}"></i>
                        @endif
                        <span>{{ $item->text }}</span>
                    </a>
                @else
                    <span class="nav-header">
                        @if ($item->hasIcon())
                            <i class="{{ $item->icon_class }}"></i>
                        @endif
                        <span>{{ $item->text }}</span>
                    </span>
                @endif

                @if ($item->hasChildren())
                    <ul class="dropdown">
                        @foreach ($item->getChildren() as $child)
                            @if ($child->isUserAllowedToSee())
                                <li class="{{ $child->isActive() ? 'active' : '' }}">
                                    <a href="{{ $child->getUrl() }}">{{ $child->text }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </li>
        @endif
    @endforeach
</ul>
```

## Converting to Array

The `toArray()` method returns all item properties including computed values:

```php
$array = $navItem->toArray();

// Returns:
// [
//     'id' => 'computed-id',
//     'text' => 'Menu Text',
//     'url' => '/computed-url',
//     'resource' => 'route.name',
//     'class' => 'menu-class',
//     'icon_class' => 'fas fa-icon',
//     'order' => 1,
//     'permission' => 'view-menu',
//     'hidden' => false,
//     'active' => true,
//     'active_class' => 'active-link',
//     'children' => [...] // Only if hasChildren()
// ]
```

## Multiple NavBars

You can create multiple independent navigation bars:

```php
// Add items to different navbars
Navigator::addItem($dashboardItem, 'main-nav');
Navigator::addItem($profileItem, 'user-nav');
Navigator::addItem($settingsItem, 'footer-nav');

// Retrieve items from specific navbars
$mainItems = Navigator::items('main-nav');
$userItems = Navigator::items('user-nav');
$footerItems = Navigator::items('footer-nav');
```

## Notes

- Items are ordered by `order` (ascending) and then by `text` for ties.
- You can gate visibility per item using `$item->permission` and `isUserAllowedToSee()`.
