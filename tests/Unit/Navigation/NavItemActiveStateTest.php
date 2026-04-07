<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Unit\Navigation;

use ElegantMedia\OxygenFoundation\Navigation\NavItem;
use ElegantMedia\OxygenFoundation\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class NavItemActiveStateTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		$reflection = new \ReflectionClass($this->app);
		$property = $reflection->getProperty('isRunningInConsole');
		$property->setAccessible(true);
		$property->setValue($this->app, null);

		putenv('APP_RUNNING_IN_CONSOLE=false');
	}

	protected function tearDown(): void
	{
		putenv('APP_RUNNING_IN_CONSOLE');

		parent::tearDown();
	}

	/*
	|--------------------------------------------------------------------------
	| URL Path Matching Tests
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function exactUrlMatchIsActive(): void
	{
		$this->app['router']->get('/dashboard', function () {
			$item = new NavItem('Dashboard');
			$item->setUrl('/dashboard');

			return response()->json(['active' => $item->isActive()]);
		});

		$this->get('/dashboard')->assertJson(['active' => true]);
	}

	#[Test]
	public function urlNoMatchIsNotActive(): void
	{
		$this->app['router']->get('/dashboard', function () {
			$item = new NavItem('Settings');
			$item->setUrl('/settings');

			return response()->json(['active' => $item->isActive()]);
		});

		$this->get('/dashboard')->assertJson(['active' => false]);
	}

	#[Test]
	public function prefixMatchIsActive(): void
	{
		$this->app['router']->get('/dashboard/analytics', function () {
			$item = new NavItem('Dashboard');
			$item->setUrl('/dashboard');

			return response()->json(['active' => $item->isActive()]);
		});

		$this->get('/dashboard/analytics')->assertJson(['active' => true]);
	}

	#[Test]
	public function segmentAwareNoPartialWordMatch(): void
	{
		$this->app['router']->get('/user-settings', function () {
			$item = new NavItem('User');
			$item->setUrl('/user');

			return response()->json(['active' => $item->isActive()]);
		});

		$this->get('/user-settings')->assertJson(['active' => false]);
	}

	#[Test]
	public function segmentAwareNoPartialWordMatchPlural(): void
	{
		$this->app['router']->get('/username-check', function () {
			$item = new NavItem('Users');
			$item->setUrl('/users');

			return response()->json(['active' => $item->isActive()]);
		});

		$this->get('/username-check')->assertJson(['active' => false]);
	}

	#[Test]
	public function rootPathExactMatchIsActive(): void
	{
		$this->app['router']->get('/', function () {
			$item = new NavItem('Home');
			$item->setUrl('/');

			return response()->json(['active' => $item->isActive()]);
		});

		$this->get('/')->assertJson(['active' => true]);
	}

	#[Test]
	public function rootPathDoesNotPrefixMatchOtherPaths(): void
	{
		$this->app['router']->get('/dashboard', function () {
			$item = new NavItem('Home');
			$item->setUrl('/');

			return response()->json(['active' => $item->isActive()]);
		});

		$this->get('/dashboard')->assertJson(['active' => false]);
	}

	#[Test]
	public function trailingSlashIsNormalized(): void
	{
		$this->app['router']->get('/dashboard', function () {
			$item = new NavItem('Dashboard');
			$item->setUrl('/dashboard/');

			return response()->json(['active' => $item->isActive()]);
		});

		$this->get('/dashboard')->assertJson(['active' => true]);
	}

	#[Test]
	public function absoluteUrlExtractsPathForMatching(): void
	{
		$this->app['router']->get('/dashboard', function () {
			$item = new NavItem('Dashboard');
			$item->setUrl('http://example.com/dashboard');

			return response()->json(['active' => $item->isActive()]);
		});

		$this->get('/dashboard')->assertJson(['active' => true]);
	}

	/*
	|--------------------------------------------------------------------------
	| Route Name Matching Tests
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function exactRouteNameMatchIsActive(): void
	{
		$this->app['router']->get('/users', function () {
			$item = new NavItem('Users');
			$item->setResource('users.index');

			return response()->json(['active' => $item->isActive()]);
		})->name('users.index');

		$this->get('/users')->assertJson(['active' => true]);
	}

	#[Test]
	public function routeNameWildcardMatchesIndex(): void
	{
		$this->app['router']->get('/users', function () {
			$item = new NavItem('Users');
			$item->setResource('users');

			return response()->json(['active' => $item->isActive()]);
		})->name('users.index');

		$this->get('/users')->assertJson(['active' => true]);
	}

	#[Test]
	public function routeNameWildcardMatchesCreate(): void
	{
		$this->app['router']->get('/users/create', function () {
			$item = new NavItem('Users');
			$item->setResource('users');

			return response()->json(['active' => $item->isActive()]);
		})->name('users.create');

		$this->get('/users/create')->assertJson(['active' => true]);
	}

	#[Test]
	public function routeNameWildcardMatchesShow(): void
	{
		$this->app['router']->get('/users/1', function () {
			$item = new NavItem('Users');
			$item->setResource('users');

			return response()->json(['active' => $item->isActive()]);
		})->name('users.show');

		$this->get('/users/1')->assertJson(['active' => true]);
	}

	#[Test]
	public function routeNameWildcardMatchesEdit(): void
	{
		$this->app['router']->get('/users/1/edit', function () {
			$item = new NavItem('Users');
			$item->setResource('users');

			return response()->json(['active' => $item->isActive()]);
		})->name('users.edit');

		$this->get('/users/1/edit')->assertJson(['active' => true]);
	}

	#[Test]
	public function routeNameNoMatchIsNotActive(): void
	{
		$this->app['router']->get('/users', function () {
			$item = new NavItem('Products');
			$item->setResource('products');

			return response()->json(['active' => $item->isActive()]);
		})->name('users.index');

		$this->get('/users')->assertJson(['active' => false]);
	}

	/*
	|--------------------------------------------------------------------------
	| Child-Based Activation Tests
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function parentIsActiveWhenChildMatches(): void
	{
		$this->app['router']->get('/dashboard/analytics', function () {
			$parent = new NavItem('Parent');
			$parent->setUrl('/settings');

			$child = new NavItem('Child');
			$child->setUrl('/dashboard/analytics');

			$parent->addChild($child);

			return response()->json([
				'parent_active' => $parent->isActive(),
				'child_active' => $child->isActive(),
			]);
		});

		$this->get('/dashboard/analytics')
			->assertJson(['parent_active' => true, 'child_active' => true]);
	}

	#[Test]
	public function parentIsActiveWhenGrandchildMatches(): void
	{
		$this->app['router']->get('/reports/sales', function () {
			$grandparent = new NavItem('Grandparent');
			$grandparent->setUrl('/admin');

			$parent = new NavItem('Parent');
			$parent->setUrl('/dashboard');

			$child = new NavItem('Child');
			$child->setUrl('/reports/sales');

			$parent->addChild($child);
			$grandparent->addChild($parent);

			return response()->json([
				'grandparent_active' => $grandparent->isActive(),
				'parent_active' => $parent->isActive(),
				'child_active' => $child->isActive(),
			]);
		});

		$this->get('/reports/sales')
			->assertJson([
				'grandparent_active' => true,
				'parent_active' => true,
				'child_active' => true,
			]);
	}

	#[Test]
	public function parentWithOwnUrlIsActiveViaChild(): void
	{
		$this->app['router']->get('/dashboard/analytics', function () {
			$parent = new NavItem('Parent');
			$parent->setUrl('/dashboard');

			$child = new NavItem('Child');
			$child->setUrl('/dashboard/analytics');

			$parent->addChild($child);

			return response()->json([
				'parent_active' => $parent->isActive(),
				'child_active' => $child->isActive(),
			]);
		});

		$this->get('/dashboard/analytics')
			->assertJson(['parent_active' => true, 'child_active' => true]);
	}

	#[Test]
	public function parentNotActiveWhenNoChildMatches(): void
	{
		$this->app['router']->get('/dashboard', function () {
			$parent = new NavItem('Parent');
			$parent->setUrl('/settings');

			$child = new NavItem('Child');
			$child->setUrl('/reports');

			$parent->addChild($child);

			return response()->json([
				'parent_active' => $parent->isActive(),
				'child_active' => $child->isActive(),
			]);
		});

		$this->get('/dashboard')
			->assertJson(['parent_active' => false, 'child_active' => false]);
	}

	/*
	|--------------------------------------------------------------------------
	| Manual Override Tests
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function setActiveTrueOverridesUrlMismatch(): void
	{
		$this->app['router']->get('/dashboard', function () {
			$item = new NavItem('Settings');
			$item->setUrl('/settings');
			$item->setActive(true);

			return response()->json(['active' => $item->isActive()]);
		});

		$this->get('/dashboard')->assertJson(['active' => true]);
	}

	#[Test]
	public function setActiveFalseOverridesUrlMatch(): void
	{
		$this->app['router']->get('/dashboard', function () {
			$item = new NavItem('Dashboard');
			$item->setUrl('/dashboard');
			$item->setActive(false);

			return response()->json(['active' => $item->isActive()]);
		});

		$this->get('/dashboard')->assertJson(['active' => false]);
	}

	#[Test]
	public function setActiveWorksWithoutHttpContext(): void
	{
		$item = new NavItem('Test');
		$item->setActive(true);
		$this->assertTrue($item->isActive());

		$item->setActive(false);
		$this->assertFalse($item->isActive());
	}

	#[Test]
	public function setActiveFalseOverridesMatchingChildren(): void
	{
		$this->app['router']->get('/dashboard/analytics', function () {
			$parent = new NavItem('Parent');
			$parent->setUrl('/settings');
			$parent->setActive(false);

			$child = new NavItem('Child');
			$child->setUrl('/dashboard/analytics');

			$parent->addChild($child);

			return response()->json([
				'parent_active' => $parent->isActive(),
				'child_active' => $child->isActive(),
			]);
		});

		$this->get('/dashboard/analytics')
			->assertJson(['parent_active' => false, 'child_active' => true]);
	}

	/*
	|--------------------------------------------------------------------------
	| Edge Case Tests
	|--------------------------------------------------------------------------
	*/

	#[Test]
	public function navItemWithoutUrlOrResourceIsNotActive(): void
	{
		$this->app['router']->get('/dashboard', function () {
			$item = new NavItem('Test');

			return response()->json(['active' => $item->isActive()]);
		});

		$this->get('/dashboard')->assertJson(['active' => false]);
	}

	#[Test]
	public function invalidResourceWithValidUrlFallsBackToUrl(): void
	{
		$this->app['router']->get('/dashboard', function () {
			$item = new NavItem('Dashboard');
			$item->setResource('nonexistent.route');
			$item->setUrl('/dashboard');

			return response()->json(['active' => $item->isActive()]);
		});

		$this->get('/dashboard')->assertJson(['active' => true]);
	}

	#[Test]
	public function emptyChildrenCollectionNotActive(): void
	{
		$this->app['router']->get('/dashboard', function () {
			$item = new NavItem('Test');
			$item->setUrl('/settings');

			return response()->json([
				'active' => $item->isActive(),
				'has_children' => $item->hasChildren(),
			]);
		});

		$this->get('/dashboard')
			->assertJson(['active' => false, 'has_children' => false]);
	}

	#[Test]
	public function deeplyNestedChildActivatesAllAncestors(): void
	{
		$this->app['router']->get('/admin/reports/sales/monthly', function () {
			$level1 = new NavItem('Admin');
			$level1->setUrl('/admin');

			$level2 = new NavItem('Reports');
			$level2->setUrl('/admin/reports');

			$level3 = new NavItem('Sales');
			$level3->setUrl('/admin/reports/sales');

			$level4 = new NavItem('Monthly');
			$level4->setUrl('/admin/reports/sales/monthly');

			$level3->addChild($level4);
			$level2->addChild($level3);
			$level1->addChild($level2);

			return response()->json([
				'level1_active' => $level1->isActive(),
				'level2_active' => $level2->isActive(),
				'level3_active' => $level3->isActive(),
				'level4_active' => $level4->isActive(),
			]);
		});

		$this->get('/admin/reports/sales/monthly')
			->assertJson([
				'level1_active' => true,
				'level2_active' => true,
				'level3_active' => true,
				'level4_active' => true,
			]);
	}
}
