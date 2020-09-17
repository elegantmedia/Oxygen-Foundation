<?php
namespace ElegantMedia\OxygenFoundation\Console\Commands;

use ElegantMedia\OxygenFoundation\Console\Commands\Traits\CopiesProjectStubFiles;
use ElegantMedia\OxygenFoundation\Support\Dir;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

abstract class ExtensionSetupCommand extends Command implements ExtensionSetupInterface
{

	use CopiesProjectStubFiles;

	// TODO: update route files
	// TODO: update database seeder

	public function handle()
	{
//		$this->checkSetupVariables();

		$this->copyPackageMigrations();
		$this->copyPackageSeeds();
		$this->autoPublishTags();

		/*
		 |-----------------------------------------------------------
		 | // TODO: if there are extension seeders, add them
		 |-----------------------------------------------------------
		 */

		// add to an auto-seed order, with 00x as class name
		// allow in config to change loading priority

		echo (file_get_contents(base_path('routes/web.php')));

		if (method_exists($this, 'beforeGeneratingMigrations')) {
			$this->beforeGeneratingMigrations();
		}

		if (method_exists($this, 'generateMigrations')) {
			$this->generateMigrations();
		}

		if (method_exists($this, 'afterGeneratingMigrations')) {
			$this->afterGeneratingMigrations();
		}

		if (method_exists($this, 'beforeGeneratingSeeds')) {
			$this->beforeGeneratingSeeds();
		}

		if (method_exists($this, 'generateSeeds')) {
			$this->generateSeeds();
		}

		if (method_exists($this, 'afterGeneratingSeeds')) {
			$this->afterGeneratingSeeds();
		}

		if (method_exists($this, 'publishPackageFiles')) {
			$this->publishPackageFiles();
		}

		if (method_exists($this, 'updateRouteFiles')) {
			$this->updateRouteFiles();
		}

		// reload classes
		// $this->call('composer:dump-autoload');
	}

	public function getMigrationSourceFilePaths()
	{
		return $this->getFilesFromPackage('database/migrations');
	}

	public function getSeedSourceFilePaths()
	{
		return $this->getFilesFromPackage('database/seeders');
	}

	public function copyPackageMigrations()
	{
		// By convention, this file should be placed in `src/Console/Commands/`
		// Migrations will be placed in `database/migrations`
		// So the logic is to see if files are there in the migrations folder,
		// find the classes and copy them to the main project's migrations folder.
		//
		// Before we do that, we also need to check if those migration classes are already loaded.

		$filePaths = $this->getMigrationSourceFilePaths();

		foreach ($filePaths as $filePath) {
			$destinationPath = $this->copyMigrationFile($filePath);
		}
	}

	public function copyPackageSeeds()
	{
		$filePaths = $this->getSeedSourceFilePaths();

		foreach ($filePaths as $filePath) {
			$destinationPath = $this->copySeedFile($filePath);

			// TODO: add $destinationPath to seeder
		}
	}

	protected function getAutoPublishTags(): array
	{
		return [
			'oxygen::auto-publish',
		];
	}

	protected function autoPublishTags(): void
	{
		foreach ($this->getAutoPublishTags() as $tag) {
			$this->call('vendor:publish', [
				'--provider' => $this->getExtensionServiceProvider(),
				'--tag' => $tag,
			]);
		}
	}


	protected function getFilesFromPackage($dirSuffix)
	{
		$targetDir = \ElegantMedia\OxygenFoundation\Support\File::classPath($this, './../../../'.$dirSuffix);

		if (!File::isDirectory($targetDir)) {
			return false;
		}

		$files = File::files($targetDir);

		return array_map(function ($file) {
			/* @var SplFileInfo $file */
			return $file->getPathname();
		}, $files);
	}
}
