<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Console\Commands\Developer;

use ElegantMedia\PHPToolkit\Exceptions\FileSystem\FileNotFoundException;
use ElegantMedia\PHPToolkit\FileEditor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'oxygen:foundation:move-public')]
class MovePublicFolderCommand extends Command
{
    protected $signature = 'oxygen:foundation:move-public
                            {destination : Destination folder}
                            {--dry-run : Show what would be changed without making actual changes}
                            {--force : Force the operation even if destination exists}';

    protected $description = 'Move public folder to another location and update configuration files';

    public function handle(): int
    {
        $destination = $this->argument('destination');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if (! $this->validateDestination($destination)) {
            return self::FAILURE;
        }

        $publicPath = public_path();
        $destinationPath = base_path($destination);

        // Check if paths are the same
        if ($publicPath === $destinationPath) {
            $this->info('Destination is the same as the current public_path. No changes needed.');

            return self::SUCCESS;
        }

        // Handle existing destination
        if (file_exists($destinationPath) && ! $force) {
            $this->error("Destination path '{$destinationPath}' already exists.");
            $this->info('Use --force option to overwrite or choose a different destination.');

            return self::FAILURE;
        }

        if ($dryRun) {
            $this->info('Dry run mode - no changes will be made.');
            $this->showPlannedChanges($publicPath, $destinationPath, $destination);

            return self::SUCCESS;
        }

        try {
            $this->movePublicFolder($publicPath, $destinationPath);
            $this->createApplicationClass();
            $this->updateConfigurationFiles($destination);

            $this->info("✓ Public folder successfully moved to '{$destination}'");
            $this->info('✓ Configuration files updated');
            $this->warn('Remember to update your web server configuration!');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to move public folder: ' . $e->getMessage());

            return self::FAILURE;
        }
    }

    protected function validateDestination(string $destination): bool
    {
        if (empty($destination) || $destination === '.' || $destination === '..') {
            $this->error('Invalid destination folder specified.');

            return false;
        }

        if (str_contains($destination, '..')) {
            $this->error('Destination cannot contain parent directory references (..)');

            return false;
        }

        return true;
    }

    protected function showPlannedChanges(string $publicPath, string $destinationPath, string $destination): void
    {
        $this->info('The following changes would be made:');
        $this->line("- Move folder: {$publicPath} → {$destinationPath}");
        $this->line('- Create: app/Application.php');
        $this->line('- Update: bootstrap/app.php');
        $this->line('- Update: vite.config.js (if exists)');
        $this->line('- Update: webpack.mix.js (if exists)');
        $this->line('- Update: .gitignore');
    }

    protected function movePublicFolder(string $publicPath, string $destinationPath): void
    {
        if (file_exists($destinationPath)) {
            File::deleteDirectory($destinationPath);
        }

        File::moveDirectory($publicPath, $destinationPath);
    }

    protected function createApplicationClass(): void
    {
        $stubPath = __DIR__ . '/../stubs/Application.stub';
        if (! file_exists($stubPath)) {
            throw new FileNotFoundException("Stub file not found: {$stubPath}");
        }

        $applicationFilePath = app_path('Application.php');
        if (! file_exists($applicationFilePath)) {
            File::copy($stubPath, $applicationFilePath);
        }
    }

    protected function updateConfigurationFiles(string $destination): void
    {
        $filesToUpdate = [
            ['file' => app_path('Application.php'), 'search' => "'public'", 'replace' => "'{$destination}'"],
            ['file' => base_path('bootstrap/app.php'), 'search' => 'new Illuminate\Foundation\Application', 'replace' => 'new App\Application'],
            ['file' => base_path('.gitignore'), 'search' => '/public/', 'replace' => "/{$destination}/"],
        ];

        // Vite config (Laravel 9+)
        if (file_exists(base_path('vite.config.js'))) {
            $filesToUpdate[] = ['file' => base_path('vite.config.js'), 'search' => '/public/', 'replace' => "/{$destination}/"];
        }

        // Webpack Mix (Legacy)
        if (file_exists(base_path('webpack.mix.js'))) {
            $filesToUpdate[] = ['file' => base_path('webpack.mix.js'), 'search' => "'public/", 'replace' => "'{$destination}/"];
        }

        foreach ($filesToUpdate as $update) {
            if (file_exists($update['file'])) {
                FileEditor::findAndReplace($update['file'], $update['search'], $update['replace']);
            }
        }
    }
}
