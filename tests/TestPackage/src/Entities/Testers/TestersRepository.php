<?php

namespace ElegantMedia\OxygenFoundation\TestPackage\Entities\Testers;

use App\Entities\BaseRepository;

class TestersRepository extends BaseRepository
{

	public function __construct(Tester $model)
	{
		parent::__construct($model);
	}
}
