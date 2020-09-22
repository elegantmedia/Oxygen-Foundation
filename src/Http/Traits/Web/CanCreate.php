<?php


namespace ElegantMedia\OxygenFoundation\Http\Traits\Web;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

trait CanCreate
{

	/**
	 *
	 * Create a new record view
	 *
	 * @return Factory|View
	 */
	public function create()
	{
		$data = [
			'pageTitle' => $this->getCreatePageTitle(),
			'entity' => $this->model,
		];

		$viewName = $this->getCreateViewName();

		return view($viewName, $data);
	}

	/**
	 *
	 * Handle store/POST method for the controller
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function store(Request $request): RedirectResponse
	{
		$rules = null;
		if (method_exists($this->getModel(), 'getCreateRules')) {
			$rules = $this->getModel()->getCreateRules();
		}

		$messages = null;
		if (method_exists($this->getModel(), 'getCreateValidationMessages')) {
			$messages = $this->getModel()->getValidationMessages();
		}

		$entity = $this->storeOrUpdateRequest($request, null, $rules, $messages);

		return redirect()->route($this->getRouteToRedirectToAfterStore());
	}

	protected function getCreatePageTitle()
	{
		return 'Add New ' . $this->getResourceSingularName();
	}

	protected function getCreateViewName()
	{
		return $this->getVendorPrefixedViewName('create');
	}

	protected function getRouteToRedirectToAfterStore()
	{
		return $this->getIndexRouteName();
	}
}
