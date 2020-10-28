<?php


namespace ElegantMedia\OxygenFoundation\TestPackage\Http\Controllers;

use App\Http\Controllers\Manage\ManageBaseController;
use ElegantMedia\OxygenFoundation\Http\Traits\Web\CanCRUD;
use ElegantMedia\OxygenFoundation\TestPackage\Entities\Testers\TestersRepository;

class TestCRUDController extends ManageBaseController
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
