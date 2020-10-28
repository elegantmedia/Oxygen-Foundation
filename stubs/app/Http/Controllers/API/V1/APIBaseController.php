<?php

namespace App\Http\Controllers\API\V1;

use ElegantMedia\OxygenFoundation\Entities\OxygenRepository;
use \Illuminate\Http\Response;

class APIBaseController extends App\Http\Controllers\Controller
{

	/** @var OxygenRepository */
	protected $repo;

}
