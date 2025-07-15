<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Console\Commands\Traits;

use ElegantMedia\OxygenFoundation\Core\Pathfinder;
use ElegantMedia\OxygenFoundation\Support\Exceptions\ClassAlreadyExistsException;
use ElegantMedia\OxygenFoundation\Support\Exceptions\FileInvalidException;
use ElegantMedia\PHPToolkit\Exceptions\FileSystem\FileNotFoundException;
use ElegantMedia\PHPToolkit\FileEditor;
use ElegantMedia\PHPToolkit\Timing;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

trait CopiesProjectStubFiles
{
    /**
     * @throws ClassAlreadyExistsException
     * @throws FileInvalidException
     * @throws FileNotFoundException
     */
    protected function copyMigrationFile($stubPath): string
    {
        // because Laravel 5.7 doesn't auto-load migration classes, manually load them
        // $migrationsPath = database_path('database');
        // Loader::includeAllFilesFromDir($migrationsPath);

        if (! file_exists($stubPath)) {
            throw new FileNotFoundException("Filing {$stubPath} not found.");
        }

        $className = FileEditor::getPHPClassName($stubPath);

        $basename = pathinfo($stubPath, PATHINFO_BASENAME);

        // We'll try to match a few incoming file name formats
        //
        // 0001_00_00_00_create_dummies_table.php
        // 0001_create_dummies_table.php
        // 0001_00_create_dummies_table.php
        // 0001_00_00_000_create_dummies_table.php
        // 0001_00_00_create_dummies_table.php
        //
        // From above examples, we have to caputure `create_dummies_table.php` using regex

        preg_match('/[\d]{1,4}_?(?:\d{1,4}_)+(.*)/', $basename, $matches);
        if (! is_countable($matches) || count($matches) < 1) {
            throw new FileInvalidException("Unable to parse migration filename `{$basename}` at `{$stubPath}`.");
        }
        $filename = Timing::microTimestamp() . '_' . $matches[1];

        $destinationDir = app(Pathfinder::class)->dbMigrationsDir();
        File::ensureDirectoryExists($destinationDir);
        $destinationPath = $destinationDir . DIRECTORY_SEPARATOR . $filename;

        $this->copyFile($stubPath, $destinationPath, $className);

        return $destinationPath;
    }

    /**
     * @throws ClassAlreadyExistsException
     * @throws FileNotFoundException
     */
    protected function copySeedFile($stubPath): string
    {
        if (! file_exists($stubPath)) {
            throw new FileNotFoundException("File {$stubPath} not found.");
        }

        // stub /tests/TestPack
        // path /vendor/
        // path /migrations/seeders/AutoSeed

        $className = FileEditor::getPHPClassName($stubPath);

        $filename = pathinfo($stubPath, PATHINFO_BASENAME);
        $destinationDir = database_path('seeders');
        File::ensureDirectoryExists($destinationDir);
        $destinationPath = $destinationDir . DIRECTORY_SEPARATOR . $filename;

        $result = $this->copyFile($stubPath, $destinationPath, $className);

        return $destinationPath;
    }

    /**
     * @param null $className
     *
     * @throws ClassAlreadyExistsException
     */
    protected function copyFile($source, $destination, $className = null): bool
    {
        if ($className && class_exists($className, false)) {
            // $this->warn("{$className} class already exists. Skipped...");
            // return false;
            throw new ClassAlreadyExistsException("{$className} class already exists");
        }

        if (! File::copy($source, $destination)) {
            throw new FileException("Unable to copy the file {$destination}");
        }

        return true;
    }
}
