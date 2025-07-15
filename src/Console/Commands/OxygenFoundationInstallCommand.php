<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Console\Commands;

class OxygenFoundationInstallCommand extends \Illuminate\Console\Command
{
    protected $signature = 'oxygen:foundation:install';

    protected $description = 'Run Oxygen Foundation Installer';

    public function handle()
    {
        $this->call('vendor:publish', [
            '--tag' => 'oxygen-foundation-install',
        ]);
    }
}
