<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Console\Commands;

use ElegantMedia\OxygenFoundation\Extensions\ExtensionsSeeder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Seeder;

class SeedCommand extends \Illuminate\Database\Console\Seeds\SeedCommand
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'oxygen:seed';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Seed the database with records';

	/**
	 * Get a seeder instance from the container.
	 *
	 * @return Seeder
	 *
	 * @throws BindingResolutionException
	 */
	protected function getSeeder(): Seeder
	{
		$class = ExtensionsSeeder::class;

		return $this->laravel->make($class)
			->setContainer($this->laravel)
			->setCommand($this);
	}
}
