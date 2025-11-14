<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Integration\Database\Eloquent\Traits;

use ElegantMedia\OxygenFoundation\Database\Eloquent\Traits\CreatesUniqueTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;

class TestLegacyTokenModel extends Model
{
	use CreatesUniqueTokens;

	protected $fillable = ['name', 'token'];

	protected $table = 'test_legacy_token_models';
}

class CreatesUniqueTokensIntegrationTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp(): void
	{
		parent::setUp();

		Schema::create('test_legacy_token_models', function (Blueprint $table) {
			$table->id();
			$table->string('name')->nullable();
			$table->string('token')->unique();
			$table->timestamps();
		});
	}

	protected function tearDown(): void
	{
		Schema::dropIfExists('test_legacy_token_models');
		parent::tearDown();
	}

	public function testNewUniqueTokenCreatesValidToken(): void
	{
		$token = TestLegacyTokenModel::newUniqueToken('token', 32);

		$this->assertIsString($token);
		$this->assertEquals(32, strlen($token));

		TestLegacyTokenModel::create(['token' => $token]);

		$token2 = TestLegacyTokenModel::newUniqueToken('token', 32);
		$this->assertNotEquals($token, $token2);
	}

	public function testNewTimestampedTokenContainsTimestampAndIsHighResolution(): void
	{
		$token1 = TestLegacyTokenModel::newTimestampedToken('token', 16);
		$token2 = TestLegacyTokenModel::newTimestampedToken('token', 16);

		$this->assertIsString($token1);
		$this->assertIsString($token2);

		$this->assertStringContainsString('_', $token1);
		$this->assertStringContainsString('_', $token2);

		[$prefix1] = explode('_', $token1, 2);
		[$prefix2] = explode('_', $token2, 2);

		$this->assertMatchesRegularExpression('/^[a-z0-9]+$/', $prefix1);
		$this->assertMatchesRegularExpression('/^[a-z0-9]+$/', $prefix2);

		// With hrtime-based prefix, two consecutive tokens should very likely differ
		$this->assertNotSame($prefix1, $prefix2);
	}
}
