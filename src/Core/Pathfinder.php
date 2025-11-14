<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Core;

class Pathfinder
{
	public function dbAutoSeedersDir(): string
	{
		return database_path('seeders/OxygenExtensions/AutoSeed');
	}

	public function dbMigrationsDir(): string
	{
		return database_path('migrations');
	}

	public function dbSeedersDir(): string
	{
		return database_path('seeders');
	}
}
