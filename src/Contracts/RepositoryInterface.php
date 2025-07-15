<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface RepositoryInterface
{
    /**
     * Fill model data from a request.
     */
    public function fillModelFromRequest(Request $request, ?int $id = null): Model;
}
