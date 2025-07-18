<?php

declare(strict_types=1);

namespace Database\Seeders\OxygenExtensions\AutoSeed;

use App\Entities\Examples\Example;

class ExamplesSeeder extends \Illuminate\Database\Seeder
{
	/**
	 * Run the database seeders.
	 */
	public function run()
	{
		$d = new Example([
			'name' => 'TestExample_001',
		]);
		$d->save();
	}
}
