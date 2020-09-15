<?php


namespace ElegantMedia\SimpleRepository;

use ElegantMedia\OxygenFoundation\Responses\Response as BaseResponse;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class OxygenFoundationServiceProvider extends ServiceProvider
{

	public function boot()
	{
		// publish config
		$this->publishes([
			__DIR__.'/../config/oxygen.php' 	=> config_path('oxygen.php'),
			__DIR__.'/../config/features.php' 	=> config_path('features.php')
		], 'oxygen-config');
	}

	public function register()
	{
		$this->mergeConfigFrom(__DIR__ . '/../config/oxygen.php', 'oxygen');
		$this->mergeConfigFrom(__DIR__ . '/../config/features.php', 'features');

		$this->registerCustomResponses();
	}

	/**
	 *
	 * Register Custom API Responses
	 *
	 */
	protected function registerCustomResponses(): void
	{
		/*
		|--------------------------------------------------------------------------
		| Success Responses
		|--------------------------------------------------------------------------
		|
		|
		|
		*/

		// success
		Response::macro('apiSuccess', function ($payload = null, $message = '') {
			return Response::json([
				'payload'	=> $payload,
				'message' 	=> $message,
				'result' 	=> true,
			]);
		});

		Response::macro('apiSuccessPaginated', function (Paginator $paginator, $message = '', $customData = []) {
			$paginatorArray = $paginator->toArray();
			if (isset($paginatorArray['data'])) {
				unset($paginatorArray['data']);
			}

			return Response::json(array_merge($customData, [
				'payload' => $paginator->items(),
				'paginator' => $paginatorArray,
				'message' => $message,
				'result'  => true,
			]));
		});

		/*
		|--------------------------------------------------------------------------
		| Error Messages
		|--------------------------------------------------------------------------
		|
		|
		|
		*/

		// unauthorized
		Response::macro('apiErrorUnauthorized', function (
			$message = 'Authentication failed. Try to login again.',
			$responseCode = BaseResponse::HTTP_UNAUTHORIZED
) {

			return Response::json([
				'message' => $message,
				'payload' => null,
				'result'  => false,
			], $responseCode); // 401 Error
		});


		// Generic API authorization error
		Response::macro('apiErrorAccessDenied', function ($message = 'Access denied.') {

			return Response::json([
				'message' => $message,
				'payload' => null,
				'result'  => false,
			], BaseResponse::HTTP_FORBIDDEN); // 403 Error
		});

		// Generic error
		Response::macro(
			'apiError',
			function (
				$message = 'Unable to process request. Please try again later.',
				$payload = null,
				$statusCode = BaseResponse::HTTP_UNPROCESSABLE_ENTITY
			) {

				return Response::json([
					'message' => $message,
					'payload' => $payload,
					'result'  => false,
				], $statusCode);
			}
		);
	}
}
