<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Unit\Http\Traits\Web;

use ElegantMedia\OxygenFoundation\Http\Traits\Web\CanDestroy;
use ElegantMedia\OxygenFoundation\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CanDestroyTest extends TestCase
{
	public function testDestroyUnauthorizedRespondsWith403(): void
	{
		$controller = new class () {
			use CanDestroy;

			public function isDestroyAllowed(): bool
			{
				return false;
			}

			public function getIndexRouteName(): string
			{
				return 'home';
			}

			// Dummy repo property for completeness
			public $repo;

			public function __construct()
			{
				$this->repo = new class () {
					public function delete($id): void
					{
					}
				};
			}
		};

		try {
			$controller->destroy(456);
			$this->fail('Expected HttpException to be thrown');
		} catch (HttpException $e) {
			$this->assertSame(403, $e->getStatusCode());
			$this->assertStringContainsString('not authorized', $e->getMessage());
		}
	}
}
