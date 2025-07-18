<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Console\Commands;

use ElegantMedia\OxygenFoundation\Console\Commands\Traits\CopiesProjectStubFiles;
use ElegantMedia\OxygenFoundation\Support\Exceptions\ClassAlreadyExistsException;
use ElegantMedia\OxygenFoundation\Support\Exceptions\FileInvalidException;
use ElegantMedia\OxygenFoundation\Support\Filing;
use ElegantMedia\PHPToolkit\Exceptions\FileSystem\FileNotFoundException;
use ElegantMedia\PHPToolkit\FileEditor;
use ElegantMedia\PHPToolkit\Loader;
use ElegantMedia\PHPToolkit\Reflector;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\File;
use ReflectionException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Process\Process;

abstract class ExtensionInstallCommand extends Command implements ExtensionSetupInterface
{
	use CopiesProjectStubFiles;

	protected $composerRequire = [];

	protected $composerRequireDev = [];

	protected $composerDontDiscover = [];

	protected $requiredServiceProviders = [];

	protected $requiredNpmPackages = [];

	protected $requiredNpmDevPackages = [];

	/**
	 * @throws ClassAlreadyExistsException
	 * @throws FileNotFoundException
	 * @throws ReflectionException
	 * @throws FileInvalidException
	 * @throws \JsonException
	 */
	public function handle(): void
	{
		$this->installRequiredDependencies();

		// before setup
		if (method_exists($this, 'beforeSetup')) {
			$this->beforeSetup();
		}

		// publish auto-publish tags
		$this->autoPublishTags();

		if (method_exists($this, 'publishPackageFiles')) {
			$this->publishPackageFiles();
		}

		if (method_exists($this, 'afterPublishing')) {
			$this->afterPublishing();
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

		// update routes from stubs
		$this->updateRoutesFromStubs();

		// composer auto-load
		// $this->composerAutoload();

		// after setup
		if (method_exists($this, 'afterSetup')) {
			$this->afterSetup();
		}
	}

	protected function autoPublishTags($force = true): void
	{
		foreach ($this->getAutoPublishTags() as $tag) {
			$this->call('vendor:publish', [
				'--provider' => $this->getExtensionServiceProvider(),
				'--tag' => $tag,
				'--force' => $force,
			]);
		}
	}

	protected function getAutoPublishTags(): array
	{
		return [
			'oxygen::auto-publish',
		];
	}

	/**
	 * @throws FileNotFoundException
	 * @throws \JsonException
	 */
	protected function installRequiredDependencies(): bool
	{
		if (! $this->hasOption('install_dependencies')) {
			return false;
		}

		if (! filter_var($this->option('install_dependencies'), FILTER_VALIDATE_BOOLEAN)) {
			return false;
		}

		$this->addDontDiscoverPackages();
		$this->installComposerRequireDependencies();
		$this->installComposerRequireDevDependencies();

		$this->appendServiceProviders();

		$this->updateNpmPackages();
		$this->updateNpmDevPackages();

		return true;
	}

	/**
	 * @throws FileNotFoundException
	 * @throws \JsonException
	 */
	protected function addDontDiscoverPackages(): void
	{
		if (count($this->composerDontDiscover) === 0) {
			return;
		}

		$composerPath = base_path('composer.json');

		if (! file_exists($composerPath)) {
			throw new FileNotFoundException("$composerPath not found");
		}

		$composer = json_decode(file_get_contents($composerPath), true);

		if (! isset($composer['extra'])) {
			$composer['extra'] = [];
		}
		if (! isset($composer['extra']['laravel'])) {
			$composer['extra']['laravel'] = [];
		}
		if (! isset($composer['extra']['laravel']['dont-discover'])) {
			$composer['extra']['laravel']['dont-discover'] = [];
		}

		$composer['extra']['laravel']['dont-discover'] = array_unique(array_merge(
			$composer['extra']['laravel']['dont-discover'],
			$this->composerDontDiscover
		));

		file_put_contents(
			$composerPath,
			json_encode($composer, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
		);
	}

	/**
	 * Install Required Pacakges.
	 */
	protected function installComposerRequireDevDependencies(): void
	{
		$this->installComposerDependencies($this->composerRequire);
	}

	/**
	 * Install Required Dev Packages.
	 */
	protected function installComposerRequireDependencies(): void
	{
		$this->installComposerDependencies($this->composerRequireDev, true);
	}

	/**
	 * Install a list of composer dependencies.
	 *
	 * @param false $dev
	 */
	protected function installComposerDependencies($packages, bool $dev = false): void
	{
		if (! count($packages)) {
			return;
		}

		$this->info('Installing ' . implode(' ', $packages) . '...');

		$command = $this->composerRequireDev;
		array_unshift($command, 'composer', 'require');

		if ($dev) {
			$command[] = '--dev';
		}

		(new Process($command, base_path()))
			->setTimeout(null)
			->run(function ($type, $output) {
				$this->output->write($output);
			});
	}

	/**
	 * Update NPM Packages.
	 */
	protected function updateNpmPackages(): void
	{
		if (count($this->requiredNpmPackages) === 0) {
			return;
		}

		foreach ($this->requiredNpmPackages as $package => $version) {
			$this->addNpmPackage($package, $version, false);
		}
	}

	/**
	 * Update NPM Dev Packages.
	 */
	protected function updateNpmDevPackages(): void
	{
		if (count($this->requiredNpmDevPackages) === 0) {
			return;
		}

		foreach ($this->requiredNpmDevPackages as $package => $version) {
			$this->addNpmPackage($package, $version, true);
		}
	}

	/**
	 * Add an NPM Package.
	 *
	 * @param bool $dev
	 */
	protected function addNpmPackage($package, $version, $dev = true): void
	{
		if (! file_exists(base_path('package.json'))) {
			return;
		}

		$configurationKey = $dev ? 'devDependencies' : 'dependencies';

		$packages = json_decode(file_get_contents(base_path('package.json')), true);

		// overwrite the key, so if the version is changed, it will update it
		// TODO: maybe only allow upgrading or restricting versions?
		$packages[$configurationKey][$package] = $version;

		ksort($packages[$configurationKey]);

		file_put_contents(
			base_path('package.json'),
			json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
		);
	}

	/**
	 * Install the Fortify service providers in the application configuration file.
	 *
	 * @throws FileNotFoundException
	 */
	protected function appendServiceProviders(): void
	{
		if (count($this->requiredServiceProviders) === 0) {
			return;
		}

		$path = config_path('app.php');

		$shortlisted = [];
		foreach ($this->requiredServiceProviders as $serviceProvider) {
			if (! FileEditor::isTextInFile($path, $serviceProvider)) {
				$shortlisted[] = $serviceProvider . '::class';
			}
		}

		if (count($shortlisted) === 0) {
			return;
		}

		array_unshift($shortlisted, 'App\Providers\RouteServiceProvider::class');

		$append = implode(',' . PHP_EOL . "\t\t", $shortlisted) . ',';

		FileEditor::findAndReplace(
			$path,
			"App\\Providers\RouteServiceProvider::class,",
			$append
		);
	}

	/**
	 * Composer AutoLoad.
	 */
	protected function composerAutoload(): void
	{
		// reload classes, but not when testing
		if (! app()->runningUnitTests()) {
			$this->info('Updating composer classmap...');

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
		try {
			$filePaths = $this->getFilesFromPackage('stubs/routes');
		} catch (DirectoryNotFoundException $ex) {
			// Silent fail - directory not found is expected in some cases
			return;
		}

		if (empty($filePaths)) {
			return;
		}

		foreach ($filePaths as $sourcePath) {
			$basename = pathinfo($sourcePath, PATHINFO_BASENAME);

			$destinationPath = base_path("routes/{$basename}");

			// Process route stub

			// Ensure the routes directory exists
			$routesDir = dirname($destinationPath);
			if (! File::isDirectory($routesDir)) {
				File::makeDirectory($routesDir, 0755, true);
			}

			if (file_exists($destinationPath)) {
				// Read the stub content
				$stubContent = file_get_contents($sourcePath);

				// Get the first line as section marker
				$lines = explode("\n", $stubContent);
				$sectionStartString = null;
				foreach ($lines as $line) {
					$trimmedLine = trim($line);
					if (! empty($trimmedLine) && strpos($trimmedLine, '<?php') === false) {
						$sectionStartString = $trimmedLine;

						break;
					}
				}

				// Check if section already exists
				if ($sectionStartString && FileEditor::isTextInFile($destinationPath, $sectionStartString, false)) {
					// Skip confirmation prompt during tests
					if (app()->runningUnitTests()) {
						continue;
					}

					if (
						! $this->confirm(
							$this->getExtensionDisplayName() . " extension routes are already in `{$basename}`. Add again?",
							false
						)
					) {
						continue;
					}
				}

				// Append the content without stripping PHP tags
				// Remove opening PHP tag and declare statement from stub since destination already has them
				$stubContent = preg_replace('/^\s*<\?php\s*\n?/', '', $stubContent);
				$stubContent = preg_replace('/^\s*declare\s*\(\s*strict_types\s*=\s*1\s*\)\s*;\s*\n?/m', '', $stubContent);
				$stubContent = "\n" . trim($stubContent) . "\n";
				file_put_contents($destinationPath, $stubContent, FILE_APPEND);
			} else {
				// If the destination file doesn't exist, copy the stub file
				File::copy($sourcePath, $destinationPath);
			}
		}
	}

	/**
	 * @throws FileNotFoundException
	 * @throws ReflectionException
	 * @throws FileInvalidException
	 */
	public function generateExtensionMigrations(): void
	{
		// By convention, this file should be placed in `src/Console/Commands/`
		// Migrations will be placed in `database/database`
		// So the logic is to see if files are there in the database folder,
		// find the classes and copy them to the main project's database folder.

		// Before we do that, we also need to check if those migration classes are already loaded.
		// Laravel doesn't auto-load migrations. So we need to load them here,
		// so that we can see if there's a class conflict
		Loader::includeAllFilesFromDir(database_path('migrations'));

		try {
			$filePaths = $this->getFilesFromPackage('database/migrations');
		} catch (DirectoryNotFoundException $ex) {
			return;
		}

		foreach ($filePaths as $filePath) {
			try {
				$destinationPath = $this->copyMigrationFile($filePath);
			} catch (ClassAlreadyExistsException $e) {
				$this->error($e->getMessage() . '. Skipping...');
			}
		}
	}

	/**
	 * @throws FileNotFoundException
	 * @throws ReflectionException
	 * @throws ClassAlreadyExistsException
	 */
	public function generateExtensionSeeders(): void
	{
		try {
			$filePaths = $this->getFilesFromPackage('database/seeders', true);
		} catch (DirectoryNotFoundException $ex) {
			return;
		}

		foreach ($filePaths as $filePath) {
			$destinationPath = $this->copySeedFile($filePath);
		}
	}

	/**
	 * @param false $recursive
	 *
	 * @return string[]
	 *
	 * @throws ReflectionException
	 */
	protected function getFilesFromPackage($dirSuffix, $recursive = false)
	{
		$targetDir = Reflector::classPath($this, './../../' . $dirSuffix);

		// Look for package files

		if (! File::isDirectory($targetDir)) {
			throw new DirectoryNotFoundException("Directory `$targetDir` not found");
		}

		if ($recursive) {
			return Filing::allFileNames($targetDir);
		}

		return Filing::fileNames($targetDir);
	}
}
