<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Feature;

use ElegantMedia\OxygenFoundation\Core\Pathfinder;
use ElegantMedia\OxygenFoundation\Facades\Navigator;
use ElegantMedia\OxygenFoundation\OxygenFoundationServiceProvider;
use ElegantMedia\OxygenFoundation\TestPackage\TestPackageServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $pathfinder;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->pathfinder = app(Pathfinder::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            OxygenFoundationServiceProvider::class,
            TestPackageServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Navigator' => Navigator::class,
        ];
    }
}
