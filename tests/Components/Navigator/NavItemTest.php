<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Components\Navigator;

use ElegantMedia\OxygenFoundation\Navigation\NavItem;

class NavItemTest extends \PHPUnit\Framework\TestCase
{
	public function testNavItemToArrayReturnsAnArray(): void
	{
		$text = 'foo';

		$item = new NavItem();
		$item->setText($text);

		$arr = $item->toArray();

		$this->assertEquals($arr['text'], $text);
	}

	public function testNavItemCanInitiate()
	{
		$item = new NavItem('My Menu');

		$array = $item->toArray();

		$this->assertEquals($array['text'], 'My Menu');
	}
}
