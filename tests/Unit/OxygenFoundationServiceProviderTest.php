<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Unit;

use ElegantMedia\OxygenFoundation\OxygenFoundationServiceProvider;
use Orchestra\Testbench\TestCase;

class OxygenFoundationServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [OxygenFoundationServiceProvider::class];
    }
    
    public function test_service_provider_is_registered(): void
    {
        $this->assertTrue(
            $this->app->providerIsLoaded(OxygenFoundationServiceProvider::class)
        );
    }
    
    public function test_config_is_publishable(): void
    {
        $provider = new OxygenFoundationServiceProvider($this->app);
        
        // Get publishes array using reflection
        $reflection = new \ReflectionClass($provider);
        $property = $reflection->getProperty('publishes');
        $property->setAccessible(true);
        $publishes = $property->getValue();
        
        // Check that config is in publishes
        $hasConfig = false;
        foreach ($publishes as $group => $paths) {
            foreach ($paths as $from => $to) {
                if (str_contains($from, 'config/oxygen.php')) {
                    $hasConfig = true;
                    break 2;
                }
            }
        }
        
        $this->assertTrue($hasConfig, 'Config file should be publishable');
    }
    
    public function test_commands_are_registered(): void
    {
        // Get artisan instance
        $artisan = $this->app->make('Illuminate\Contracts\Console\Kernel');
        $commands = $artisan->all();
        
        // Check that oxygen commands are registered
        $this->assertArrayHasKey('oxygen:foundation:move-public', $commands);
        $this->assertArrayHasKey('db:refresh', $commands);
        $this->assertArrayHasKey('oxygen:foundation:install', $commands);
    }
}