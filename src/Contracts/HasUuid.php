<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Contracts;

interface HasUuid
{
	/**
	 * Get the UUID column name.
	 */
	public function getUuidColumn(): string;

	/**
	 * Find a model by its UUID.
	 */
	public static function findByUuid(string $uuid): ?self;
}
