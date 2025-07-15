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

        $this->items = new Collection;
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
     *
     * @return mixed
     */
    public function items()
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

            // otherwise sort by order
            return (int) ($first->getOrder() > $second->getOrder());
        });

        return $sorted;
    }

    public function getItem($itemId)
    {
        return $this->items->first(function ($item) use ($itemId) {
            return $item->getId() === $itemId;
        });
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
