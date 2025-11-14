<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Database\Eloquent\Traits;

use ElegantMedia\OxygenFoundation\Exceptions\TokenGenerationException;
use Illuminate\Support\Str;

trait CreatesUniqueTokens
{
    /**
     * Create a cryptographically secure unique token for a given database field.
     *
     * @throws TokenGenerationException
     */
    public static function newUniqueToken(string $dbFieldName, int $length = 35): string
    {
        $maxAttempts = 10;
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            $token = Str::random($length);

            if (! static::where($dbFieldName, $token)->exists()) {
                return $token;
            }

            $attempts++;
        }

        throw new TokenGenerationException(
            "Failed to create a unique token for field '{$dbFieldName}' after {$maxAttempts} attempts."
        );
    }

    /**
     * Create a cryptographically secure token with a high-resolution timestamp prefix.
     *
     * @throws TokenGenerationException
     */
    public static function newTimestampedToken(string $dbFieldName, int $randomLength = 24): string
    {
        $maxAttempts = 10;
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            // Use monotonic high-resolution time to avoid float precision issues
            $timestamp = base_convert((string) hrtime(true), 10, 36);
            $token = $timestamp . '_' . Str::random($randomLength);

            if (! static::where($dbFieldName, $token)->exists()) {
                return $token;
            }

            $attempts++;
        }

        throw new TokenGenerationException(
            "Failed to create a unique timestamped token for field '{$dbFieldName}' after {$maxAttempts} attempts."
        );
    }
}
