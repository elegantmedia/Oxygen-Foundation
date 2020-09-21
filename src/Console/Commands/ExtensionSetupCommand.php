<?php

namespace ElegantMedia\OxygenFoundation\Console\Commands;

use ElegantMedia\OxygenFoundation\Console\Commands\Traits\CopiesProjectStubFiles;
use ElegantMedia\OxygenFoundation\Support\Exceptions\ClassAlreadyExistsException;
use ElegantMedia\OxygenFoundation\Support\Exceptions\FileInvalidException;
use ElegantMedia\OxygenFoundation\Support\Filing;
use ElegantMedia\PHPToolkit\Exceptions\FileSystem\FileNotFoundException;
use ElegantMedia\PHPToolkit\Exceptions\FileSystem\SectionAlreadyExistsException;
use ElegantMedia\PHPToolkit\FileEditor;
use ElegantMedia\PHPToolkit\Reflector;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\File;
use ReflectionException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

abstract class ExtensionSetupCommand extends Command implements ExtensionSetupInterface
{

	use CopiesProjectStubFiles;

	/**
	 * @throws ClassAlreadyExistsException
	 * @throws FileNotFoundException
	 * @throws ReflectionException
	 * @throws FileInvalidException
	 */
	public function handle(): void
	{
		// before setup
		if (method_exists($this, 'beforeSetup')) {
			$this->beforeSetup();
		}

		// migrations
		if (method_exists($this, 'beforeGeneratingMigrations')) {
			$this->beforeGeneratingMigrations();
		}

		$this->generateExtensionMigrations();

		if (method_exists($this, 'afterGeneratingMigrations')) {
			$this->afterGeneratingMigrations();
		}

		// seeders
		if (method_exists($this, 'beforeGeneratingSeeds')) {
			$this->beforeGeneratingSeeds();
		}

		$this->generateExtensionSeeders();

		if (method_exists($this, 'afterGeneratingSeeds')) {
			$this->afterGeneratingSeeds();
		}

		// publish auto-publish tags
		$this->autoPublishTags();

		if (method_exists($this, 'publishPackageFiles')) {
			$this->publishPackageFiles();
		}

		// update routes from stubs
		$this->updateRoutesFromStubs();

		$this->composerAutoload();

		// after setup
		if (method_exists($this, 'afterSetup')) {
			$this->afterSetup();
		}
	}

	/**
	 *
	 */
	protected function composerAutoload(): void
	{
		// reload classes, but not when testing
		if (!app()->runningUnitTests()) {
			/** @var Composer $composer */
			$composer = app(Composer::class);
			$composer->dumpAutoloads();
		}
	}


	/**
	 * @throws FileNotFoundException
	 * @throws ReflectionException
	 */
	public function updateRoutesFromStubs(): void
	{
		$filePaths = $this->getFilesFromPackage('stubs/routes');

		foreach ($filePaths as $sourcePath) {
			$basename = pathinfo($sourcePath, PATHINFO_BASENAME);

			$destinationPath = base_path("routes/{$basename}");

			if (file_exists($destinationPath)) {
				try {
					$bytes = FileEditor::appendStubIfSectionNotFound($destinationPath, $sourcePath, null, null, true);
				} catch (SectionAlreadyExistsException $ex) {
					if (!$this->confirm(
						$this->getExtensionDisplayName() . " extension routes are already in `{$basename}`. Add again?",
						false
					)) {
						continue;
					}

					$bytes = FileEditor::appendStub($destinationPath, $sourcePath);
				}
			}
		}
	}

	/**
	 * @throws FileNotFoundException
	 * @throws ReflectionException
	 * @throws ClassAlreadyExistsException
	 * @throws FileInvalidException
	 */
	public function generateExtensionMigrations(): void
	{
		// By convention, this file should be placed in `src/Console/Commands/`
		// Migrations will be placed in `database/database`
		// So the logic is to see if files are there in the database folder,
		// find the classes and copy them to the main project's database folder.
		//
		// Before we do that, we also need to check if those migration classes are already loaded.

		$filePaths = $this->getFilesFromPackage('database/migrations');

		foreach ($filePaths as $filePath) {
			//try {
			$destinationPath = $this->copyMigrationFile($filePath);
//			} catch (ClassAlreadyExistsException $e) {
//				$this->error($e->getMessage());
//			}
		}
	}


	/**
	 * @throws FileNotFoundException
	 * @throws ReflectionException
	 * @throws ClassAlreadyExistsException
	 */
	public function generateExtensionSeeders(): void
	{
		$filePaths = $this->getFilesFromPackage('publish/database/seeders', true);

		foreach ($filePaths as $filePath) {
			$destinationPath = $this->copySeedFile($filePath);
		}
	}

	/**
	 * @param $dirSuffix
	 * @param false $recursive
	 * @return string[]
	 * @throws ReflectionException
	 */
	protected function getFilesFromPackage($dirSuffix, $recursive = false)
	{
		$targetDir = Reflector::classPath($this, './../../../' . $dirSuffix);

		if (!File::isDirectory($targetDir)) {
			throw new DirectoryNotFoundException("Directory `$targetDir` not found");
		}

		if ($recursive) {
			return Filing::allFileNames($targetDir);
		}

		return Filing::fileNames($targetDir);
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

	protected function getAutoPublishTags(): array
	{
		return [
			'oxygen::auto-publish',
		];
	}
}
