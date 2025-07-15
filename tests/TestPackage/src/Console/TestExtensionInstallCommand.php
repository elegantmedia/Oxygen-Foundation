<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\TestPackage\Console;

use ElegantMedia\OxygenFoundation\Console\Commands\ExtensionInstallCommand;
use ElegantMedia\OxygenFoundation\TestPackage\TestPackageServiceProvider;

class TestExtensionInstallCommand extends ExtensionInstallCommand
{
    protected $signature = 'setup:extension:test-extension';

    protected $description = 'Oxygen Test Extension';

    public function getExtensionServiceProvider(): string
    {
        return TestPackageServiceProvider::class;
    }

    public function getExtensionDisplayName(): string
    {
        return 'TestExtension';
    }
}
