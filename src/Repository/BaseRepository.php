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
			throw new ModelNotFoundException();
		}

        // Prefer validated data if available (FormRequest), otherwise restrict to fillable fields
        if (method_exists($request, 'validated')) {
            $data = $request->validated();
        } else {
            $fillable = $entity->getFillable();
            $data = empty($fillable) ? $request->except(['_token', '_method']) : $request->only($fillable);
        }

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
