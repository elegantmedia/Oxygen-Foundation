<?php


namespace ElegantMedia\OxygenFoundation\Http\Traits\Web;

trait CanBrowse
{

	/**
	 *
	 * Index method of the controller
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		$data = [
			'pageTitle' => $this->getResourcePluralName(),
			'allItems' => $this->repo->searchPaginate(),
			'isDestroyingEntityAllowed' => $this->isDestroyAllowed(),
		];

		$viewName = $this->getIndexViewName();

		return view($viewName, $data);
	}

	protected function getIndexViewName(): string
	{
		return $this->getVendorPrefixedViewName('index');
	}
}
