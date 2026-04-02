<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Unit\Navigation;

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

	public function testNavItemCanSetAndGetId(): void
	{
		$item = new NavItem('My Menu');
		$item->setId('custom-id-123');

		$this->assertEquals('custom-id-123', $item->getId());

		$array = $item->toArray();
		$this->assertEquals('custom-id-123', $array['id']);
	}

	public function testNavItemIsActiveWhenManuallySet(): void
	{
		$item = new NavItem('My Menu');

		$item->setActive(true);
		$this->assertTrue($item->isActive());

		$item->setActive(false);
		$this->assertFalse($item->isActive());
	}

	public function testNavItemCanSetAndGetActiveClass(): void
	{
		$item = new NavItem('My Menu');
		$item->setActiveClass('active-link');

		$this->assertEquals('active-link', $item->getActiveClass());

		$array = $item->toArray();
		$this->assertEquals('active-link', $array['active_class']);
	}

	public function testNavItemCanHaveChildren(): void
	{
		$parent = new NavItem('Parent');

		$child1 = new NavItem('Child 1');
		$child2 = new NavItem('Child 2');

		$parent->addChild($child1)->addChild($child2);

		$this->assertTrue($parent->hasChildren());
		$this->assertEquals(2, $parent->getChildren()->count());
		$this->assertEquals('Child 1', $parent->getChildren()->first()->getText());
	}

	public function testNavItemChildrenHaveParentReference(): void
	{
		$parent = new NavItem('Parent');
		$child = new NavItem('Child');

		$parent->addChild($child);

		$this->assertSame($parent, $child->getParent());
	}

	public function testNavItemChildrenAreSorted(): void
	{
		$parent = new NavItem('Parent');

		$child1 = new NavItem('B Child');
		$child1->setOrder(2);

		$child2 = new NavItem('A Child');
		$child2->setOrder(1);

		$child3 = new NavItem('C Child');
		$child3->setOrder(1);

		$parent->addChild($child1)->addChild($child2)->addChild($child3);

		$children = $parent->getChildren();

		$this->assertEquals('A Child', $children->values()->get(0)->getText());
		$this->assertEquals('C Child', $children->values()->get(1)->getText());
		$this->assertEquals('B Child', $children->values()->get(2)->getText());
	}

	public function testNavItemToArrayIncludesChildren(): void
	{
		$parent = new NavItem('Parent');
		$parent->setUrl('/parent');

		$child = new NavItem('Child');
		$child->setUrl('/child');

		$parent->addChild($child);

		$array = $parent->toArray();

		$this->assertArrayHasKey('children', $array);
		$this->assertCount(1, $array['children']);
		$this->assertEquals('Child', $array['children'][0]['text']);
	}

	public function testNavItemWithoutChildrenHasNoChildrenKey(): void
	{
		$item = new NavItem('Item');
		$item->setUrl('/item');

		$array = $item->toArray();

		$this->assertArrayNotHasKey('children', $array);
		$this->assertFalse($item->hasChildren());
	}
}
