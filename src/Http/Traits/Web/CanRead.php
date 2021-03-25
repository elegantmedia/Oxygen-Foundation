<?php


namespace ElegantMedia\OxygenFoundation\Http\Traits\Web;

use ElegantMedia\PHPToolkit\Exceptions\FileSystem\FileNotFoundException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

trait CanRead
{

	/**
	 * @param $id
	 *
	 * @return Factory|View
	 * @throws FileNotFoundException
	 */
	public function show($id)
	{
		$entity = $this->repo->find($id);

		$data = [
			'pageTitle' => $this->getShowPageTitle($entity),
			'entity' => $entity,
		];

		$viewName = $this->getShowViewName();

		return view($viewName, $data);
	}

	/**
	 * @param Model $model
	 * @return string
	 */
	protected function getShowPageTitle(Model $model): string
	{
		return 'View ' . $this->getResourceSingularTitle();
	}

	/**
	 * @return mixed
	 * @throws FileNotFoundException
	 */
	protected function getShowViewName()
	{
		$view = $this->getVendorPrefixedViewName('show');

		if (view()->exists($view)) {
			return $view;
		}

		throw new FileNotFoundException("View $view not found");
	}
}
