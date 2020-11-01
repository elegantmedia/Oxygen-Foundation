<?php

namespace ElegantMedia\OxygenFoundation;

use ElegantMedia\OxygenFoundation\Console\Commands\Developer\MovePublicFolderCommand;
use ElegantMedia\OxygenFoundation\Console\Commands\Developer\RefreshDatabaseCommand;
use ElegantMedia\OxygenFoundation\Console\Commands\OxygenFoundationInstallCommand;
use ElegantMedia\OxygenFoundation\Console\Commands\SeedCommand;
use ElegantMedia\OxygenFoundation\Core\OxygenCore;
use ElegantMedia\OxygenFoundation\Core\Pathfinder;
use ElegantMedia\OxygenFoundation\Macros\RegisterResponseMacros;
use ElegantMedia\OxygenFoundation\Macros\RegisterSchemaMacros;
use ElegantMedia\OxygenFoundation\Navigation\Navigator;
use ElegantMedia\OxygenFoundation\Scout\KeywordSearchEngine;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;

class OxygenFoundationServiceProvider extends ServiceProvider
{

	use RegisterSchemaMacros;
	use RegisterResponseMacros;

	public function register()
	{
		$this->app->singleton('oxygen', function () {
			return new OxygenCore();
		});

		$this->app->singleton(Pathfinder::class);

		$this->mergeConfigFrom(__DIR__ . '/../config/oxygen.php', 'oxygen');
		$this->mergeConfigFrom(__DIR__ . '/../config/features.php', 'features');
		$this->mergeConfigFrom(__DIR__ . '/../config/scout.php', 'scout');

		$this->registerResponseMacros();
		$this->registerSchemaMacros();
		$this->registerCommands();

		// Register Navigator Facade
		$this->app->singleton('elegantmedia.oxygen.navigator', function () {
			return new Navigator();
		});
	}

	public function boot()
	{
		// publish config
		$this->publishes([
			__DIR__.'/../config/oxygen.php' 	=> config_path('oxygen.php'),
			__DIR__.'/../config/features.php' 	=> config_path('features.php'),
			__DIR__.'/../config/scout.php' 		=> config_path('scout.php'),
		], 'oxygen-config');

		$this->publishes([
			__DIR__.'/../stubs/app' => app_path(),
		], 'oxygen-foundation-install');

		$this->bootScoutSearchEngines();
		// $this->app[EngineManager::class]->extend('keyword', function ($app) {
		// 	return new KeywordSearchEngine();
		// });
	}

	protected function bootScoutSearchEngines(): void
	{
		$this->app[EngineManager::class]->extend('keyword', function ($app) {
		// resolve(EngineManager::class)->extend('keyword', function () {
			return new KeywordSearchEngine();
		});
	}


	protected function registerCommands(): void
	{
		$this->commands([
			SeedCommand::class,
			OxygenFoundationInstallCommand::class,
			MovePublicFolderCommand::class,
		]);

		if (!app()->environment('production')) {
			$this->commands([
				RefreshDatabaseCommand::class,
			]);
		}
	}
}
