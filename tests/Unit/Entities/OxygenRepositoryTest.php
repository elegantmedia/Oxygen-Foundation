<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Unit\Entities;

use ElegantMedia\OxygenFoundation\Entities\OxygenRepository;
use ElegantMedia\OxygenFoundation\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Mockery;

class OxygenRepositoryTest extends TestCase
{
    public function testFillModelFromRequestFiltersMetaAndUnfillable(): void
    {
        $model = Mockery::mock(Model::class);

        $repo = new class ($model) extends OxygenRepository {
            protected Model $model;

            public function __construct(Model $model)
            {
                $this->model = $model;
            }

            public function newModel(): Model
            {
                return clone $this->model;
            }

            public function find($id, array $with = []): ?Model
            {
                return $id ? $this->model : null;
            }
        };

        $request = Request::create('/', 'POST', [
            'name' => 'Repo',
            '_token' => 'x',
            '_method' => 'POST',
            'foo' => 'bar',
        ]);

        $model->shouldReceive('getFillable')->andReturn(['name']);
        $model->shouldReceive('fill')->once()->with(['name' => 'Repo']);
        $model->shouldReceive('save')->once();
        $model->shouldReceive('isDirty')->once()->andReturn(false);

        $result = $repo->fillModelFromRequest($request);

        $this->assertInstanceOf(Model::class, $result);
    }
}

