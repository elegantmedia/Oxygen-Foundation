<?php
namespace ElegantMedia\OxygenFoundation\Core;

use Illuminate\Support\Collection;

class ExtensionManager
{

	protected $extensions;

	public function __construct()
	{
		$this->extensions = new Collection();
	}

	public function load($extensionName)
	{
		// TODO: this input must be a common interface

		$this->extensions->push($extensionName);
	}
}
