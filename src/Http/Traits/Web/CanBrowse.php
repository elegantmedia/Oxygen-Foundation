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
			'allItems' => $this->dataRepo->search(),
			'isDestroyingEntityAllowed' => $this->isDestroyingEntityAllowed(),
		];

		$viewName = $this->getIndexViewName();

		return view($viewName, $data);
	}

	protected function getIndexViewName(): string
	{
		return $this->getVendorPrefixedViewName('index');
	}

}
