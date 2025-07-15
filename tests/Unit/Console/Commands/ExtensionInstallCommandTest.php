<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Unit\Console\Commands;

use ElegantMedia\OxygenFoundation\Console\Commands\ExtensionInstallCommand;
use Orchestra\Testbench\TestCase;
use ReflectionClass;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

class ExtensionInstallCommandTest extends TestCase
{
    private ExtensionInstallCommand $command;
    private ReflectionClass $reflection;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test implementation of the abstract class
        $this->command = new class extends ExtensionInstallCommand {
            protected $signature = 'test:extension';
            protected $description = 'Test Extension';
            
            public function getExtensionServiceProvider(): string
            {
                return 'TestServiceProvider';
            }
            
            public function getExtensionDisplayName(): string
            {
                return 'TestExtension';
            }
        };
        
        $this->reflection = new ReflectionClass($this->command);
    }
    
    public function test_get_auto_publish_tags_returns_default_tags(): void
    {
        $method = $this->reflection->getMethod('getAutoPublishTags');
        $method->setAccessible(true);
        
        $tags = $method->invoke($this->command);
        
        $this->assertIsArray($tags);
        $this->assertContains('oxygen::auto-publish', $tags);
    }
    
    public function test_get_files_from_package_throws_exception_for_non_existent_directory(): void
    {
        $method = $this->reflection->getMethod('getFilesFromPackage');
        $method->setAccessible(true);
        
        $this->expectException(DirectoryNotFoundException::class);
        $method->invoke($this->command, 'non/existent/directory');
    }
    
    public function test_update_routes_from_stubs_handles_missing_directory_gracefully(): void
    {
        $method = $this->reflection->getMethod('updateRoutesFromStubs');
        $method->setAccessible(true);
        
        // Should not throw exception even if directory doesn't exist
        $this->assertNull($method->invoke($this->command));
    }
    
    public function test_extension_interface_methods_are_implemented(): void
    {
        $this->assertIsString($this->command->getExtensionServiceProvider());
        $this->assertIsString($this->command->getExtensionDisplayName());
    }
    
    public function test_install_required_dependencies_returns_false_without_option(): void
    {
        // This test requires a proper command setup with Laravel's console infrastructure
        // which is too complex for a unit test. Moving this to integration tests.
        $this->markTestSkipped('This functionality requires Laravel console infrastructure and should be tested in integration tests.');
    }
    
    public function test_composer_autoload_does_not_run_in_tests(): void
    {
        $method = $this->reflection->getMethod('composerAutoload');
        $method->setAccessible(true);
        
        // Should not throw exception in test environment
        $this->assertNull($method->invoke($this->command));
    }
}