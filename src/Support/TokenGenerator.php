<?php

namespace ElegantMedia\OxygenFoundation\Support;

use ElegantMedia\OxygenFoundation\Exceptions\TokenGenerationException;
use ElegantMedia\PHPToolkit\Text;
use Illuminate\Database\Eloquent\Model;

class TokenGenerator
{

	/**
	 *
	 * Create a unique token based on a DB column
	 *
	 * @param Model $model
	 * @param $dbColumn
	 * @param int $minChars
	 * @param int $maxChars
	 * @return string|null
	 * @throws TokenGenerationException
	 */
	public static function getCodeForModel(Model $model, $dbColumn, $minChars = 4, $maxChars = 8): ?string
	{
		$code = null;

		// safety checks
		if ($minChars <= 0) {
			$minChars = 1;
		}
		if ($maxChars <= $minChars) {
			$maxChars = $minChars + 1;
		}

		// check for duplicates
		$uniqueFound = false;

		for ($x = $minChars; $x < $maxChars; $x++) {
			// loop 100k times
			for ($i = 0, $iMax = 100000; $i < $iMax; $i++) {
				$code = self::generate($x);
				$entity = $model::where($dbColumn, $code)->first();
				if (!$entity) {
					$uniqueFound = true;
					break 2;
				}
			}
		}

		if (!$uniqueFound) {
			throw new TokenGenerationException(
				"Failed to generate a new code for the field `$dbColumn`. Limit of {$maxChars} exhausted"
			);
		}

		return $code;
	}

	/**
	 *
	 * Return a new token that doesn't have any ambiguous characters or offensive words
	 *
	 * @param int $characterLimit
	 * @return string
	 */
	public static function generate($characterLimit = 4): string
	{
		// don't let limit be negative
		if ($characterLimit <= 0) {
			$characterLimit = 1;
		}

		$foundCode = false;
		$code = null;

		// repeat forever until a non-offensive code is found
		do {
			$code = Text::randomUnambiguous($characterLimit);
			if (!self::isOffensive($code)) {
				$foundCode = true;
			}
		} while (!$foundCode);

		return $code;
	}

	/**
	 *
	 * Find if a given word is offensive
	 *
	 * @param $word
	 *
	 * @return bool
	 */
	public static function isOffensive($word): bool
	{
		$word = strtolower($word);

		$offensiveRegEx = implode('|', self::getOffensiveWords());

		if (preg_match("/({$offensiveRegEx})/i", $word)) {
			return true;
		}

		return false;
	}

	/**
	 *
	 * Get a list of known offensive word stems
	 *
	 * @return string[]
	 */
	protected static function getOffensiveWords(): array
	{
		return [
			'fuc', 'cunt', 'lick', 'sex', 'moth', 'ass', 'cum', 'suck',
			'hole', 'dick', 'cock', 'puss', 'bitch', 'boob', 'tit', 'whor', 'fcu', 'hair',
			'fat', 'black', 'nigg', 'vagi', 'frea', 'shlon', 'saus',
			'bang', 'shi', 'milf', 'gilf', 'fart', 'nut', 'blow', 'tit',
			'puk', 'pak', 'hut', 'kar', 'pon', 'wes', 'ves', 'bal', 'gon', 'pai',
		];
	}
}
