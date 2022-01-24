<?php


namespace ElegantMedia\OxygenFoundation\Console\Commands\Developer;

use ElegantMedia\PHPToolkit\Exceptions\FileSystem\FileNotFoundException;
use ElegantMedia\PHPToolkit\FileEditor;
use Illuminate\Support\Facades\File;

class MovePublicFolderCommand extends \Illuminate\Console\Command
{

	protected $signature = 'oxygen:foundation:move-public
								{destination : Destination folder}';

	protected $description = 'Move public folder to another folder';

	public function handle()
	{
		$destination = $this->argument('destination');

		$publicPath = public_path();
		$destinationPath = base_path($destination);

		// see if new path is the same as current one
		if ($publicPath === $destinationPath) {
			$this->info("Destination is the same as the current public_path. Skipping...");
			return 0;
		}

		// if it already exists, skip it
		if (file_exists($destinationPath)) {
			$this->info("`$destinationPath` already exists.");
		} else {
			// rename the existing folder
			File::moveDirectory($publicPath, $destinationPath);
		}

		// create new Application class
		$stubPath = __DIR__.'/../stubs/Application.stub';
		if (!file_exists($stubPath)) {
			throw new FileNotFoundException("`$stubPath` not found.");
		}

		$applicationFilePath = app_path('Application.php');
		if (!file_exists($applicationFilePath)) {
			File::copy($stubPath, $applicationFilePath);
		}

		// /app/Application.php
		FileEditor::findAndReplace($applicationFilePath, "'public'", "'$destination'");

		// /bootstrap/app.php
		FileEditor::findAndReplace(
			base_path('bootstrap/app.php'),
			'new Illuminate\Foundation\Application',
			"new App\Application"
		);

		// webpack.mix.js
		FileEditor::findAndReplace(base_path('webpack.mix.js'), "'public/", "'$destination/");

		// .gitignore
		FileEditor::findAndReplace(base_path('.gitignore'), "/public/", "/$destination/");

		return 1;
	}
}
