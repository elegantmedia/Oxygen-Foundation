<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Entities\Devices;

use Carbon\Carbon;
use ElegantMedia\OxygenFoundation\Database\Eloquent\Traits\CreatesUniqueTokens;
use ElegantMedia\OxygenFoundation\Exceptions\TokenGenerationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property string|null     $access_token
 * @property Carbon|null     $access_token_expires_at
 * @property string|null     $device_id
 * @property string|null     $device_push_token
 * @property string|null     $device_type
 * @property string|null     $latest_ip_address
 * @property int|string|null $user_id
 */
class Device extends Model
{
	use CreatesUniqueTokens;

	protected $table = 'devices';

	protected $defaultTokenExpiryDays = 90;

	protected $fillable = [
		'device_id',
		'device_type',
		'device_push_token',
		'user_id',
		'latest_ip_address',
	];

	protected $casts = [
		'access_token_expires_at' => 'datetime',
	];

	/**
	 * Plain token is only available right after creation/refresh.
	 */
	protected ?string $plainTextAccessToken = null;

	public function scopeActive($query)
	{
		return $query->where(function ($q) {
			$q->whereNull('access_token_expires_at')
				->orWhere('access_token_expires_at', '>', Carbon::now());
		});
	}

	public function user()
	{
		$userClass = config('auth.providers.users.model');

		if ($userClass) {
			return $this->belongsTo($userClass);
		}

		return null;
	}

	/**
	 * Force device type to be lower-case.
	 */
	public function setDeviceTypeAttribute($value): void
	{
		$this->attributes['device_type'] = strtolower((string) $value);
	}

	/**
	 * Return plain text access token (only available immediately after create/refresh).
	 */
	public function getPlainTextAccessToken(): ?string
	{
		return $this->plainTextAccessToken;
	}

	/**
	 * Refresh the current access token and store the hashed value.
	 *
	 * @throws TokenGenerationException
	 */
	public function refreshAccessToken(): void
	{
		[$plainToken, $hashedToken] = static::generateHashedTokenPair();

		$this->plainTextAccessToken = $plainToken;
		$this->attributes['access_token'] = $hashedToken;
		$this->attributes['access_token_expires_at'] = Carbon::now()->addDays($this->getDefaultTokenExpiryDays());
		$this->save();
	}

	/**
	 * Reset device access token.
	 */
	public function resetAccessToken(): void
	{
		$this->plainTextAccessToken = null;
		$this->attributes['access_token'] = null;
		$this->attributes['access_token_expires_at'] = null;
		$this->save();
	}

	/**
	 * Hash a plain access token for storage/lookup.
	 */
	public static function hashAccessToken(string $plainToken): string
	{
		return hash_hmac('sha256', $plainToken, static::getAccessTokenHashKey());
	}

	/**
	 * Setup model event hooks.
	 */
	protected static function boot(): void
	{
		parent::boot();

		static::creating(function (self $model): void {
			if (empty($model->attributes['access_token'])) {
				[$plainToken, $hashedToken] = static::generateHashedTokenPair();
				$model->plainTextAccessToken = $plainToken;
				$model->attributes['access_token'] = $hashedToken;
			}

			if (empty($model->attributes['access_token_expires_at'])) {
				$model->attributes['access_token_expires_at'] = Carbon::now()->addDays($model->getDefaultTokenExpiryDays());
			}
		});
	}

	/**
	 * @return int
	 */
	public function getDefaultTokenExpiryDays(): int
	{
		return (int) $this->defaultTokenExpiryDays;
	}

	/**
	 * @param int $defaultTokenExpiryDays
	 */
	public function setDefaultTokenExpiryDays(int $defaultTokenExpiryDays): self
	{
		$this->defaultTokenExpiryDays = $defaultTokenExpiryDays;

		return $this;
	}

	/**
	 * Generate a plain/hashed token pair and guarantee hashed uniqueness.
	 *
	 * @throws TokenGenerationException
	 */
	protected static function generateHashedTokenPair(int $length = 64): array
	{
		$maxAttempts = 10;
		$attempts = 0;

		while ($attempts < $maxAttempts) {
			$plainToken = Str::random($length);
			$hashedToken = static::hashAccessToken($plainToken);

			if (! static::query()->where('access_token', $hashedToken)->exists()) {
				return [$plainToken, $hashedToken];
			}

			$attempts++;
		}

		throw new TokenGenerationException(
			"Failed to generate a unique access token after {$maxAttempts} attempts."
		);
	}

	/**
	 * Resolve the token hashing key from app.key (base64 supported).
	 */
	protected static function getAccessTokenHashKey(): string
	{
		$key = (string) config('app.key', '');

		if (Str::startsWith($key, 'base64:')) {
			$decoded = base64_decode(substr($key, 7), true);
			if ($decoded !== false) {
				$key = $decoded;
			}
		}

		return $key;
	}
}
