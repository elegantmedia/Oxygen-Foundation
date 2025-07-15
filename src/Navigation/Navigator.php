<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Navigation;

use Illuminate\Support\Collection;

class Navigator
{
    protected $navBars;

    public const DEFAULT_NAME = 'default';

    public function __construct()
    {
        $this->navBars = new Collection;
    }

    /**
     * Get the current instance.
     *
     * @return $this
     */
    public function get(): Navigator
    {
        return $this;
    }

    /**
     * Create and return a new NavBar.
     */
    public function newNavBar($navBarName): NavBar
    {
        $navBar = new NavBar($navBarName);

        $this->navBars->push($navBar);

        return $navBar;
    }

    /**
     * Find an existing NavBar.
     *
     * @param string $navBarName
     *
     * @return mixed
     */
    public function getNavBar($navBarName = self::DEFAULT_NAME)
    {
        $navBar = $this->navBars->first(function ($navBar) use ($navBarName) {
            return $navBar->getName() === $navBarName;
        });

        if (! $navBar) {
            $navBar = $this->newNavBar($navBarName);
        }

        return $navBar;
    }

    /**
     * Add a NavItem to an existing NavBar.
     *
     * @param array|NavItem $item
     * @param string        $parentName
     *
     * @return $this
     */
    public function addItem($item, $parentName = self::DEFAULT_NAME): self
    {
        $navBar = $this->getNavBar($parentName);

        if (! $navBar) {
            $navBar = $this->newNavBar($parentName);
        }

        if (is_array($item)) {
            $item = new NavItem($item);
        }

        $navBar->add($item);

        return $this;
    }

    /**
     * Mark a menu item as hidden, so they won't appear.
     *
     * @param string $navBarName
     */
    public function hideItem($itemId, $navBarName = self::DEFAULT_NAME): void
    {
        $navBar = $this->getNavBar($navBarName);

        $navItem = $navBar->getItem($itemId);

        if ($navItem) {
            $navItem->setHidden(true);
        }
    }

    /**
     * Get list of items for a Requested NavBar.
     *
     * @param string $navBarName
     *
     * @return mixed
     */
    public function items($navBarName = self::DEFAULT_NAME)
    {
        return $this->getNavBar($navBarName)->items();
    }
}
