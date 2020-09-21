<?php


namespace ElegantMedia\OxygenFoundation\Tests\Feature;

use ElegantMedia\OxygenFoundation\Core\Pathfinder;
use ElegantMedia\OxygenFoundation\OxygenFoundationServiceProvider;
use ElegantMedia\OxygenFoundation\TestPackage\TestPackageServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{

	protected $pathfinder;

	public function __construct()
	{
		parent::__construct();

		$this->pathfinder = app(Pathfinder::class);
	}

	protected function getPackageProviders($app)
	{
		return [
			OxygenFoundationServiceProvider::class,
			TestPackageServiceProvider::class
		];
	}
}
