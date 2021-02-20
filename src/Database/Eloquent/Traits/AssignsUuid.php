<?php


namespace ElegantMedia\OxygenFoundation\Database\Eloquent\Traits;

use Illuminate\Support\Str;

trait AssignsUuid
{

	/**
	 *
	 * Auto-assign a UUID to a model when creating.
	 *
	 */
	public static function bootAssignsUuid(): void
	{
		self::creating(function ($model) {
			if (empty($model->uuid)) {
				$model->uuid = (string) Str::uuid();
			}
		});
	}
}
