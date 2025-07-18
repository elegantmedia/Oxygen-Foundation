<?php

declare(strict_types=1);

// Global functions with oxygen package

if (! function_exists('has_feature')) {
	/**
	 * Check if a given feature is enabled in the application
	 * To edit features, see `config/features.php`.
	 *
	 * @return Illuminate\Config\Repository|mixed
	 */
	function has_feature($featureSlug): bool
	{
		// if the string doesn't start with `features.`, append it
		if (strpos($featureSlug, 'features.') !== 0) {
			$featureSlug = 'features.' . $featureSlug;
		}

		return config($featureSlug, false);
	}
}

if (! function_exists('standard_datetime')) {
	/**
	 * Convert a date to EM's standard date time format.
	 *
	 * @param null $dateTime
	 *
	 * @return string|null
	 */
	function standard_datetime($dateTime = null)
	{
		if (empty($dateTime)) {
			return null;
		}

		if ($dateTime instanceof Carbon\Carbon) {
			return $dateTime->format(config('oxygen.date_time_format'));
		}
	}
}

if (! function_exists('standard_date')) {
	/**
	 * Convert a date to standard date format.
	 *
	 * @param null $dateTime
	 *
	 * @return string|null
	 */
	function standard_date($dateTime = null)
	{
		if (empty($dateTime)) {
			return null;
		}

		if ($dateTime instanceof Carbon\Carbon) {
			return $dateTime->format(config('oxygen.date_format'));
		}
	}
}

if (! function_exists('standard_time')) {
	/**
	 * Convert a date to EM's standard time format.
	 *
	 * @param null $dateTime
	 *
	 * @return string|null
	 */
	function standard_time($dateTime = null)
	{
		if (empty($dateTime)) {
			return null;
		}

		if ($dateTime instanceof Carbon\Carbon) {
			return $dateTime->format(config('oxygen.time_format'));
		}
	}
}

if (! function_exists('entity_resource_path')) {
	/**
	 * Guess the primary resource path from a given URL.
	 * turns /something/12/edit -> /something/12
	 * turns /something/create -> /something
	 * turns /something/new -> /something.
	 *
	 * @return string
	 */
	function entity_resource_path($url = '')
	{
		// if the URL is not given, get the current URL
		if ($url === '') {
			$url = request()->url();
		}

		$elements = explode('/', $url);
		$lastElement = end($elements);
		if (in_array($lastElement, ['edit', 'create', 'new'])) {
			array_pop($elements);

			return implode('/', $elements);
		}

		return $url;
	}
}
