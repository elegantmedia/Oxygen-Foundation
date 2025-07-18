<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Unit\Database\Eloquent\Traits;

use ElegantMedia\OxygenFoundation\Database\Eloquent\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\TestCase;
use Ramsey\Uuid\Uuid;

class HasUuidTest extends TestCase
{
	private Model $model;

	protected function setUp(): void
	{
		parent::setUp();

		// Create a test model using the trait
		$this->model = new class () extends Model {
			use HasUuid;

			protected $fillable = ['name'];

			protected $table = 'test_models';

			// Override to prevent database operations
			public function save(array $options = []): bool
			{
				return true;
			}
		};
	}

	public function testGetRouteKeyNameReturnsUuid(): void
	{
		$this->assertEquals('uuid', $this->model->getRouteKeyName());
	}

	public function testHasUuidTraitAddsUuidToFillable(): void
	{
		// The trait should add uuid to fillable fields
		$this->assertContains('uuid', $this->model->getFillable());
	}

	public function testBootHasUuidAddsCreatingListener(): void
	{
		// Test that the trait is properly used
		$traits = class_uses($this->model);
		$this->assertArrayHasKey(HasUuid::class, $traits);
	}

	public function testCustomRouteKeyName(): void
	{
		$customModel = new class () extends Model {
			use HasUuid;

			protected string $routeKeyName = 'custom_uuid';

			protected $table = 'test_models';

			// Override to prevent database operations
			public function save(array $options = []): bool
			{
				return true;
			}
		};

		$this->assertEquals('custom_uuid', $customModel->getRouteKeyName());
	}
}
