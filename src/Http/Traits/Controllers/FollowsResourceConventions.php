<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Http\Traits\Controllers;

use ElegantMedia\OxygenFoundation\Repository\BaseRepository;
use ElegantMedia\SimpleRepository\Search\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait FollowsResourceConventions
{
	protected ?string $resourceEntityName = null;

	protected ?string $viewsVendorName = null;

	protected ?string $resourcePrefix = null;

	protected ?string $indexRouteName = null;

	protected bool $isDestroyAllowed = false;

	protected ?BaseRepository $repo = null;

	public function getResourceEntityName(): string
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

	public function getResourcePrefix(): ?string
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

	protected function getModel(): Model
	{
		if (! $this->repo) {
			throw new \InvalidArgumentException('Repository is not defined.');
		}

		return $this->repo->getModel();
	}

	protected function getViewsVendorName(): ?string
	{
		return $this->viewsVendorName;
	}

	protected function getVendorPrefixedViewName(string $suffix): string
	{
		$parts = array_filter([
			$this->getResourcePrefix(),
			$suffix,
		]);

		$suffix = implode('.', $parts);

		$vendorName = $this->getViewsVendorName();

		if (! empty($vendorName)) {
			return $vendorName . '::' . $suffix;
		}

		return $suffix;
	}

	protected function getIndexRouteName(string $suffix = 'index'): string
	{
		if ($this->indexRouteName) {
			return $this->indexRouteName;
		}

		$parts = array_filter([
			$this->getResourcePrefix(),
			$this->getResourceKebabName(),
			$suffix,
		]);

		$route = implode('.', $parts);

		if (! Route::has($route)) {
			throw new \InvalidArgumentException(
				"Route `$route` is not defined. Create this route or return a valid route from getIndexRouteName()"
			);
		}

		$this->indexRouteName = $route;

		return $route;
	}

	protected function storeOrUpdateRequest(
		Request $request,
		?int $id = null,
		?array $rules = null,
		?array $messages = null
	): Model {
		// validations
		if ($rules) {
			$this->validate($request, $rules, $messages ?? []);
		}

		// save and return model
		return $this->repo->fillModelFromRequest($request, $id);
	}

	protected function newSearchFilter(bool $withDefaults = true): Filterable
	{
		return $this->repo->newSearchFilter($withDefaults);
	}
}
