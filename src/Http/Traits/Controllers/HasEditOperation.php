<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Http\Traits\Controllers;

use ElegantMedia\PHPToolkit\Exceptions\FileSystem\FileNotFoundException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait HasEditOperation
{
    /**
     * Edit the resource.
     */
    public function edit(int|string $id): Factory|View
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
     * Get form data for edit view.
     */
    protected function getEditForm($entity = null): ?array
    {
        return null;
    }

    /**
     * Handle update/PUT request for the controller.
     */
    public function update(Request $request, int|string $id): RedirectResponse
    {
        $rules = null;
        if (method_exists($this->getModel(), 'getUpdateRules')) {
            $rules = $this->getModel()->getUpdateRules();
        }

        $messages = null;
        if (method_exists($this->getModel(), 'getUpdateValidationMessages')) {
            $messages = $this->getModel()->getUpdateValidationMessages();
        }

        $entity = $this->storeOrUpdateRequest($request, (int) $id, $rules, $messages);

        return redirect()->route($this->getRouteToRedirectToAfterUpdate());
    }

    protected function getEditPageTitle(Model $model): string
    {
        return 'Edit ' . $this->getResourceSingularTitle();
    }

    protected function getEditViewName(): string
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

    protected function getRouteToRedirectToAfterUpdate(): string
    {
        return $this->getIndexRouteName();
    }
}
