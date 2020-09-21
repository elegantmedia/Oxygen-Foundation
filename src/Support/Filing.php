<?php
namespace ElegantMedia\OxygenFoundation\Support;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class Filing
{

	/*
	|--------------------------------------------------------------------------
	| Illuminate/Support Functions
	|--------------------------------------------------------------------------
	|
	|
	|
	*/

	public static function allFileNames($directory)
	{
		$files = File::allFiles($directory);

		return array_map(function ($file) {
			/* @var SplFileInfo $file */
			return $file->getPathname();
		}, $files);
	}

	public static function fileNames($directory)
	{
		$files = File::files($directory);

		return array_map(function ($file) {
			/* @var SplFileInfo $file */
			return $file->getPathname();
		}, $files);
	}
}
