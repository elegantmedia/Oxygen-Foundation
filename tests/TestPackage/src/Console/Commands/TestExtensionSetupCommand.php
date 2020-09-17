<?php
namespace ElegantMedia\OxygenFoundation\TestPackage\Console\Commands;


use ElegantMedia\OxygenFoundation\Console\Commands\ExtensionSetupCommand;
use ElegantMedia\OxygenFoundation\TestPackage\TestPackageServiceProvider;

class TestExtensionSetupCommand extends ExtensionSetupCommand
{

	protected $signature 	= 'setup:extension:test-extension';
	protected $description 	= 'Oxygen Test Extension';

	public function getExtensionServiceProvider(): string
	{
		return TestPackageServiceProvider::class;
	}

	public function getExtensionName(): string
	{
		return 'TestExtension';
	}
}
