<?php

/*
|--------------------------------------------------------------------------
| Custom Routing/API Responses
|--------------------------------------------------------------------------
*/
namespace Illuminate\Contracts\Routing;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\JsonResponse;

/**
 * @method JsonResponse apiSuccess($payload = null, $message = '') Send an API success response.
 * @method JsonResponse apiSuccessPaginated(Paginator $paginator, $message = '', $customData = []) Send a paginated response.
 *
 * @method JsonResponse apiErrorUnauthorized($message = null) Send 401 response.
 * @method JsonResponse apiErrorAccessDenied($message = null) Send 403 response.
 * @method JsonResponse apiError($message = null, $payload = null, $statusCode = 422) Send a generic error response.
 */
interface ResponseFactory
{
}


/*
|--------------------------------------------------------------------------
| Custom Database Columns
|--------------------------------------------------------------------------
*/
namespace Illuminate\Database\Schema;

/**
 *
 * @method \Illuminate\Database\Schema\Blueprint location($prefix = '')	Latitude,longitude database field
 * @method \Illuminate\Database\Schema\Blueprint place($prefix = '')	Place with address details, lat, lng coordinates
 * @method \Illuminate\Database\Schema\Blueprint file($prefix = '')		File with path, disk, url
 *
 * @method \Illuminate\Database\Schema\Blueprint dropLocation()($prefix = '') 	Drop location fields
 * @method \Illuminate\Database\Schema\Blueprint dropPlace($prefix = '') 		Drop place fields
 * @method \Illuminate\Database\Schema\Blueprint dropFile($prefix = '') 		Drop file fields
 *
 */
class Blueprint
{
}
