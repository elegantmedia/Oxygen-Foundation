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
		$this->command = new class () extends ExtensionInstallCommand {
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

	public function testGetAutoPublishTagsReturnsDefaultTags(): void
	{
		$method = $this->reflection->getMethod('getAutoPublishTags');
		$method->setAccessible(true);

		$tags = $method->invoke($this->command);

		$this->assertIsArray($tags);
		$this->assertContains('oxygen::auto-publish', $tags);
	}

	public function testGetFilesFromPackageThrowsExceptionForNonExistentDirectory(): void
	{
		$method = $this->reflection->getMethod('getFilesFromPackage');
		$method->setAccessible(true);

		$this->expectException(DirectoryNotFoundException::class);
		$method->invoke($this->command, 'non/existent/directory');
	}

	public function testUpdateRoutesFromStubsHandlesMissingDirectoryGracefully(): void
	{
		$method = $this->reflection->getMethod('updateRoutesFromStubs');
		$method->setAccessible(true);

		// Should not throw exception even if directory doesn't exist
		$this->assertNull($method->invoke($this->command));
	}

	public function testExtensionInterfaceMethodsAreImplemented(): void
	{
		$this->assertIsString($this->command->getExtensionServiceProvider());
		$this->assertIsString($this->command->getExtensionDisplayName());
	}

	public function testInstallRequiredDependenciesReturnsFalseWithoutOption(): void
	{
		$method = $this->reflection->getMethod('installRequiredDependencies');
		$method->setAccessible(true);

		// Create a concrete implementation for testing
		$command = new class () extends ExtensionInstallCommand {
			protected $signature = 'test:command';

			protected $description = 'Test Command';

			private $hasOptionReturn = false;

			public function getExtensionServiceProvider(): string
			{
				return 'TestServiceProvider';
			}

			public function getExtensionDisplayName(): string
			{
				return 'TestExtension';
			}

			public function hasOption($key): bool
			{
				return $key === 'install_dependencies' ? false : parent::hasOption($key);
			}

			public function option($key = null): mixed
			{
				return null;
			}
		};

		$result = $method->invoke($command);

		$this->assertFalse($result);
	}

	public function testComposerAutoloadDoesNotRunInTests(): void
	{
		$method = $this->reflection->getMethod('composerAutoload');
		$method->setAccessible(true);

		// Should not throw exception in test environment
		$this->assertNull($method->invoke($this->command));
	}
}
