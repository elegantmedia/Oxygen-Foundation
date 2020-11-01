<?php

namespace ElegantMedia\OxygenFoundation\Macros;

use ElegantMedia\OxygenFoundation\Http\Response as BaseResponse;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Response;

trait RegisterResponseMacros
{

	/**
	 *
	 * Register Custom API Responses
	 *
	 */
	protected function registerResponseMacros(): void
	{
		$this->registerSuccessMacros();
		$this->registerErrorMacros();
	}

	/**
	 *
	 * Register Success Responses
	 *
	 */
	protected function registerSuccessMacros(): void
	{
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
	}


	/**
	 *
	 * Register Error Responses
	 *
	 */
	protected function registerErrorMacros(): void
	{
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
