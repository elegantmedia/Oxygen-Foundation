<?php

namespace ElegantMedia\OxygenFoundation\TestPackage;

use ElegantMedia\OxygenFoundation\Extensions\ExtensionServiceProviderInterface;
use ElegantMedia\OxygenFoundation\TestPackage\Console\Commands\TestExtensionSetupCommand;
use Illuminate\Support\ServiceProvider;

class TestPackageServiceProvider extends ServiceProvider
{

	public function boot(): void
	{
		// auto-publishing files
		$this->publishes([
			__DIR__ . '/../publish/app/Entities' => app_path('Entities'),
		], 'oxygen::auto-publish');

		// views
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'oxygen::test-extension');
	}

	public function register()
	{
		$this->commands([
			TestExtensionSetupCommand::class,
		]);
	}
}
