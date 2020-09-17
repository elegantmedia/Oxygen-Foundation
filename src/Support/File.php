<?php
namespace ElegantMedia\OxygenFoundation\Support;



use ElegantMedia\OxygenFoundation\Support\Exceptions\FileNotFoundException;

class File
{

	/**
	 *
	 * Get a Classname from a file. Only reads the file without parsing for syntax.
	 * Source:https://stackoverflow.com/questions/7153000/get-class-name-from-file
	 *
	 * @param $filePath
	 * @return string
	 * @throws FileNotFoundException
	 */
	public static function getClassName($filePath): string
	{
		if (!file_exists($filePath)) {
			throw new FileNotFoundException("File {$filePath} not found.");
		}

		$fp = fopen($filePath, 'r');
		$class = $namespace = $buffer = '';
		$i = 0;
		while (!$class) {
			if (feof($fp)) {
				break;
			}

			$buffer .= fread($fp, 512);
			$tokens = token_get_all($buffer);

			if (strpos($buffer, '{') === false) {
				continue;
			}

			for ($iMax = count($tokens); $i< $iMax; $i++) {
				if ($tokens[$i][0] === T_NAMESPACE) {
					for ($j=$i+1, $jMax = count($tokens); $j< $jMax; $j++) {
						if ($tokens[$j][0] === T_STRING) {
							$namespace .= '\\'.$tokens[$j][1];
						} elseif ($tokens[$j] === '{' || $tokens[$j] === ';') {
							break;
						}
					}
				}

				if ($tokens[$i][0] === T_CLASS) {
					for ($j=$i+1, $jMax = count($tokens); $j< $jMax; $j++) {
						if ($tokens[$j] === '{') {
							$class = $tokens[$i+2][1];
						}
					}
				}
			}
		}

		return "{$namespace}\\{$class}";
	}

	/**
	 *
	 * Delete files in a directory by it's file extensions
	 *
	 * @param $dir
	 * @param $extension
	 * @return false|int
	 */
	public static function cleanDirectoryByExtension($dir, $extension)
	{
		$dir = rtrim($dir, DIRECTORY_SEPARATOR);

		if (!is_dir($dir)) {
			return false;
		}

		if (empty($extension)) {
			return false;
		}

		return self::clearnDirectoryByWildcard($dir, "*.$extension");
	}

	/**
	 *
	 * Delete files in a directory by wildcard
	 *
	 * @param $dir
	 * @param $wildcard
	 * @return int
	 */
	public static function clearnDirectoryByWildcard($dir, $wildcard)
	{
		// get the files
		$files = glob($dir.DIRECTORY_SEPARATOR.$wildcard);

		$count = 0;
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
				$count++;
			}
		}

		return $count;
	}

	/**
	 *
	 * Get the inherited class' directory path
	 *
	 * @param $self
	 * @param null $append
	 * @return string
	 * @throws \ReflectionException
	 */
	public static function classPath($self, $append = null): string
	{
		$reflector = new \ReflectionClass(get_class($self));

		$path = dirname($reflector->getFileName());

		if ($append) {
			$path .= DIRECTORY_SEPARATOR.ltrim($append, DIRECTORY_SEPARATOR);
		}

		return $path;
	}

}
