<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Console\Commands\Developer;

class RefreshDatabaseCommand extends \Illuminate\Console\Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'db:refresh {--nomigrate} {--noseed} {--modules}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Wipe all tables, migrate and seed all data';

	/**
	 * Create a new command instance.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		$this->call('db:wipe');

		if ($this->option('nomigrate')) {
			return self::SUCCESS;
		}

		$this->call('migrate');

		if (! $this->option('noseed')) {
			$this->call('db:seed');
			$this->call('oxygen:seed');
		}

		return self::SUCCESS;
	}
}
