<?php

namespace Database\Seeders\OxygenExtensions\AutoSeed;

use App\Entities\Examples\Example;

class ExamplesSeeder extends \Illuminate\Database\Seeder
{
	/**
	 * Run the database seeders.
	 *
	 * @return void
	 */
	public function run()
	{
		$d = new Example([
			'name' => 'TestExample_001',
		]);
		$d->save();
	}
}
