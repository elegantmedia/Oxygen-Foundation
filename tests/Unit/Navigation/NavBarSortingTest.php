<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Unit\Navigation;

use ElegantMedia\OxygenFoundation\Facades\Navigator;
use ElegantMedia\OxygenFoundation\Navigation\NavItem;
use ElegantMedia\OxygenFoundation\Tests\TestCase;

class NavBarSortingTest extends TestCase
{
    public function testSortingHandlesNegativeDuplicatesAndTextTies(): void
    {
        $neg = (new NavItem())->setOrder(-5)->setText('z_neg');
        $zeroB = (new NavItem())->setOrder(0)->setText('b_zero');
        $zeroA = (new NavItem())->setOrder(0)->setText('a_zero');
        $oneB = (new NavItem())->setOrder(1)->setText('beta');
        $oneA = (new NavItem())->setOrder(1)->setText('alpha');
        $high = (new NavItem())->setOrder(10)->setText('x_high');

        // add in unsorted order
        Navigator::addItem($oneB);
        Navigator::addItem($zeroB);
        Navigator::addItem($high);
        Navigator::addItem($neg);
        Navigator::addItem($oneA);
        Navigator::addItem($zeroA);

        $sorted = Navigator::getNavBar()->items()->values();

        $this->assertSame([
            'z_neg',
            'a_zero',
            'b_zero',
            'alpha',
            'beta',
            'x_high',
        ], $sorted->pluck('text')->all());
    }
}

