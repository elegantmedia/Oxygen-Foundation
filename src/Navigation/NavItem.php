<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Navigation;

use ElegantMedia\PHPToolkit\Types\HasAttributes;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * @property string $id         Unique ID of the NavItem
 * @property string $text       Nav Item displayed text
 * @property string $class      CSS class
 * @property string $icon_class Icon class for the item
 * @property string $url        URL for the item
 * @property string $resource   Resource name
 * @property int    $order      Sort order
 * @property bool   $hidden     Is hidden?
 * @property string $permission Required permission
 * @property bool|null $active  Is active? (null = auto-detect)
 * @property string $active_class CSS class to apply when active
 * @property Collection $children Child NavItems
 */
class NavItem implements Arrayable
{
	use HasAttributes;

	protected ?NavItem $parent = null;

	public function __construct($attributes = null)
	{
		if (is_string($attributes)) {
			$this->text = $attributes;
		}

		if (is_array($attributes)) {
			$this->attributes = $attributes;
		}

		if (! isset($this->attributes['order'])) {
			$this->order = 0;
		}

		if (! isset($this->attributes['hidden'])) {
			$this->hidden = false;
		}
	}

	public function hasResource(): bool
	{
		return ! empty($this->resource);
	}

	public function hasValidResource(): bool
	{
		if (! $this->hasResource()) {
			return false;
		}

		return \Illuminate\Support\Facades\Route::has($this->resource);
	}

	public function userAllowedToSee(): bool
	{
		return $this->isUserAllowedToSee();
	}

	public function isUserAllowedToSee(): bool
	{
		if ($this->isHidden()) {
			return false;
		}

		$permission = $this->getPermission();

		// if there's no permission, allow anyone to see
		if (empty($permission)) {
			return true;
		}

		$user = Auth::guard()->user();

		if (! $user instanceof AuthorizableContract) {
			return false;
		}

		return $user->can($permission);
	}

	public function hasIcon(): bool
	{
		return ! empty($this->icon_class);
	}

	/**
	 * Get the instance as an array.
	 */
	public function toArray(): array
	{
		$array = [
			'id' => $this->getId(),
			'text' => $this->text,
			'url' => $this->getUrl(),
			'resource' => $this->resource,
			'class' => $this->class,
			'icon_class' => $this->icon_class,
			'order' => $this->order,
			'permission' => $this->permission,
			'hidden' => $this->hidden,
			'active' => $this->isActive(),
			'active_class' => $this->getActiveClass(),
		];

		if ($this->hasChildren()) {
			$array['children'] = $this->getChildren()->map(fn (NavItem $child) => $child->toArray())->values()->all();
		}

		return $array;
	}

	/*
	 |-----------------------------------------------------------
	 | Getters and Setters
	 |-----------------------------------------------------------
	 */

	/**
	 * @param mixed $text
	 */
	public function setText($text): self
	{
		$this->attributes['text'] = $text;

		return $this;
	}

	/**
	 * @param mixed $url
	 */
	public function setUrl($url): self
	{
		$this->attributes['url'] = $url;

		return $this;
	}

	/**
	 * @param mixed $resource
	 */
	public function setResource($resource): self
	{
		$this->resource = $resource;

		return $this;
	}

	/**
	 * @param mixed $class
	 */
	public function setClass($class): self
	{
		$this->class = $class;

		return $this;
	}

	/**
	 * @param int $order
	 */
	public function setOrder(int $order): self
	{
		$this->order = (int) $order;

		return $this;
	}

	/**
	 * @return $this
	 */
	public function setIconClass(string $class): self
	{
		$this->icon_class = $class;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getPermission(): ?string
	{
		return $this->permission;
	}

	/**
	 * @param string|null $permission
	 */
	public function setPermission(?string $permission): self
	{
		$this->permission = $permission;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getOrder(): int
	{
		return $this->order;
	}

	/**
	 * @return string|null
	 */
	public function getResource(): ?string
	{
		return $this->resource;
	}

	/**
	 * @return string
	 */
	public function getText(): string
	{
		return $this->attributes['text'];
	}

	public function hasUrl(): bool
	{
		return ! is_null($this->getUrl());
	}

	/**
	 * @return string|null
	 */
	public function getUrl(): ?string
	{
		if (! empty($this->attributes['url'])) {
			return $this->attributes['url'];
		}

		if ($this->hasValidResource()) {
			return route($this->resource);
		}

		return null;
	}

	public function getId(): ?string
	{
		// return the unique ID for function
		if ($this->id) {
			return $this->id;
		}

		return $this->getUrl();
	}

	/**
	 * @param string $id
	 */
	public function setId(string $id): self
	{
		$this->attributes['id'] = $id;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getClass(): ?string
	{
		return $this->class;
	}

	public function setHidden($hidden = true): self
	{
		$this->hidden = $hidden;

		return $this;
	}

	public function isHidden(): bool
	{
		return $this->hidden;
	}

	/*
	 |-----------------------------------------------------------
	 | Active State
	 |-----------------------------------------------------------
	 */

	/**
	 * Check if the nav item is currently active.
	 * Auto-detects based on current URL/route if not manually set.
	 */
	public function isActive(): bool
	{
		// If manually set, use that value
		if (isset($this->attributes['active'])) {
			return (bool) $this->attributes['active'];
		}

		if (! function_exists('app') || ! function_exists('request')) {
			return false;
		}

		$app = app();

		if (! is_object($app) || ! method_exists($app, 'runningInConsole') || $app->runningInConsole()) {
			return false;
		}

		if (! method_exists($app, 'bound') || ! $app->bound('request')) {
			return false;
		}

		$request = request();

		if (! is_object($request)) {
			return false;
		}

		// Prefer route-name matching when a resource is set
		if ($this->hasResource() && method_exists($request, 'route') && method_exists($request, 'routeIs')) {
			$pattern = str_ends_with($this->resource, '*') ? $this->resource : $this->resource . '*';

			if ($request->route() && $request->routeIs($pattern)) {
				return true;
			}
		}

		// Check if item URL matches current URL
		if ($this->hasUrl() && method_exists($request, 'getPathInfo')) {
			$itemUrl = (string) $this->getUrl();

			// Normalize to path comparison (supports absolute and relative URLs)
			$itemPath = parse_url($itemUrl, PHP_URL_PATH) ?: $itemUrl;
			$currentPath = $request->getPathInfo(); // always starts with "/"

			$itemPath = rtrim($itemPath, '/') ?: '/';
			$currentPath = rtrim($currentPath, '/') ?: '/';

			// Exact match or segment-aware prefix match
			if ($itemPath === $currentPath || ($itemPath !== '/' && str_starts_with($currentPath, $itemPath . '/'))) {
				return true;
			}
		}

		// Check if any child is active (recursive)
		if ($this->hasChildren()) {
			foreach ($this->getChildren() as $child) {
				if ($child->isActive()) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Manually set the active state.
	 */
	public function setActive(bool $active): self
	{
		$this->attributes['active'] = $active;

		return $this;
	}

	/**
	 * Get the CSS class to apply when active.
	 */
	public function getActiveClass(): ?string
	{
		return $this->active_class ?? null;
	}

	/**
	 * Set the CSS class to apply when active.
	 */
	public function setActiveClass(string $class): self
	{
		$this->attributes['active_class'] = $class;

		return $this;
	}

	/*
	 |-----------------------------------------------------------
	 | Children / Nested Items
	 |-----------------------------------------------------------
	 */

	/**
	 * Add a child nav item.
	 */
	public function addChild(NavItem $item): self
	{
		if (! isset($this->attributes['children'])) {
			$this->attributes['children'] = new Collection();
		}

		$item->setParent($this);
		$this->attributes['children']->push($item);

		return $this;
	}

	/**
	 * Get all child nav items, sorted by order then text.
	 */
	public function getChildren(): Collection
	{
		if (! isset($this->attributes['children'])) {
			return new Collection();
		}

		return $this->attributes['children']->sortBy(function (NavItem $item) {
			return [$item->getOrder(), strtolower($item->getText())];
		})->values();
	}

	/**
	 * Check if this item has children.
	 */
	public function hasChildren(): bool
	{
		return isset($this->attributes['children']) && $this->attributes['children']->count() > 0;
	}

	/**
	 * Set the parent nav item.
	 */
	public function setParent(NavItem $parent): self
	{
		$this->parent = $parent;

		return $this;
	}

	/**
	 * Get the parent nav item.
	 */
	public function getParent(): ?NavItem
	{
		return $this->parent;
	}

	/**
	 * Check if any child is visible to the user.
	 */
	public function hasVisibleChildren(): bool
	{
		if (! $this->hasChildren()) {
			return false;
		}

		foreach ($this->getChildren() as $child) {
			if ($child->isUserAllowedToSee()) {
				return true;
			}
		}

		return false;
	}
}
