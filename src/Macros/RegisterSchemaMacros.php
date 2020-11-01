<?php

namespace ElegantMedia\OxygenFoundation\Macros;

use ElegantMedia\PHPToolkit\Arr;
use Illuminate\Database\Schema\Blueprint;

trait RegisterSchemaMacros
{

	protected function registerSchemaMacros(): void
	{
		Blueprint::macro('prefix', function ($prefix, $field) {
			return Arr::implodeIgnoreEmpty('_', [$prefix, $field]);
		});

		$this->registerLocationMacro();
		$this->registerPlaceMacro();
		$this->registerFileMacro();
	}

	/**
	 *
	 * Register `location` fields
	 *
	 */
	protected function registerLocationMacro(): void
	{
		// create location fields
		Blueprint::macro('location', function ($prefix = '') {
			/** @var Blueprint $this */
			$this->float($this->prefix($prefix, 'latitude', 10, 6))->nullable()->index();
			$this->float($this->prefix($prefix, 'longitude', 10, 6))->nullable()->index();
		});

		// drop location fields
		Blueprint::macro('dropLocation', function ($prefix = '') {
			$this->dropColumn($this->prefix($prefix, 'latitude'));
			$this->dropColumn($this->prefix($prefix, 'longitude'));
		});
	}

	/**
	 *
	 * Register `place` fields
	 *
	 */
	protected function registerPlaceMacro(): void
	{
		// create place fields
		Blueprint::macro('place', function ($prefix = '') {
			/** @var Blueprint $this */
			$this->string($this->prefix('venue'))->nullable();
			$this->string($this->prefix('address'))->nullable();
			$this->string($this->prefix('street'))->nullable();
			$this->string($this->prefix('street_2'))->nullable();
			$this->string($this->prefix('city'))->nullable();
			$this->string($this->prefix('state'))->nullable();
			$this->string($this->prefix('zip'))->nullable();
			$this->string($this->prefix('country'))->nullable();
			$this->location($prefix);
		});

		// drop place fields
		Blueprint::macro('dropPlace', function ($prefix = '') {
			$this->dropColumn($this->prefix('venue'));
			$this->dropColumn($this->prefix('address'));
			$this->dropColumn($this->prefix('street'));
			$this->dropColumn($this->prefix('street_2'));
			$this->dropColumn($this->prefix('city'));
			$this->dropColumn($this->prefix('state'));
			$this->dropColumn($this->prefix('zip'));
			$this->dropColumn($this->prefix('country'));
			$this->dropLocation($prefix);
		});
	}

	/**
	 *
	 * Register `file` fields
	 *
	 */
	protected function registerFileMacro(): void
	{
		Blueprint::macro('file', function ($prefix = '') {
			/** @var Blueprint $this */
			$this->string($this->prefix($prefix, 'uuid'))->unique()->nullable();
			$this->string($this->prefix($prefix, 'name'))->nullable();
			$this->boolean($this->prefix($prefix, 'allow_public_access'))->default(false);
			$this->string($this->prefix($prefix, 'original_filename'))->nullable();
			$this->string($this->prefix($prefix, 'file_path'))->nullable();
			$this->string($this->prefix($prefix, 'file_disk'))->nullable();
			$this->string($this->prefix($prefix, 'file_url'))->nullable();
			$this->bigInteger($this->prefix($prefix, 'file_size_bytes'))->unsigned()->nullable();
			$this->integer($this->prefix($prefix, 'uploaded_by_user_id'))->nullable()->references('id')->on('users');
		});

		Blueprint::macro('dropFile', function ($prefix = '') {
			/** @var Blueprint $this */
			$this->dropColumn($this->prefix($prefix, 'uuid'));
			$this->dropColumn($this->prefix($prefix, 'name'));
			$this->dropColumn($this->prefix($prefix, 'allow_public_access'));
			$this->dropColumn($this->prefix($prefix, 'original_filename'));
			$this->dropColumn($this->prefix($prefix, 'file_path'));
			$this->dropColumn($this->prefix($prefix, 'file_disk'));
			$this->dropColumn($this->prefix($prefix, 'file_url'));
			$this->dropColumn($this->prefix($prefix, 'file_size_bytes'));
			$this->dropColumn($this->prefix($prefix, 'uploaded_by_user_id'));
		});
	}
}
