<?php


namespace ElegantMedia\OxygenFoundation\Facades;

use Illuminate\Support\Collection;

/**
 * @method static Navigator get()
 * @method static NavBar getNavBar(string $navBarName)
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
