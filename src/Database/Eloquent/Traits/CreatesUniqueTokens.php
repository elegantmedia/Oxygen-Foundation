<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Database\Eloquent\Traits;

use ElegantMedia\OxygenFoundation\Exceptions\TokenGenerationException;

trait CreatesUniqueTokens
{
	/**
	 * Create a unique token for a given Database field.
	 *
	 * @param int $length
	 *
	 * @throws TokenGenerationException
	 */
	public static function newUniqueToken($dbFieldName, $length = 35): string
	{
		// take the timestamp and merge with a random string
		// unlikely to cause a collision unless there's very high traffic
		// repeat iMax times and fail

		$randomToken = null;
		$iMax = 10;

		for ($i = 0; $i < $iMax; $i++) {
			$randomToken = time() . \Illuminate\Support\Str::random($length);
			$existing = self::where($dbFieldName, $randomToken)->first();
			if (! $existing) {
				return $randomToken;
			}
		}

		throw new TokenGenerationException("Failed to create a unique token. Failed after trying $iMax times.");
	}
}
