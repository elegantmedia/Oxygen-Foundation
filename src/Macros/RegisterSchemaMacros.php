<?php

declare(strict_types=1);

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
	 * Register `location` fields.
	 */
	protected function registerLocationMacro(): void
	{
		// create location fields
		Blueprint::macro('location', function ($prefix = '') {
			/* @var Blueprint $this */
			$this->float($this->prefix($prefix, 'latitude'), 10, 6)->nullable()->index();
			$this->float($this->prefix($prefix, 'longitude'), 10, 6)->nullable()->index();
		});

		// drop location fields
		Blueprint::macro('dropLocation', function ($prefix = '') {
			$this->dropColumn($this->prefix($prefix, 'latitude'));
			$this->dropColumn($this->prefix($prefix, 'longitude'));
		});
	}

	/**
	 * Register `place` fields.
	 */
	protected function registerPlaceMacro(): void
	{
		// create place fields
		Blueprint::macro('place', function ($prefix = '') {
			/* @var Blueprint $this */
			$this->string($this->prefix($prefix, 'venue'))->nullable();
			$this->string($this->prefix($prefix, 'address'))->nullable();
			$this->string($this->prefix($prefix, 'formatted_address'))->nullable();
			$this->string($this->prefix($prefix, 'street'))->nullable();
			$this->string($this->prefix($prefix, 'street_2'))->nullable();
			$this->string($this->prefix($prefix, 'city'))->nullable();
			$this->string($this->prefix($prefix, 'state'))->nullable();
			$this->string($this->prefix($prefix, 'state_iso_code'))->nullable();
			$this->string($this->prefix($prefix, 'zip'))->nullable();
			$this->string($this->prefix($prefix, 'country'))->nullable();
			$this->string($this->prefix($prefix, 'country_iso_code'))->nullable();
			$this->location($prefix);
		});

		// drop place fields
		Blueprint::macro('dropPlace', function ($prefix = '') {
			$this->dropColumn($this->prefix($prefix, 'venue'));
			$this->dropColumn($this->prefix($prefix, 'address'));
			$this->dropColumn($this->prefix($prefix, 'formatted_address'));
			$this->dropColumn($this->prefix($prefix, 'street'));
			$this->dropColumn($this->prefix($prefix, 'street_2'));
			$this->dropColumn($this->prefix($prefix, 'city'));
			$this->dropColumn($this->prefix($prefix, 'state'));
			$this->dropColumn($this->prefix($prefix, 'state_iso_code'));
			$this->dropColumn($this->prefix($prefix, 'zip'));
			$this->dropColumn($this->prefix($prefix, 'country'));
			$this->dropColumn($this->prefix($prefix, 'country_iso_code'));
			$this->dropLocation($prefix);
		});
	}

	/**
	 * Register `file` fields.
	 */
	protected function registerFileMacro(): void
	{
		Blueprint::macro('file', function ($prefix = '') {
			/* @var Blueprint $this */
			$this->string($this->prefix($prefix, 'uuid'))->unique()->nullable();
			$this->string($this->prefix($prefix, 'name'))->nullable();
			$this->boolean($this->prefix($prefix, 'allow_public_access'))->default(false);
			$this->string($this->prefix($prefix, 'original_filename'))->nullable();
			$this->string($this->prefix($prefix, 'file_path'))->nullable();
			$this->string($this->prefix($prefix, 'file_disk'))->nullable();
			$this->string($this->prefix($prefix, 'file_url'))->nullable();
			$this->bigInteger($this->prefix($prefix, 'file_size_bytes'))->unsigned()->nullable();
			// Modern foreign key syntax with proper index and constraint
			$this->foreignId($this->prefix($prefix, 'uploaded_by_user_id'))
				->nullable()
				->constrained('users')
				->nullOnDelete();
		});

		Blueprint::macro('dropFile', function ($prefix = '') {
			/* @var Blueprint $this */
			// Drop unique index on uuid before dropping the column for better SQLite compatibility
			try {
				$this->dropUnique([$this->prefix($prefix, 'uuid')]);
			} catch (\Throwable $e) {
				// Ignore if index doesn't exist or driver can't drop it explicitly; Laravel may rebuild the table
			}
			// Drop FK before dropping the FK column
			$fkColumn = $this->prefix($prefix, 'uploaded_by_user_id');

			try {
				if (method_exists($this, 'dropConstrainedForeignId')) {
					$this->dropConstrainedForeignId($fkColumn);
				} else {
					$this->dropForeign([$fkColumn]);
					$this->dropColumn($fkColumn);
				}
			} catch (\Throwable $e) {
				// Fallback: attempt to drop the column directly
				try {
					$this->dropColumn($fkColumn);
				} catch (\Throwable $ignored) {
				}
			}
			$this->dropColumn($this->prefix($prefix, 'uuid'));
			$this->dropColumn($this->prefix($prefix, 'name'));
			$this->dropColumn($this->prefix($prefix, 'allow_public_access'));
			$this->dropColumn($this->prefix($prefix, 'original_filename'));
			$this->dropColumn($this->prefix($prefix, 'file_path'));
			$this->dropColumn($this->prefix($prefix, 'file_disk'));
			$this->dropColumn($this->prefix($prefix, 'file_url'));
			$this->dropColumn($this->prefix($prefix, 'file_size_bytes'));
		});
	}
}
