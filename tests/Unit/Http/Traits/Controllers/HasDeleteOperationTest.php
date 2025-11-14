<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Unit\Http\Traits\Controllers;

use ElegantMedia\OxygenFoundation\Http\Traits\Controllers\HasDeleteOperation;
use ElegantMedia\OxygenFoundation\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HasDeleteOperationTest extends TestCase
{
    public function testDestroyUnauthorizedRespondsWith403(): void
    {
        $controller = new class () {
            use HasDeleteOperation;

            public function isDestroyAllowed(): bool
            {
                return false;
            }

            public function getIndexRouteName(): string
            {
                return 'home';
            }

            // Dummy repo to satisfy property presence if needed
            public $repo;

            public function __construct()
            {
                $this->repo = new class () {
                    public function delete($id): void {}
                };
            }
        };

        try {
            $controller->destroy(123);
            $this->fail('Expected HttpException to be thrown');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
            $this->assertStringContainsString('not authorized', $e->getMessage());
        }
    }
}

