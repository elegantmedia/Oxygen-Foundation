<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Feature;

use ElegantMedia\OxygenFoundation\Console\Commands\SeedCommand as OxygenSeedCommand;
use Illuminate\Database\Console\Seeds\SeedCommand as LaravelSeedCommand;
use Illuminate\Support\Facades\Artisan;

class SeedCommandTest extends TestCase
{
	public function testSeedCommandsAreRegisteredWithoutNameCollision(): void
	{
		$commands = Artisan::all();

		$this->assertArrayHasKey('db:seed', $commands);
		$this->assertArrayHasKey('oxygen:seed', $commands);
		$this->assertInstanceOf(LaravelSeedCommand::class, $commands['db:seed']);
		$this->assertNotInstanceOf(OxygenSeedCommand::class, $commands['db:seed']);
		$this->assertInstanceOf(OxygenSeedCommand::class, $commands['oxygen:seed']);
	}
}
