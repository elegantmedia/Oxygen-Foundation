<?php
namespace ElegantMedia\OxygenFoundation\Tests\Feature;

use ElegantMedia\OxygenFoundation\Console\Commands\ExtensionSetupCommand;

use ElegantMedia\OxygenFoundation\Console\Commands\PackageSetupHandler;
use ElegantMedia\OxygenFoundation\OxygenFoundationServiceProvider;
use ElegantMedia\OxygenFoundation\TestPackage\Console\Commands\TestExtensionSetupCommand;
use ElegantMedia\OxygenFoundation\TestPackage\TestPackageServiceProvider;
use Illuminate\Support\Facades\File;

class OxygenExtensionSetupCommandTest extends \Orchestra\Testbench\TestCase
{

	protected function getMigrationsDestinationDir()
	{
		return database_path('migrations');
	}

	protected function getSeedersDestinationDir()
	{
		return database_path('seeders');
	}

	protected function getMigrationsDestinationFileCount()
	{
		return count(File::files(self::getMigrationsDestinationDir()));
	}

	protected function getSeedersDestinationFileCount()
	{
		return count(File::files($this->getSeedersDestinationDir()));
	}

	protected function getFileCountOnFolder($dirPath)
	{
		if (!is_dir($dirPath)) {
			[];
		}

		return count(File::files($dirPath));
	}

	/**
	 * Setup the test environment.
	 */
	protected function setUp(): void
	{
		parent::setUp();

		\ElegantMedia\OxygenFoundation\Support\File::cleanDirectoryByExtension($this->getMigrationsDestinationDir(), 'php');
		\ElegantMedia\OxygenFoundation\Support\File::cleanDirectoryByExtension($this->getSeedersDestinationDir(), 'php');
		\ElegantMedia\OxygenFoundation\Support\File::cleanDirectoryByExtension(app_path('Entities/Bikes'), 'php');

		// $this->withoutMockingConsoleOutput();
	}

	/**
	 * Setup the test environment.
	 */
	protected function tearDown(): void
	{
		\ElegantMedia\OxygenFoundation\Support\File::cleanDirectoryByExtension($this->getMigrationsDestinationDir(), 'php');
		\ElegantMedia\OxygenFoundation\Support\File::cleanDirectoryByExtension($this->getSeedersDestinationDir(), 'php');
		\ElegantMedia\OxygenFoundation\Support\File::cleanDirectoryByExtension(app_path('Entities/Bikes'), 'php');

		parent::tearDown();
	}

	protected function getPackageProviders($app)
	{
		return [
			OxygenFoundationServiceProvider::class,
			TestPackageServiceProvider::class
		];
	}

	public function testPackageCopiesMigrations(): void
	{
		// /** @var ExtensionSetupCommand $cmd */
		// $cmd = app(TestExtensionSetupCommand::class);

		$fileCount = $this->getFileCountOnFolder(__DIR__ . '/../TestPackage/database/migrations');
		$before = $this->getFileCountOnFolder(database_path('migrations'));

		$this->artisan('setup:extension:test-extension');

		$after = $this->getFileCountOnFolder(database_path('migrations'));

		$this->assertEquals($after, ($before + $fileCount), 'Migrations not copied to destination.');
	}


	public function testPackageCopiesSeeders(): void
	{
		$fileCount = $this->getFileCountOnFolder(__DIR__ . '/../TestPackage/database/seeders');
		$before = $this->getFileCountOnFolder(database_path('seeders'));

		$this->artisan('setup:extension:test-extension');

		$after = $this->getFileCountOnFolder(database_path('seeders'));

		$this->assertEquals($after, ($before + $fileCount), 'Seeders not copied to destination.');
	}

	public function testPackageAutoPublishesFiles(): void
	{
		$fileCount = $this->getFileCountOnFolder(__DIR__ . '/../TestPackage/publish/app/Entities/Bikes');
		$before = $this->getFileCountOnFolder(app_path('Entities/Bikes'));

		$this->artisan('setup:extension:test-extension');

		$after = $this->getFileCountOnFolder(app_path('Entities/Bikes'));

		$this->assertEquals($after, ($before + $fileCount), 'Files not auto-published to destination.');
	}
}
