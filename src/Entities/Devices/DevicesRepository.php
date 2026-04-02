<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Entities\Devices;

use ElegantMedia\OxygenFoundation\Entities\OxygenRepository;

class DevicesRepository extends OxygenRepository
{
	public function __construct(?Device $model = null)
	{
		$device = $model ?? new Device();

		parent::__construct($device);
	}

	/**
	 * Create a device or update an existing one if found.
	 *
	 * @param array $data
	 * @param mixed|null $userID
	 * @return Device
	 */
	public function createOrUpdateByIDAndType(array $data, $userID = null)
	{
		if (empty($data['device_id']) || empty($data['device_type'])) {
			throw new \InvalidArgumentException('device_id and device_type are required parameters');
		}

		$request = request();

		/** @var Device|null $device */
		$device = $this->newQuery()
			->where('device_id', $data['device_id'])
			->where('device_type', strtolower((string) $data['device_type']))
			->first();

		if ($device) {
			if ($userID) {
				$device->user()->associate($userID);
			}
			if ($request) {
				$device->latest_ip_address = $request->ip();
			}
			if (array_key_exists('device_push_token', $data)) {
				$device->device_push_token = $data['device_push_token'];
			}
			$device->refreshAccessToken();

			return $device;
		}

		if ($userID) {
			$data['user_id'] = $userID;
		}

		if ($request) {
			$data['latest_ip_address'] = $request->ip();
		}

		/** @var Device $created */
		$created = $this->create($data);

		return $created;
	}

	public function findByDeviceForUser($userId, $deviceId): ?Device
	{
		return $this->newQuery()
			->where('device_id', $deviceId)
			->where('user_id', $userId)
			->first();
	}

	/**
	 * Return devices by access token.
	 */
	public function getByToken(string $accessToken)
	{
		$hashedToken = Device::hashAccessToken($accessToken);

		return $this->newQuery()->where('access_token', $hashedToken)->get();
	}

	/**
	 * Get all devices by a user ID.
	 */
	public function getByUserId($userId)
	{
		return $this->newQuery()->where('user_id', $userId)->get();
	}

	public function getByIdAndType($deviceId, $deviceType): ?Device
	{
		return $this->newQuery()
			->where('device_id', $deviceId)
			->where('device_type', strtolower((string) $deviceType))
			->first();
	}

	/**
	 * Reset access tokens for all devices by a user.
	 */
	public function resetAccessTokensByUserId($userId): void
	{
		$devices = $this->getByUserId($userId);

		foreach ($devices as $device) {
			$device->resetAccessToken();
		}
	}

	/**
	 * Delete a device by device ID.
	 */
	public function deleteByDeviceId($deviceId): void
	{
		$devices = $this->newQuery()->where('device_id', $deviceId)->get();

		foreach ($devices as $device) {
			$device->delete();
		}
	}

	/**
	 * Delete a device by access token.
	 */
	public function deleteByToken(string $accessToken): void
	{
		$devices = $this->getByToken($accessToken);

		foreach ($devices as $device) {
			$device->delete();
		}
	}
}
