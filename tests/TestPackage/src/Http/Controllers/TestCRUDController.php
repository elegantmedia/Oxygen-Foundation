<?php


namespace ElegantMedia\OxygenFoundation\TestPackage\Http\Controllers;

use ElegantMedia\OxygenFoundation\Http\Controllers\WebCRUDController;
use ElegantMedia\OxygenFoundation\Http\Traits\Web\CanCRUD;
use ElegantMedia\OxygenFoundation\TestPackage\Entities\Testers\TestersRepository;
use Illuminate\Routing\Controller;

class TestCRUDController extends WebCRUDController
{

	use CanCRUD;

	public function __construct(TestersRepository $repo)
	{
		$this->repo = $repo;

		$this->resourceEntityName = 'Testers';

		$this->viewsVendorName = 'oxygen::test-extension';

		$this->resourcePrefix = 'manage';

		$this->isDestroyAllowed = false;
	}

}
