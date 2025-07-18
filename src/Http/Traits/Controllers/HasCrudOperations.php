<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Http\Traits\Controllers;

trait HasCrudOperations
{
	use FollowsResourceConventions;
	use HasBrowseOperation;
	use HasCreateOperation;
	use HasDeleteOperation;
	use HasEditOperation;
}
