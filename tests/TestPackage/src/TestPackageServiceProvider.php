<?php

namespace ElegantMedia\OxygenFoundation\TestPackage;

use ElegantMedia\OxygenFoundation\Extensions\ExtensionServiceProviderInterface;
use ElegantMedia\OxygenFoundation\TestPackage\Console\TestExtensionInstallCommand;
use Illuminate\Support\ServiceProvider;

class TestPackageServiceProvider extends ServiceProvider
{

	public function boot(): void
	{
		// auto-publishing files
		$this->publishes([
			__DIR__ . '/../publish/app/Entities' => app_path('Entities'),
			__DIR__ . '/../publish/database/seeders' => database_path('seeders'),
		], 'oxygen::auto-publish');

		// views
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'oxygen::test-extension');
	}

	public function register()
	{
		$this->commands([
			TestExtensionInstallCommand::class,
		]);
	}
}
