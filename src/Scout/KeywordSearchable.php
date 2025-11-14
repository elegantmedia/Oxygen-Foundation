<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Scout;

interface KeywordSearchable
{
	public function getSearchableFields(): array;
}
