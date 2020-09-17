<?php

namespace ElegantMedia\OxygenFoundation\TestPackage;


use ElegantMedia\OxygenFoundation\TestPackage\Console\Commands\TestExtensionSetupCommand;

class TestPackageServiceProvider extends \Illuminate\Support\ServiceProvider
{

	public function boot()
	{
		// auto-publishing files
		$this->publishes([
			__DIR__.'/../publish/app/Entities' => app_path('Entities'),
		], 'oxygen::auto-publish');
	}

	public function register()
	{
		$this->commands(TestExtensionSetupCommand::class);
	}

}
