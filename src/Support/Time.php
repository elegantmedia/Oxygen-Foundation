<?php


namespace ElegantMedia\OxygenFoundation\Support;

class Time
{

	/**
	 * Get the micro-timestamp of current time
	 *
	 * @example 2008_07_14_010813.98232
	 *
	 * @param string $format
	 * @return string
	 */
	public static function microTimestamp($format = 'Y_m_d_His'): string
	{
		// get microtime of file generation to preserve migration sequence
		$time = explode(" ", microtime());

		// change the format to 2008_07_14_010813.98232
		return date($format, $time[1]) . '.' . substr((string)$time[0], 2, 5);
	}
}
