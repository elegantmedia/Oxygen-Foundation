<?php

declare(strict_types=1);

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
use ElegantMedia\OxygenFoundation\Scout\Engines\SecureKeywordEngine;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;

class OxygenFoundationServiceProvider extends ServiceProvider
{
    use RegisterResponseMacros;
    use RegisterSchemaMacros;

    protected array $configFiles = [
        'oxygen' => __DIR__ . '/../config/oxygen.php',
        'features' => __DIR__ . '/../config/features.php',
        'scout' => __DIR__ . '/../config/scout.php',
    ];

    public function register(): void
    {
        $this->app->singleton('oxygen', fn () => new OxygenCore);
        $this->app->singleton(Pathfinder::class);

        foreach ($this->configFiles as $key => $path) {
            $this->mergeConfigFrom($path, $key);
        }

        $this->registerResponseMacros();
        $this->registerSchemaMacros();
        $this->registerCommands();

        // Register Navigator Facade
        $this->app->singleton('elegantmedia.oxygen.navigator', fn () => new Navigator);
    }

    public function boot(): void
    {
        $this->registerPublishables();
        $this->bootScoutSearchEngines();
    }

    protected function registerPublishables(): void
    {
        if ($this->app->runningInConsole()) {
            // Publish config files
            $configPaths = [];
            foreach ($this->configFiles as $key => $path) {
                $configPaths[$path] = config_path($key . '.php');
            }
            $this->publishes($configPaths, 'oxygen-config');

            // Publish installation stubs
            $this->publishes([
                __DIR__ . '/../stubs/app' => app_path(),
            ], 'oxygen-foundation-install');
        }
    }

    protected function bootScoutSearchEngines(): void
    {
        $this->app[EngineManager::class]->extend('keyword', fn () => new SecureKeywordEngine);
    }

    protected function registerCommands(): void
    {
        $commands = [
            SeedCommand::class,
            OxygenFoundationInstallCommand::class,
            MovePublicFolderCommand::class,
        ];

        if (! $this->app->environment('production')) {
            $commands[] = RefreshDatabaseCommand::class;
        }

        $this->commands($commands);
    }
}
