<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Facades;

use ElegantMedia\OxygenFoundation\Navigation\NavBar;
use ElegantMedia\OxygenFoundation\Navigation\NavItem;
use Illuminate\Support\Collection;

/**
 * @method static Navigator  get()
 * @method static NavBar     getNavBar(string $navBarName = 'default')
 * @method static Navigator  addItem(NavItem|array $item, string $navBarName = 'default')
 * @method static Navigator  addChildItem(NavItem $child, string $parentId, string $navBarName = 'default')
 * @method static void       hideItem(string $itemId, string $navBarName = 'default')
 * @method static Collection items(string $navBarName = 'default')
 */
class Navigator extends \Illuminate\Support\Facades\Facade
{
	protected static function getFacadeAccessor()
	{
		return 'elegantmedia.oxygen.navigator';
	}
}
