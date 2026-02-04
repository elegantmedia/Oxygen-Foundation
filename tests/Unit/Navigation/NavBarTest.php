<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Unit\Navigation;

use ElegantMedia\OxygenFoundation\Facades\Navigator;
use ElegantMedia\OxygenFoundation\Navigation\NavItem;
use ElegantMedia\OxygenFoundation\Tests\TestCase;

class NavBarTest extends TestCase
{
	/**
	 * Test NavBar.
	 */
	public function testNavBarReturnsCorrectNavItems()
	{
		$this->assertEquals(0, Navigator::items()->count());

		$text = 'foobar';
		$item = new NavItem('foo');

		Navigator::addItem($item);
		$items = Navigator::items();
		$this->assertEquals($items->first()->getText(), $item->getText());

		$differentItemText = 'bar';
		$differentItem = new NavItem('foo');

		$parent = 'baz';
		Navigator::addItem($differentItem, $parent);
		Navigator::addItem(new NavItem('Second'), $parent);

		$this->assertEquals(1, Navigator::items('default')->count());
		$this->assertEquals(2, Navigator::items($parent)->count());
	}

	/**
	 * Test NavBar Item Sorting.
	 */
	public function testNavBarItemsAreCorrectlySorted()
	{
		$first = new NavItem();
		$first->setOrder(1)->setText('first');

		$second = new NavItem();
		$second->setOrder(2)->setText('second');

		$third = new NavItem();
		$third->setText('a_third');

		$fourth = new NavItem();
		$fourth->setText('b_fourth');

		Navigator::addItem($fourth);
		Navigator::addItem($second);
		Navigator::addItem($first);
		Navigator::addItem($third);

		$navBar = Navigator::getNavBar();

		$item = $navBar->items()->slice(0, 1);

		// the correct order will be order by number first and then alphabetically
		// default number is zero.

		$this->assertEquals($third->getText(), $navBar->items()->slice(0, 1)->first()->getText());
		$this->assertEquals($fourth->getText(), $navBar->items()->slice(1, 1)->first()->getText());
		$this->assertEquals($first->getText(), $navBar->items()->slice(2, 1)->first()->getText());
		$this->assertEquals($second->getText(), $navBar->items()->slice(3, 1)->first()->getText());
	}

	/**
	 * Sorting handles negatives, duplicates, and ties by text.
	 */
	public function testNavBarSortingHandlesNegativeAndDuplicates(): void
	{
		$neg = new NavItem();
		$neg->setOrder(-5)->setText('z_neg');

		$zeroB = new NavItem();
		$zeroB->setOrder(0)->setText('b_zero');

		$zeroA = new NavItem();
		$zeroA->setOrder(0)->setText('a_zero');

		$oneB = new NavItem();
		$oneB->setOrder(1)->setText('beta');

		$oneA = new NavItem();
		$oneA->setOrder(1)->setText('alpha');

		$high = new NavItem();
		$high->setOrder(10)->setText('x_high');

		// Add in unsorted order
		Navigator::addItem($oneB);
		Navigator::addItem($zeroB);
		Navigator::addItem($high);
		Navigator::addItem($neg);
		Navigator::addItem($oneA);
		Navigator::addItem($zeroA);

		$navBar = Navigator::getNavBar();
		$sorted = $navBar->items()->values();

		$expected = [
			'z_neg',   // -5
			'a_zero',  // 0 (tie by text)
			'b_zero',  // 0
			'alpha',   // 1 (tie by text)
			'beta',    // 1
			'x_high',  // 10
		];

		$this->assertSame($expected, $sorted->pluck('text')->all());
	}

	public function testNavBarCanFindItemRecursively(): void
	{
		$parent = new NavItem('Parent');
		$parent->setId('parent-id');

		$child = new NavItem('Child');
		$child->setId('child-id');

		$grandchild = new NavItem('Grandchild');
		$grandchild->setId('grandchild-id');

		$child->addChild($grandchild);
		$parent->addChild($child);

		Navigator::addItem($parent);

		$navBar = Navigator::getNavBar();

		$foundParent = $navBar->getItemRecursive('parent-id');
		$this->assertNotNull($foundParent);
		$this->assertEquals('Parent', $foundParent->getText());

		$foundChild = $navBar->getItemRecursive('child-id');
		$this->assertNotNull($foundChild);
		$this->assertEquals('Child', $foundChild->getText());

		$foundGrandchild = $navBar->getItemRecursive('grandchild-id');
		$this->assertNotNull($foundGrandchild);
		$this->assertEquals('Grandchild', $foundGrandchild->getText());

		$notFound = $navBar->getItemRecursive('nonexistent');
		$this->assertNull($notFound);
	}

	public function testNavigatorCanAddChildItem(): void
	{
		$parent = new NavItem('Parent');
		$parent->setId('parent-id');

		Navigator::addItem($parent);

		$child = new NavItem('Child');
		Navigator::addChildItem($child, 'parent-id');

		$navBar = Navigator::getNavBar();
		$foundParent = $navBar->getItem('parent-id');

		$this->assertTrue($foundParent->hasChildren());
		$this->assertEquals('Child', $foundParent->getChildren()->first()->getText());
	}

	public function testNavigatorCanHideNestedItems(): void
	{
		$parent = new NavItem('Parent');
		$parent->setId('parent-id');

		$child = new NavItem('Child');
		$child->setId('child-id');

		$parent->addChild($child);
		Navigator::addItem($parent);

		Navigator::hideItem('child-id');

		$navBar = Navigator::getNavBar();
		$foundChild = $navBar->getItemRecursive('child-id');

		$this->assertTrue($foundChild->isHidden());
	}
}
