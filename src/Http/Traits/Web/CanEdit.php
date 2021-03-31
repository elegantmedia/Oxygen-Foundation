<?php


namespace ElegantMedia\OxygenFoundation\Http\Traits\Web;

use ElegantMedia\PHPToolkit\Exceptions\FileSystem\FileNotFoundException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

trait CanEdit
{

	/**
	 *
	 * Edit the resource
	 *
	 * @param $id
	 *
	 * @return Factory|View
	 * @throws FileNotFoundException
	 */
	public function edit($id)
	{
		$entity = $this->repo->find($id);

		$data = [
			'pageTitle' => $this->getEditPageTitle($entity),
			'entity' => $entity,
			'form' => $this->getEditForm($entity),
		];

		$viewName = $this->getEditViewName();

		return view($viewName, $data);
	}

	/**
	 * @param null $entity
	 *
	 * @return array
	 */
	protected function getEditForm($entity = null)
	{
		return null;
	}

	/**
	 *
	 * Handle update/PUT request for the controller
	 *
	 * @param Request $request
	 * @param         $id
	 *
	 * @return RedirectResponse
	 */
	public function update(Request $request, $id): RedirectResponse
	{
		$rules = null;
		if (method_exists($this->getModel(), 'getUpdateRules')) {
			$rules = $this->getModel()->getUpdateRules();
		}

		$messages = null;
		if (method_exists($this->getModel(), 'getUpdateValidationMessages')) {
			$messages = $this->getModel()->getUpdateValidationMessages();
		}

		$entity = $this->storeOrUpdateRequest($request, $id, $rules, $messages);

		return redirect()->route($this->getRouteToRedirectToAfterUpdate());
	}

	/**
	 * @param Model $model
	 * @return string
	 */
	protected function getEditPageTitle(Model $model): string
	{
		return 'Edit ' . $this->getResourceSingularTitle();
	}

	protected function getEditViewName()
	{
		$view = $this->getVendorPrefixedViewName('edit');

		if (view()->exists($view)) {
			return $view;
		}

		$view = $this->getVendorPrefixedViewName('form');

		if (view()->exists($view)) {
			return $view;
		}

		throw new FileNotFoundException("View $view not found");
	}

	protected function getRouteToRedirectToAfterUpdate()
	{
		return $this->getIndexRouteName();
	}
}
