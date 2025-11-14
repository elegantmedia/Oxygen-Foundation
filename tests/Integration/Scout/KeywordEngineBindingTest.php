<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Integration\Scout;

use ElegantMedia\OxygenFoundation\Scout\KeywordSearchEngine;
use ElegantMedia\OxygenFoundation\Tests\TestCase;
use Laravel\Scout\EngineManager;

class KeywordEngineBindingTest extends TestCase
{
	public function testKeywordDriverResolvesToKeywordSearchEngine(): void
	{
		/** @var EngineManager $manager */
		$manager = app(EngineManager::class);
		$engine = $manager->driver('keyword');

		$this->assertInstanceOf(KeywordSearchEngine::class, $engine);
	}
}
