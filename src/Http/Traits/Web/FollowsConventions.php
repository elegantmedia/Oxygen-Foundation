<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Http\Traits\Web;

use ElegantMedia\OxygenFoundation\Entitities\OxygenRepository;
use ElegantMedia\PHPToolkit\Arr;
use ElegantMedia\SimpleRepository\Search\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait FollowsConventions
{
    protected $resourceEntityName;

    protected $viewsVendorName;

    protected $resourcePrefix;

    protected $indexRouteName;

    protected $isDestroyAllowed = false;

    /**
     * @var OxygenRepository
     */
    protected $repo;

    /**
     * @return mixed
     */
    public function getResourceEntityName()
    {
        if (empty($this->resourceEntityName)) {
            throw new \InvalidArgumentException('`$resourceEntityName` is not set.');
        }

        return $this->resourceEntityName;
    }

    public function getResourceSingularName(): string
    {
        return (string) Str::of($this->getResourceEntityName())->singular()->studly();
    }

    public function getResourceSingularTitle(): string
    {
        return (string) Str::of($this->getResourceEntityName())->singular()->title();
    }

    public function getResourcePluralName(): string
    {
        return Str::pluralStudly($this->getResourceEntityName());
    }

    /**
     * @return mixed
     */
    public function getResourcePrefix()
    {
        return $this->resourcePrefix;
    }

    public function getResourceKebabName(): string
    {
        return Str::kebab($this->getResourceEntityName());
    }

    public function isDestroyAllowed(): bool
    {
        return $this->isDestroyAllowed;
    }

    protected function getModel()
    {
        if (! $this->repo) {
            throw new \InvalidArgumentException('Repository is not defined.');
        }

        return $this->repo->getModel();
    }

    /**
     * @return mixed
     */
    protected function getViewsVendorName()
    {
        return $this->viewsVendorName;
    }

    protected function getVendorPrefixedViewName($suffix): string
    {
        $suffix = Arr::implodeIgnoreEmpty('.', [
            $this->getResourcePrefix(),
            $suffix,
        ]);

        $vendorName = $this->getViewsVendorName();

        if (! empty($vendorName)) {
            return $vendorName . '::' . $suffix;
        }

        return $suffix;
    }

    protected function getIndexRouteName($suffix = 'index'): string
    {
        if ($this->indexRouteName) {
            return $this->indexRouteName;
        }

        $route = Arr::implodeIgnoreEmpty('.', [
            $this->getResourcePrefix(),
            $this->getResourceKebabName(),
            $suffix,
        ]);

        if (! \Illuminate\Support\Facades\Route::has($route)) {
            throw new \InvalidArgumentException(
                "Route `$route` is not defined. Create this route or return a valid route from getIndexRouteName()"
            );
        }

        $this->indexRouteName = $route;

        return $route;
    }

    protected function storeOrUpdateRequest(Request $request, $id = null, $rules = null, $messages = null): Model
    {
        // validations
        if ($rules) {
            $this->validate($request, $rules, $messages ?? []);
        }

        // save and return model
        return $this->repo->fillModelFromRequest($request, $id);
    }

    /**
     * @param bool $withDefaults
     */
    protected function newSearchFilter($withDefaults = true): Filterable
    {
        return $this->repo->newSearchFilter($withDefaults);
    }
}
