<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Unit\Scout;

use ElegantMedia\OxygenFoundation\Scout\Engines\SecureKeywordEngine;
use ElegantMedia\OxygenFoundation\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
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
        $this->engine = new SecureKeywordEngine;
    }

    public function test_search_escapes_special_characters(): void
    {
        $this->markTestSkipped('Requires database connection to test properly');
    }

    public function test_throws_exception_when_searchable_fields_not_defined(): void
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('getSearchableFields')->never();

        $builder = new Builder($model, 'test');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Model must implement getSearchableFields() method');

        $this->engine->search($builder);
    }

    public function test_paginate_returns_paginated_results(): void
    {
        $this->markTestSkipped('Requires database connection to test properly');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
