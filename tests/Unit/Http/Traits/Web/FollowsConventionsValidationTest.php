<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Unit\Http\Traits\Web;

use ElegantMedia\OxygenFoundation\Http\Traits\Web\FollowsConventions;
use ElegantMedia\OxygenFoundation\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FollowsConventionsValidationTest extends TestCase
{
	public function testStoreOrUpdateRequestValidatesUsingRequest(): void
	{
		$controller = new class () {
			use FollowsConventions;

			public function callStore(Request $request, ?array $rules = null, ?array $messages = null)
			{
				return $this->storeOrUpdateRequest($request, null, $rules, $messages);
			}
		};

		$request = Request::create('/', 'POST', []);

		$this->expectException(ValidationException::class);
		$controller->callStore($request, ['name' => 'required']);
	}
}
