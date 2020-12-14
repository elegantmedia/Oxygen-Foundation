<?php

namespace ElegantMedia\OxygenFoundation\Navigation;

use ElegantMedia\PHPToolkit\Types\HasAttributes;
use Illuminate\Contracts\Support\Arrayable;

/* @property id string Unique ID of the NavItem  */
/* @property $text string Nav Item displayed text */
/* @property $class string Get  */
/* @property $icon_class string Icon class for the item  */
/* @property $url string URL for the item  */
/* @property $resource string  */
/* @property $order int  */
/* @property $hidden boolean Is hidden? */
/* @property $permission string  */
class NavItem implements Arrayable
{

	use HasAttributes;

	public function __construct($attributes = null)
	{
		if (is_string($attributes)) {
			$this->text = $attributes;
		}

		if (is_array($attributes)) {
			$this->attributes = $attributes;
		}

		if (!isset($this->attributes['order'])) {
			$this->order = 0;
		}

		if (!isset($this->attributes['hidden'])) {
			$this->hidden = false;
		}
	}


	/**
	 * @return bool
	 */
	public function hasResource(): bool
	{
		return !empty($this->resource);
	}

	/**
	 * @return bool
	 */
	public function hasValidResource(): bool
	{
		if (!$this->hasResource()) {
			return false;
		}

		return \Illuminate\Support\Facades\Route::has($this->resource);
	}

	public function userAllowedToSee(): bool
	{
		return $this->isUserAllowedToSee();
	}

	/**
	 * @return bool
	 */
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

		$user = auth()->user();

		if (!$user) {
			return false;
		}

		return $user->can($permission);
	}

	/**
	 * @return bool
	 */
	public function hasIcon(): bool
	{
		return !empty($this->icon_class);
	}


	/**
	 * Get the instance as an array.
	 *
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'id' => $this->id,
			'text' => $this->text,
			'url' => $this->url,
			'resource' => $this->resource,
			'class' => $this->class,
			'order' => $this->order,
			'permission' => $this->permission,
			'hidden' => $this->hidden,
		];
	}

	/*
	 |-----------------------------------------------------------
	 | Getters and Setters
	 |-----------------------------------------------------------
	 */

	/**
	 * @param mixed $text
	 *
	 * @return NavItem
	 */
	public function setText($text): self
	{
		$this->attributes['text'] = $text;

		return $this;
	}

	/**
	 * @param mixed $url
	 *
	 * @return NavItem
	 */
	public function setUrl($url): self
	{
		$this->attributes['url'] = $url;

		return $this;
	}

	/**
	 * @param mixed $resource
	 *
	 * @return NavItem
	 */
	public function setResource($resource): self
	{
		$this->resource = $resource;

		return $this;
	}

	/**
	 * @param mixed $class
	 *
	 * @return NavItem
	 */
	public function setClass($class): self
	{
		$this->class = $class;

		return $this;
	}

	/**
	 * @param mixed $order
	 *
	 * @return NavItem
	 */
	public function setOrder(int $order): self
	{
		$this->order = (int) $order;

		return $this;
	}

	/**
	 * @param string $class
	 *
	 * @return $this
	 */
	public function setIconClass(string $class): self
	{
		$this->icon_class = $class;

		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getPermission()
	{
		return $this->permission;
	}

	/**
	 * @param mixed $permission
	 */
	public function setPermission($permission): self
	{
		$this->permission = $permission;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getOrder(): int
	{
		return $this->order;
	}

	/**
	 * @return null|string
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * @return mixed
	 */
	public function getText(): string
	{
		return $this->attributes['text'];
	}

	public function hasUrl()
	{
		return !is_null($this->getUrl());
	}

	/**
	 * @return null|string
	 */
	public function getUrl()
	{
		if (!empty($this->attributes['url'])) {
			return $this->attributes['url'];
		}

		if ($this->hasValidResource()) {
			return route($this->resource);
		}

		return null;
	}

	public function getId()
	{
		// return the unique ID for function
		if ($this->id) {
			return $this->id;
		}

		return $this->getUrl();
	}

	/**
	 * @return null|string
	 */
	public function getClass()
	{
		return $this->class;
	}

	public function setHidden($hidden = true)
	{
		$this->hidden = $hidden;

		return $this;
	}

	public function isHidden()
	{
		return $this->hidden;
	}
}
