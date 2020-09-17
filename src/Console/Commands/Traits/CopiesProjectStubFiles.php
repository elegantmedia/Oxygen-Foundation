<?php
namespace ElegantMedia\OxygenFoundation\Console\Commands\Traits;


use ElegantMedia\OxygenFoundation\Support\Exceptions\FileInvalidException;
use ElegantMedia\OxygenFoundation\Support\Exceptions\FileNotFoundException;
use ElegantMedia\OxygenFoundation\Support\Time;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

trait CopiesProjectStubFiles
{


	protected function copyMigrationFile($stubPath)
	{
		// because Laravel 5.7 doesn't auto-load migration classes, manually load them
		// $migrationsPath = database_path('migrations');
		// Loader::includeAllFilesFromDir($migrationsPath);


		if (!file_exists($stubPath)) {
			throw new FileNotFoundException("File {$stubPath} not found.");
		}

		$className = \ElegantMedia\OxygenFoundation\Support\File::getClassName($stubPath);

		$basename = pathinfo($stubPath, PATHINFO_BASENAME);
		preg_match('/[\d]{1,4}_(.*)/', $basename, $matches);
		if (!is_countable($matches) || count($matches) < 1) {
			throw new FileInvalidException("Unable to parse migration filename `{$basename}` at `{$stubPath}`.");
		}
		$filename = Time::microTimestamp() . '_' . $matches[1];

		$destinationDir = database_path('migrations');
		File::ensureDirectoryExists($destinationDir);
		$destinationPath = $destinationDir.DIRECTORY_SEPARATOR.$filename;

		$this->copyFile($stubPath, $destinationPath, $className);

		return $destinationPath;
	}

	protected function copySeedFile($stubPath)
	{
		if (!file_exists($stubPath)) {
			throw new FileNotFoundException("File {$stubPath} not found.");
		}

		$className = \ElegantMedia\OxygenFoundation\Support\File::getClassName($stubPath);

		$filename = pathinfo($stubPath, PATHINFO_BASENAME);
		$destinationDir = database_path('seeders');
		File::ensureDirectoryExists($destinationDir);
		$destinationPath = $destinationDir.DIRECTORY_SEPARATOR.$filename;

		$result = $this->copyFile($stubPath, $destinationPath, $className);

		return $destinationPath;
	}

	protected function copyFile($source, $destination, $className = null): bool
	{
		if ($className && class_exists($className, false)) {
			$this->info("{$className} class already exists. Skipped...");
			return false;
		}

		if (!File::copy($source, $destination)) {
			throw new FileException("Unable to copy the file {$destination}");
		}
		return true;
	}


}
