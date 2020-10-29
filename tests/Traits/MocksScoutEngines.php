<?php

namespace Tests\Traits;

use ElegantMedia\OxygenFoundation\Scout\KeywordSearchEngine;
use Laravel\Scout\EngineManager;
use Mockery\MockInterface;

trait MocksScoutEngines
{

	protected function mockScoutKeywordEngine(): MockInterface
	{
		$engineMock = mock(app(KeywordSearchEngine::class))->makePartial()->shouldIgnoreMissing();

		$managerMock = mock(EngineManager::class)->makePartial()->shouldIgnoreMissing();

		$managerMock->shouldReceive('driver')->andReturn($engineMock);

		$this->swap(EngineManager::class, $managerMock);

		return $engineMock;
	}

}
