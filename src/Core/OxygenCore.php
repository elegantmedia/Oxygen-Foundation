<?php
namespace ElegantMedia\OxygenFoundation\Core;

class OxygenCore
{

	protected $extensionManager;

	public function __construct()
	{
		$this->extensionManager = new ExtensionManager();
	}

	public function register($namespace)
	{
		$this->extensionManager->load($namespace);
	}

	public static function getUserClass()
	{
		$config = app()->make('config');

		if (is_null($guard = $config->get('auth.defaults.guard'))) {
			return null;
		}

		if (is_null($provider = $config->get("auth.guards.{$guard}.provider"))) {
			return null;
		}

		return $config->get("auth.providers.{$provider}.model");
	}

	public static function makeUserModel()
	{
		return app()->make(self::getUserClass());
	}
}
