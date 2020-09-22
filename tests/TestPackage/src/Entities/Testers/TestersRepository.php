<?php


namespace ElegantMedia\OxygenFoundation\TestPackage\Entities\Testers;


class TestersRepository extends \ElegantMedia\SimpleRepository\SimpleBaseRepository
{

	public function __construct(Tester $model)
	{
		parent::__construct($model);
	}

}
