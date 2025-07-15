<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use ElegantMedia\OxygenFoundation\Entities\OxygenRepository;

class APIBaseController extends App\Http\Controllers\Controller
{
    /** @var OxygenRepository */
    protected $repo;
}
