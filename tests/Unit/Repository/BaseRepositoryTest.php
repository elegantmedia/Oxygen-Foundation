<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Unit\Repository;

use ElegantMedia\OxygenFoundation\Repository\BaseRepository;
use ElegantMedia\OxygenFoundation\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Mockery;

class BaseRepositoryTest extends TestCase
{
    private BaseRepository $repository;

    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = Mockery::mock(Model::class);
        $this->repository = new class($this->model) extends BaseRepository
        {
            protected Model $model;

            public function __construct(Model $model)
            {
                $this->model = $model;
            }

            public function getModel(): Model
            {
                return $this->model;
            }

            public function newModel(): Model
            {
                return clone $this->model;
            }

            public function find(string|int $id, array $with = []): ?Model
            {
                return $id ? $this->model : null;
            }
        };
    }

    public function test_fill_model_from_request_creates_new_model_when_no_id(): void
    {
        $request = Request::create('/', 'POST', ['name' => 'Test']);

        $this->model->shouldReceive('fill')->once()->with(['name' => 'Test']);
        $this->model->shouldReceive('save')->once();
        $this->model->shouldReceive('isDirty')->once()->andReturn(false);

        $result = $this->repository->fillModelFromRequest($request);

        $this->assertInstanceOf(Model::class, $result);
    }

    public function test_fill_model_from_request_updates_existing_model(): void
    {
        $request = Request::create('/', 'PUT', ['name' => 'Updated']);

        $this->model->shouldReceive('fill')->once()->with(['name' => 'Updated']);
        $this->model->shouldReceive('save')->once();
        $this->model->shouldReceive('isDirty')->once()->andReturn(true);
        $this->model->shouldReceive('refresh')->once()->andReturn($this->model);

        $result = $this->repository->fillModelFromRequest($request, 1);

        $this->assertInstanceOf(Model::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
