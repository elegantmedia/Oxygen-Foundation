<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Components\Navigator;

use ElegantMedia\OxygenFoundation\Navigation\NavItem;

class NavItemTest extends \PHPUnit\Framework\TestCase
{
    public function test_nav_item_to_array_returns_an_array(): void
    {
        $text = 'foo';

        $item = new NavItem;
        $item->setText($text);

        $arr = $item->toArray();

        $this->assertEquals($arr['text'], $text);
    }

    public function test_nav_item_can_initiate()
    {
        $item = new NavItem('My Menu');

        $array = $item->toArray();

        $this->assertEquals($array['text'], 'My Menu');
    }
}
