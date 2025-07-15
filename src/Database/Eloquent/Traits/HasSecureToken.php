<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Database\Eloquent\Traits;

use ElegantMedia\OxygenFoundation\Exceptions\TokenGenerationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSecureToken
{
    /**
     * Create a cryptographically secure unique token for a given database field.
     *
     * @throws TokenGenerationException
     */
    public static function generateUniqueToken(string $field, int $length = 32): string
    {
        $maxAttempts = 10;
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            $token = Str::random($length);

            if (! static::where($field, $token)->exists()) {
                return $token;
            }

            $attempts++;
        }

        throw new TokenGenerationException(
            "Failed to generate a unique token for field '{$field}' after {$maxAttempts} attempts."
        );
    }

    /**
     * Create a cryptographically secure token with timestamp prefix.
     *
     * @throws TokenGenerationException
     */
    public static function generateTimestampedToken(string $field, int $randomLength = 24): string
    {
        $maxAttempts = 10;
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            // Use base36 encoding for timestamp to keep it shorter
            $timestamp = base_convert((string) microtime(true), 10, 36);
            $token = $timestamp . '_' . Str::random($randomLength);

            if (! static::where($field, $token)->exists()) {
                return $token;
            }

            $attempts++;
        }

        throw new TokenGenerationException(
            "Failed to generate a unique timestamped token for field '{$field}' after {$maxAttempts} attempts."
        );
    }

    /**
     * Generate a URL-safe token.
     *
     * @throws TokenGenerationException
     */
    public static function generateUrlSafeToken(string $field, int $length = 32): string
    {
        $maxAttempts = 10;
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            $token = rtrim(strtr(base64_encode(random_bytes($length)), '+/', '-_'), '=');

            if (! static::where($field, $token)->exists()) {
                return $token;
            }

            $attempts++;
        }

        throw new TokenGenerationException(
            "Failed to generate a unique URL-safe token for field '{$field}' after {$maxAttempts} attempts."
        );
    }

    /**
     * Automatically assign a token to the specified field when creating.
     */
    protected static function bootHasSecureTokenFor(string $field, string $method = 'generateUniqueToken'): void
    {
        static::creating(function (Model $model) use ($field, $method): void {
            if (empty($model->getAttribute($field))) {
                $model->setAttribute($field, static::$method($field));
            }
        });
    }
}
