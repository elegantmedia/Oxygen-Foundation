<?php


namespace ElegantMedia\OxygenFoundation\Navigation;

use Illuminate\Support\Collection;

class Navigator
{

	protected $navBars;

	public const DEFAULT_NAME = 'default';

	public function __construct()
	{
		$this->navBars = new Collection();
	}

	/**
	 *
	 * Get the current instance
	 *
	 * @return $this
	 */
	public function get(): Navigator
	{
		return $this;
	}

	/**
	 *
	 * Create and return a new NavBar
	 *
	 * @param $navBarName
	 *
	 * @return NavBar
	 */
	public function newNavBar($navBarName): NavBar
	{
		$navBar = new NavBar($navBarName);

		$this->navBars->push($navBar);

		return $navBar;
	}

	/**
	 *
	 * Find an existing NavBar
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

		if (!$navBar) {
			$navBar = $this->newNavBar($navBarName);
		}

		return $navBar;
	}

	/**
	 *
	 * Add a NavItem to an existing NavBar
	 *
	 * @param array|NavItem $item
	 * @param string $parentName
	 *
	 * @return $this
	 */
	public function addItem($item, $parentName = self::DEFAULT_NAME): self
	{
		$navBar = $this->getNavBar($parentName);

		if (!$navBar) {
			$navBar = $this->newNavBar($parentName);
		}

		if (is_array($item)) {
			$item = new NavItem($item);
		}

		$navBar->add($item);

		return $this;
	}

	/**
	 *
	 * Get list of items for a Requested NavBar
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
