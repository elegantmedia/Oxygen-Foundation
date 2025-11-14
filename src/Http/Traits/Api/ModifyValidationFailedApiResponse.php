<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Http\Traits\Api;

use Illuminate\Support\Arr;

trait ModifyValidationFailedApiResponse
{
	/**
	 * Validate the given request with the given rules.
	 *
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function validate(
		\Illuminate\Http\Request $request,
		array $rules,
		array $messages = [],
		array $customAttributes = []
	): ?array {
		try {
			return parent::validate($request, $rules, $messages, $customAttributes);
		} catch (\Illuminate\Validation\ValidationException $ex) {
			if ($request->expectsJson() && ! $request->pjax()) {
				$errorMessages = Arr::flatten($ex->errors());
				$mergedErrorMessages = implode(' ', $errorMessages);
				$data = [
					'errors' => $ex->errors(),
				];

				throw new \Illuminate\Http\Exceptions\HttpResponseException(
					response()->apiError(
						$mergedErrorMessages,
						$data,
						\Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
					)
				);
			}

			throw $ex;
		}
	}
}
