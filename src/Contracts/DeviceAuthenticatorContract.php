<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Contracts;

interface DeviceAuthenticatorContract
{
	/**
	 * Return a valid access token for a given user ID.
	 *
	 * @param mixed $userId
	 * @return string|null
	 */
	public function getAnAccessTokenForUserId($userId);

	/**
	 * Returns a user by a given access token (from request headers).
	 *
	 * @param bool $throwNotFoundException
	 * @return mixed|null
	 */
	public function getUserByAccessToken($throwNotFoundException = true);

	/**
	 * Get an access token for a device & user.
	 *
	 * @param mixed $deviceId
	 * @param mixed $userId
	 * @return string|null
	 */
	public function getTokenByDeviceByUser($deviceId, $userId);

	/**
	 * Create or refresh a device token.
	 *
	 * @param mixed $deviceId
	 * @param mixed $deviceType
	 * @param mixed $devicePushToken
	 * @param mixed $userId
	 * @return string|null
	 */
	public function setToken($deviceId, $deviceType, $devicePushToken, $userId);

	/**
	 * Find a device by access token.
	 *
	 * @param mixed $accessToken
	 * @return mixed|null
	 */
	public function findDeviceByToken($accessToken);

	/**
	 * Validate to see if a token exists.
	 *
	 * @param mixed $accessToken
	 * @return bool
	 */
	public function validateToken($accessToken);

	/**
	 * Delete a device by token or device ID.
	 *
	 * @param mixed $deviceId
	 * @param mixed|null $accessToken
	 * @return bool
	 */
	public function deleteByToken($deviceId, $accessToken = null);

	/**
	 * Clear access tokens by a user ID.
	 *
	 * @param mixed $userId
	 */
	public function clearAllAccessTokensByUserId($userId);

	/**
	 * Clear an access token.
	 *
	 * @param mixed $accessToken
	 */
	public function clearAccessToken($accessToken);
}
