<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Repository;

use ElegantMedia\OxygenFoundation\Contracts\RepositoryInterface;
use ElegantMedia\SimpleRepository\Repository\BaseRepository as SimpleRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

abstract class BaseRepository extends SimpleRepository implements RepositoryInterface
{
    /**
     * Fill model data from a request.
     */
    public function fillModelFromRequest(Request $request, ?int $id = null): Model
    {
        if (! $id) {
            $entity = $this->newModel();
        } else {
            $entity = $this->find($id);
        }

        if (! $entity) {
            throw new ModelNotFoundException;
        }

        $data = $request->all();
        $entity->fill($data);

        if (method_exists($this, 'beforeSavingModel')) {
            $this->beforeSavingModel($request, $entity);
        }

        $entity->save();

        if (method_exists($this, 'afterSavingModel')) {
            $this->afterSavingModel($request, $entity);
        }

        return $entity->isDirty() ? $entity->refresh() : $entity;
    }
}
