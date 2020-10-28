<?php
namespace ElegantMedia\OxygenFoundation\Tests\Feature;

use ElegantMedia\OxygenFoundation\Console\Commands\PackageSetupHandler;
use ElegantMedia\PHPToolkit\Dir;
use ElegantMedia\PHPToolkit\Path;
use Illuminate\Support\Facades\File;

class ExtensionSetupCommandTest extends TestCase
{

	protected function getMigrationsDestinationDir()
	{
		return $this->pathfinder->dbMigrationsDir();
	}

	protected function getSeedersDestinationDir()
	{
		return $this->pathfinder->dbSeedersDir();
	}

	protected function getMigrationsDestinationFileCount()
	{
		return count(File::files(self::getMigrationsDestinationDir()));
	}

	protected function getSeedersDestinationFileCount()
	{
		return count(File::files($this->getSeedersDestinationDir()));
	}

	protected function getFileCountOnFolder($dirPath, $recursive = false)
	{
		if (!File::isDirectory($dirPath)) {
			// throw new DirectoryNotFoundException($dirPath);
			return 0;
		}

		$files = $recursive ? File::allFiles($dirPath) : File::files($dirPath);

		return count($files);
	}

	/**
	 * Setup the test environment.
	 */
	protected function setUp(): void
	{
		parent::setUp();

		Dir::cleanDirectoryByExtension($this->getMigrationsDestinationDir(), 'php');
		Dir::cleanDirectoryByExtension($this->getSeedersDestinationDir(), 'php');
		Dir::cleanDirectoryByExtension($this->pathfinder->dbAutoSeedersDir(), 'php');
		Dir::cleanDirectoryByExtension(app_path('Entities/Dummies'), 'php');

		$this->withoutMockingConsoleOutput();

		$this->restoreRoutesFile();
	}

	/**
	 * Setup the test environment.
	 */
	protected function tearDown(): void
	{
		Dir::cleanDirectoryByExtension($this->getMigrationsDestinationDir(), 'php');
		Dir::cleanDirectoryByExtension($this->getSeedersDestinationDir(), 'php');
		Dir::cleanDirectoryByExtension($this->pathfinder->dbAutoSeedersDir(), 'php');
		Dir::cleanDirectoryByExtension(app_path('Entities/Dummies'), 'php');

		$this->restoreRoutesFile();

		parent::tearDown();
	}


	protected function restoreRoutesFile()
	{
		// restore file from backup
		File::ensureDirectoryExists(base_path('routes'));
		File::copy(__DIR__.'/../laravel/routes/web.php', base_path('routes/web.php'));
	}

	/**
	 *
	 * Test migration files are copied
	 *
	 */
	public function testPackageCopiesMigrations(): void
	{
		$src = Path::canonical(__DIR__ . '/../TestPackage/database/migrations');

		$dest = $this->pathfinder->dbMigrationsDir();

		$fileCount = $this->getFileCountOnFolder($src);

		$before = $this->getFileCountOnFolder($dest);

		$this->artisan('setup:extension:test-extension');

		$after = $this->getFileCountOnFolder($dest);

		$this->assertEquals(
			($before + $fileCount),
			$after,
			"{$fileCount} Migration file(s) not copied from `$src` to `$dest`."
		);
	}


	/**
	 *
	 * Test if seeders are copied
	 *
	 */
	public function testPackageCopiesSeeders(): void
	{
		$fileCount = $this->getFileCountOnFolder(__DIR__ . '/../TestPackage/publish/database/seeders', true);
		$before = $this->getFileCountOnFolder(database_path('seeders'), true);

		$this->artisan('setup:extension:test-extension');

		$after = $this->getFileCountOnFolder(database_path('seeders'), true);

		$this->assertEquals(($before + $fileCount), $after, 'Seeders not copied to destination.');
	}

	/**
	 *
	 * Test if the extension auto-publishes marked tags
	 *
	 */
	public function testPackageAutoPublishesFiles(): void
	{
		$fileCount = $this->getFileCountOnFolder(__DIR__ . '/../TestPackage/publish/app/Entities/Dummies');
		$before = $this->getFileCountOnFolder(app_path('Entities/Dummies'));

		$this->artisan('setup:extension:test-extension');

		$after = $this->getFileCountOnFolder(app_path('Entities/Dummies'));

		$this->assertEquals($after, ($before + $fileCount), 'Files not published to destination.');
	}

	public function testExtensionUpdatesRouteFiles(): void
	{
		// if there's an existing file back it up
		$originalFileContents = null;
		$parentFilePath = base_path('routes/web.php');
		File::ensureDirectoryExists(base_path('routes'));

		if (file_exists($parentFilePath)) {
			$originalFileContents = file_get_contents($parentFilePath);
			File::copy($parentFilePath, base_path('routes/web.php.bak'));
		}

		if (empty($originalFileContents)) {
			$originalFileContents = file_get_contents(__DIR__.'/../laravel/routes/web.php');
		}

		File::put($parentFilePath, $originalFileContents);

		// before updates, routes file should not contain the marker text
		$lineMarker = '// Start TestExtension API Routes';
		$this->assertFalse(strpos($originalFileContents, $lineMarker), 'Line marker should not be on the routes file');

		// run the extension
		$this->artisan('setup:extension:test-extension');

		// check the contents again
		$newContents = file_get_contents($parentFilePath);
		$this->assertIsInt(strpos($newContents, $lineMarker));
	}
}
