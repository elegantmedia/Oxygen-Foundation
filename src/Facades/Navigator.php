<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Facades;

use ElegantMedia\OxygenFoundation\Navigation\NavBar;
use ElegantMedia\OxygenFoundation\Navigation\NavItem;
use Illuminate\Support\Collection;

/**
 * @method static Navigator get()
 * @method static NavBar    getNavBar(string $navBarName)
 * @method static Navigator addItem(NavItem $item, $navBarName = 'default');
 * @method static Collection items($navBarName = 'default');
 */
class Navigator extends \Illuminate\Support\Facades\Facade
{
	protected static function getFacadeAccessor()
	{
		return 'elegantmedia.oxygen.navigator';
	}
}
