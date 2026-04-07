<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Database\Eloquent\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
	/**
	 * Initialize the trait on the model.
	 */
	public function initializeHasUuid(): void
	{
		$this->mergeFillable(['uuid']);
	}

	/**
	 * Boot the trait.
	 */
	public static function bootHasUuid(): void
	{
		static::creating(function (Model $model): void {
			if (empty($model->getAttribute('uuid'))) {
				$model->setAttribute('uuid', (string) Str::uuid());
			}
		});
	}

	/**
	 * Get the route key for the model.
	 */
	public function getRouteKeyName(): string
	{
		return property_exists($this, 'routeKeyName') ? $this->routeKeyName : 'uuid';
	}

	/**
	 * Get the UUID column name.
	 */
	public function getUuidColumn(): string
	{
		return 'uuid';
	}

	/**
	 * Retrieve the model for a bound value.
	 */
	public function resolveRouteBinding($value, $field = null): ?Model
	{
		return $this->where('uuid', $value)->firstOrFail();
	}

	/**
	 * Scope a query to find by UUID.
	 */
	public function scopeWhereUuid($query, string $uuid)
	{
		return $query->where('uuid', $uuid);
	}

	/**
	 * Find a model by its UUID.
	 */
	public static function findByUuid(string $uuid): ?self
	{
		return static::query()->where('uuid', $uuid)->first();
	}
}
