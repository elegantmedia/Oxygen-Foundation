<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Feature;

use App\Entities\Examples\Example;
use ElegantMedia\OxygenFoundation\Core\Pathfinder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExtensionAutoSeedersTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * Setup the test environment.
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->loadMigrationsFrom(__DIR__ . '/../laravel/database/migrations');

		$this->artisan('migrate', ['--database' => 'testing']);
	}

	/**
	 * Define environment setup.
	 *
	 * @param \Illuminate\Foundation\Application $app
	 */
	protected function getEnvironmentSetUp($app)
	{
		$app['config']->set('database.default', 'testing');
	}

	/**
	 * Test if extension's auto-seeders are detected.
	 */
	public function testExtensionAutoSeederRunsTests(): void
	{
		// we're mocking the auto-seeder's source path
		// so we don't have to duplicate the file(s)
		$testSeedersDir = __DIR__ . '/../laravel/database/seeders/OxygenExtensions/AutoSeed';

		$this->mock(Pathfinder::class, function ($mock) use ($testSeedersDir) {
			/* @var Mockery $mock */
			$mock->shouldReceive()->dbAutoSeedersDir()->once()
				->andReturn($testSeedersDir);
		});

		$beforeCount = Example::whereName('TestExample_001')->count();

		$this->artisan('oxygen:seed');

		$afterCount = Example::whereName('TestExample_001')->count();

		$this->assertEquals($afterCount, $beforeCount + 1, 'Record not seeded in database.');
	}
}
