<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Unit\Scout;

use ElegantMedia\OxygenFoundation\Scout\Engines\SecureKeywordEngine;
use ElegantMedia\OxygenFoundation\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Builder;
use Mockery;

class TestSearchableModel extends Model
{
	public function getSearchableFields(): array
	{
		return ['name', 'description'];
	}
}

class SecureKeywordEngineTest extends TestCase
{
	private SecureKeywordEngine $engine;

	protected function setUp(): void
	{
		parent::setUp();
		$this->engine = new SecureKeywordEngine();
	}

	public function testSearchEscapesSpecialCharacters(): void
	{
		$model = new TestSearchableModel();
		$builder = new Builder($model, 'test%_query\\');

		// Use reflection to test the protected prepareSearchTerms method
		$reflection = new \ReflectionClass($this->engine);
		$method = $reflection->getMethod('prepareSearchTerms');
		$method->setAccessible(true);

		$searchTerms = $method->invoke($this->engine, 'test%_query\\');

		// Check that special characters are escaped
		$this->assertIsArray($searchTerms);
		$this->assertCount(2, $searchTerms);
		$this->assertEquals('%test\\%\\_query\\\\%', $searchTerms[0]);
		$this->assertStringContainsString('test\\%\\_query\\\\', $searchTerms[1]);
	}

	public function testThrowsExceptionWhenSearchableFieldsNotDefined(): void
	{
		$model = Mockery::mock(Model::class);
		$model->shouldReceive('getSearchableFields')->never();

		$builder = new Builder($model, 'test');

		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Model must implement getSearchableFields() method');

		$this->engine->search($builder);
	}

	public function testPaginateReturnsPaginatedResults(): void
	{
		// Create a mock query builder that returns a paginator
		$queryBuilder = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
		$paginator = Mockery::mock(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class);

		$queryBuilder->shouldReceive('where')->once()->andReturnSelf();
		$queryBuilder->shouldReceive('paginate')
			->once()
			->with(15, ['*'], 'page', 1)
			->andReturn($paginator);

		// Create a mock model that returns our query builder
		$model = Mockery::mock(TestSearchableModel::class)->makePartial();
		$model->shouldReceive('query')->once()->andReturn($queryBuilder);
		$model->shouldReceive('getSearchableFields')->once()->andReturn(['name', 'description']);

		$builder = new Builder($model, 'test');

		$result = $this->engine->paginate($builder, 15, 1);

		$this->assertSame($paginator, $result);
	}

	protected function tearDown(): void
	{
		Mockery::close();
		parent::tearDown();
	}
}
