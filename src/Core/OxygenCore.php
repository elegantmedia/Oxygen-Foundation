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

}
