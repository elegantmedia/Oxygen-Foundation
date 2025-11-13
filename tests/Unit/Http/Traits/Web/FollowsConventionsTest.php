<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Unit\Http\Traits\Web;

use ElegantMedia\OxygenFoundation\Http\Traits\Web\FollowsConventions;
use ElegantMedia\OxygenFoundation\Tests\TestCase;

class FollowsConventionsTest extends TestCase
{
    public function testTraitIsLoadableAndUsable(): void
    {
        $controller = new class () {
            use FollowsConventions;

            public function setResourceEntityName(string $name): self
            {
                $this->resourceEntityName = $name;

                return $this;
            }

            public function setViewsVendorName(string $name): self
            {
                $this->viewsVendorName = $name;

                return $this;
            }

            public function setResourcePrefix(string $name): self
            {
                $this->resourcePrefix = $name;

                return $this;
            }

            // Expose protected method for testing
            public function vendorPrefixed(string $suffix): string
            {
                return $this->getVendorPrefixedViewName($suffix);
            }
        };

        $this->assertTrue(trait_exists(FollowsConventions::class));

        // Exercise a subset of trait methods
        $controller->setResourceEntityName('Testers')
            ->setViewsVendorName('oxygen')
            ->setResourcePrefix('manage');

        $this->assertSame('Tester', $controller->getResourceSingularName());
        $this->assertSame('Tester', $controller->getResourceSingularTitle());
        $this->assertSame('Testers', $controller->getResourcePluralName());
        $this->assertSame('testers', $controller->getResourceKebabName());
        $this->assertSame('oxygen::manage.index', $controller->vendorPrefixed('index'));
    }

    public function testTraitImportsOxygenRepositoryFromEntitiesNamespace(): void
    {
        $path = 'src/Http/Traits/Web/FollowsConventions.php';
        $this->assertFileExists($path);

        $contents = (string) file_get_contents($path);
        $this->assertStringContainsString(
            'use ElegantMedia\\OxygenFoundation\\Entities\\OxygenRepository;',
            $contents
        );
        $this->assertStringNotContainsString('Entitities\\OxygenRepository', $contents);
    }
}
