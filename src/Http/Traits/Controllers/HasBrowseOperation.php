<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Http\Traits\Controllers;

use ElegantMedia\SimpleRepository\Search\Filterable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

trait HasBrowseOperation
{
    /**
     * Index Results filter. Override this method to customise the results.
     */
    protected function getIndexFilter(): Filterable
    {
        $filter = $this->repo->newSearchFilter(true);

        return $filter;
    }

    /**
     * Index method of the controller.
     */
    public function index(): Factory|View
    {
        $data = [
            'pageTitle' => $this->getResourcePluralName(),
            'allItems' => $this->repo->search($this->getIndexFilter()),
            'isDestroyingEntityAllowed' => $this->isDestroyAllowed(),
            'canCreateEntities' => $this->canCreateEntities(),
            'canEditEntities' => $this->canEditEntities(),
        ];

        $viewName = $this->getIndexViewName();

        return view($viewName, $data);
    }

    protected function getIndexViewName(): string
    {
        return $this->getVendorPrefixedViewName('index');
    }

    protected function canCreateEntities(): bool
    {
        return method_exists($this, 'create');
    }

    public function canEditEntities(): bool
    {
        return method_exists($this, 'edit');
    }
}
