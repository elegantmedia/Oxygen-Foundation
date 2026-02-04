<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Navigation;

use Illuminate\Support\Collection;

class NavBar
{
	protected $name = Navigator::DEFAULT_NAME;

	protected $items;

	public function __construct($navBarName = Navigator::DEFAULT_NAME)
	{
		$this->setName($navBarName);

		$this->items = new Collection();
	}

	/**
	 * Add a new NavItem.
	 */
	public function add(NavItem $item): NavBar
	{
		$this->items->push($item);

		return $this;
	}

	/**
	 * Get a list of menu items.
	 */
	public function items(): Collection
	{
		$sorted = $this->items->sort(function ($first, $second) {
			// ensure orders are numbers, just for safety
			if (! is_int($first->getOrder())) {
				$first->setOrder(0);
			}

			if (! is_int($second->getOrder())) {
				$second->setOrder(0);
			}

			// if the sort order is equal, then sort by text
			if ($first->getOrder() === $second->getOrder()) {
				return strcmp(strtolower($first->getText()), strtolower($second->getText()));
			}

			// otherwise sort by numeric order (ascending)
			return $first->getOrder() <=> $second->getOrder();
		});

		return $sorted;
	}

	public function getItem($itemId): ?NavItem
	{
		return $this->items->first(function ($item) use ($itemId) {
			return $item->getId() === $itemId;
		});
	}

	/**
	 * Find an item by ID, searching recursively through all children.
	 */
	public function getItemRecursive(string $itemId): ?NavItem
	{
		foreach ($this->items as $item) {
			if ($item->getId() === $itemId) {
				return $item;
			}

			if ($item->hasChildren()) {
				$found = $this->findInChildren($item, $itemId);
				if ($found) {
					return $found;
				}
			}
		}

		return null;
	}

	/**
	 * Recursively search for an item in children.
	 */
	protected function findInChildren(NavItem $parent, string $itemId): ?NavItem
	{
		foreach ($parent->getChildren() as $child) {
			if ($child->getId() === $itemId) {
				return $child;
			}

			if ($child->hasChildren()) {
				$found = $this->findInChildren($child, $itemId);
				if ($found) {
					return $found;
				}
			}
		}

		return null;
	}

	public function setName(string $name): NavBar
	{
		$this->name = $name;

		return $this;
	}

	public function getName(): string
	{
		return $this->name;
	}
}
