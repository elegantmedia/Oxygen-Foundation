<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Integration\Database\Eloquent\Traits;

use ElegantMedia\OxygenFoundation\Database\Eloquent\Traits\HasSecureToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;

class TestSecureTokenModel extends Model
{
	use HasSecureToken;

	protected $fillable = ['name', 'token'];

	protected $table = 'test_secure_token_models';
}

class HasSecureTokenIntegrationTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp(): void
	{
		parent::setUp();

		// Create test table
		Schema::create('test_secure_token_models', function (Blueprint $table) {
			$table->id();
			$table->string('name')->nullable();
			$table->string('token')->unique();
			$table->timestamps();
		});
	}

	protected function tearDown(): void
	{
		Schema::dropIfExists('test_secure_token_models');
		parent::tearDown();
	}

	public function testGenerateUniqueTokenCreatesValidToken(): void
	{
		$token = TestSecureTokenModel::generateUniqueToken('token', 32);

		$this->assertIsString($token);
		$this->assertEquals(32, strlen($token));

		// Test uniqueness by creating a model with the token
		$model = TestSecureTokenModel::create(['token' => $token]);

		// Try to generate another token - it should be different
		$token2 = TestSecureTokenModel::generateUniqueToken('token', 32);
		$this->assertNotEquals($token, $token2);
	}

	public function testGenerateUniqueTokenWithCustomLength(): void
	{
		$token = TestSecureTokenModel::generateUniqueToken('token', 16);

		$this->assertIsString($token);
		$this->assertEquals(16, strlen($token));
	}

	public function testGenerateTimestampedTokenContainsTimestamp(): void
	{
		$token = TestSecureTokenModel::generateTimestampedToken('token', 24);

		$this->assertIsString($token);
		$this->assertStringContainsString('_', $token);

		// Check that it has timestamp prefix and random suffix
		$parts = explode('_', $token);
		$this->assertCount(2, $parts);
		$this->assertEquals(24, strlen($parts[1]));

		// Verify the timestamp part is base36 encoded
		$this->assertMatchesRegularExpression('/^[a-z0-9]+$/', $parts[0]);
	}

	public function testGenerateUrlSafeTokenIsUrlSafe(): void
	{
		$token = TestSecureTokenModel::generateUrlSafeToken('token', 32);

		$this->assertIsString($token);
		// Check that it contains only URL-safe characters
		$this->assertMatchesRegularExpression('/^[A-Za-z0-9\-_]+$/', $token);
		// Should not contain URL-unsafe characters
		$this->assertStringNotContainsString('+', $token);
		$this->assertStringNotContainsString('/', $token);
		$this->assertStringNotContainsString('=', $token);
	}
}
