<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Feature;

class FoundationInstallTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		$this->cleanup();
	}

	protected function tearDown(): void
	{
		$this->cleanup();

		parent::tearDown();
	}

	protected function cleanup()
	{
		if (file_exists(app_path('Entities/BaseRepository.php'))) {
			unlink(app_path('Entities/BaseRepository.php'));
		}
	}

	public function testOxygenInstallAddsBaseFiles()
	{
		$this->assertFileDoesNotExist(app_path('Entities/BaseRepository.php'));

		$this->artisan('oxygen:foundation:install');

		$this->assertFileExists(app_path('Entities/BaseRepository.php'));
	}
}
