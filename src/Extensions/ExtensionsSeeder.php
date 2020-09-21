<?php
namespace ElegantMedia\OxygenFoundation\Extensions;

use ElegantMedia\OxygenFoundation\Core\Pathfinder;
use ElegantMedia\OxygenFoundation\Support\Filing;
use ElegantMedia\PHPToolkit\Exceptions\FileSystem\FileNotFoundException;
use ElegantMedia\PHPToolkit\FileEditor;
use Illuminate\Support\Facades\File;

class ExtensionsSeeder extends \Illuminate\Database\Seeder
{

	/**
	 * @param Pathfinder $pathfinder
	 * @throws FileNotFoundException
	 */
	public function run(Pathfinder $pathfinder)
	{
		$autoSeedPath = $pathfinder->dbAutoSeedersDir();

		if (!File::isDirectory($autoSeedPath)) {
			return;
		}

		$files = Filing::allFileNames($autoSeedPath);

		foreach ($files as $file) {
			$class = FileEditor::getPHPClassName($file);
			$this->call($class);
		}
	}
}
