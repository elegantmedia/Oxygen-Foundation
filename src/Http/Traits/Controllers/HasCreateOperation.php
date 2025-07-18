<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Http\Traits\Controllers;

use ElegantMedia\PHPToolkit\Exceptions\FileSystem\FileNotFoundException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait HasCreateOperation
{
	/**
	 * Create a new record view.
	 */
	public function create(): Factory|View
	{
		$data = [
			'pageTitle' => $this->getCreatePageTitle(),
			'entity' => $this->getModel(),
			'form' => $this->getCreateForm($this->getModel()),
		];

		$viewName = $this->getCreateViewName();

		return view($viewName, $data);
	}

	/**
	 * Get form data for create view.
	 */
	protected function getCreateForm($entity = null): ?array
	{
		return null;
	}

	/**
	 * Handle store/POST method for the controller.
	 */
	public function store(Request $request): RedirectResponse
	{
		$rules = null;
		if (method_exists($this->getModel(), 'getCreateRules')) {
			$rules = $this->getModel()->getCreateRules();
		}

		$messages = null;
		if (method_exists($this->getModel(), 'getCreateValidationMessages')) {
			$messages = $this->getModel()->getCreateValidationMessages();
		}

		$entity = $this->storeOrUpdateRequest($request, null, $rules, $messages);

		return redirect()->route($this->getRouteToRedirectToAfterStore());
	}

	protected function getCreatePageTitle(): string
	{
		return 'Add New ' . $this->getResourceSingularTitle();
	}

	protected function getCreateViewName(): string
	{
		$view = $this->getVendorPrefixedViewName('create');

		if (view()->exists($view)) {
			return $view;
		}

		$view = $this->getVendorPrefixedViewName('form');

		if (view()->exists($view)) {
			return $view;
		}

		throw new FileNotFoundException("View $view not found");
	}

	protected function getRouteToRedirectToAfterStore(): string
	{
		return $this->getIndexRouteName();
	}
}
