<?php


namespace ElegantMedia\OxygenFoundation\Entitities;

use ElegantMedia\SimpleRepository\SimpleBaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class OxygenRepository extends SimpleBaseRepository
{

	/**
	 *
	 * Fill model data from a request
	 *
	 * @param Request $request
	 * @param null $id
	 *
	 * @return Model
	 */
	public function fillModelFromRequest(Request $request, $id = null): Model
	{
		if (!$id) {
			$entity = $this->newModel();
		} else {
			$entity = $this->find($id);
		}

		if (!$entity) {
			throw new ModelNotFoundException();
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
