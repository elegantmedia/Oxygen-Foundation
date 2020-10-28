<?php

namespace ElegantMedia\OxygenFoundation;

use ElegantMedia\OxygenFoundation\Console\Commands\Developer\MovePublicFolderCommand;
use ElegantMedia\OxygenFoundation\Console\Commands\Developer\RefreshDatabaseCommand;
use ElegantMedia\OxygenFoundation\Console\Commands\OxygenFoundationInstallCommand;
use ElegantMedia\OxygenFoundation\Console\Commands\SeedCommand;
use ElegantMedia\OxygenFoundation\Core\OxygenCore;
use ElegantMedia\OxygenFoundation\Core\Pathfinder;
use ElegantMedia\OxygenFoundation\Http\Response as BaseResponse;
use ElegantMedia\OxygenFoundation\Navigation\Navigator;
use ElegantMedia\OxygenFoundation\Scout\KeywordSearchEngine;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;

class OxygenFoundationServiceProvider extends ServiceProvider
{

	public function register()
	{
		$this->app->singleton('oxygen', function () {
			return new OxygenCore();
		});

		$this->app->singleton(Pathfinder::class);

		$this->mergeConfigFrom(__DIR__ . '/../config/oxygen.php', 'oxygen');
		$this->mergeConfigFrom(__DIR__ . '/../config/features.php', 'features');
		$this->mergeConfigFrom(__DIR__ . '/../config/scout.php', 'scout');

		$this->registerCustomResponses();
		$this->registerCommands();

		// Register Navigator Facade
		$this->app->singleton('elegantmedia.oxygen.navigator', function () {
			return new Navigator();
		});
	}

	public function boot()
	{
		// publish config
		$this->publishes([
			__DIR__.'/../config/oxygen.php' 	=> config_path('oxygen.php'),
			__DIR__.'/../config/features.php' 	=> config_path('features.php'),
			__DIR__.'/../config/scout.php' 		=> config_path('scout.php'),
		], 'oxygen-config');

		$this->publishes([
			__DIR__.'/../stubs/app' => app_path(),
		], 'oxygen-foundation-install');

		$this->bootScoutSearchEngines();
	}

	protected function bootScoutSearchEngines(): void
	{
		resolve(EngineManager::class)->extend('keyword', function () {
			return new KeywordSearchEngine();
		});
	}


	protected function registerCommands(): void
	{
		$this->commands([
			SeedCommand::class,
			OxygenFoundationInstallCommand::class,
			MovePublicFolderCommand::class,
		]);

		if (!app()->environment('production')) {
			$this->commands([
				RefreshDatabaseCommand::class,
			]);
		}
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
