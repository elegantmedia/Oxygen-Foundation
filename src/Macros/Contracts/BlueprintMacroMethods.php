<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Macros\Contracts;

/**
 * Describes the custom schema builder macros registered by OxygenFoundation
 * so that static analysis tools are aware of the fluent API surface.
 */
interface BlueprintMacroMethods
{
	public function prefix(string $prefix, string $field): string;

	public function location(string $prefix = ''): void;

	public function dropLocation(string $prefix = ''): void;

	public function place(string $prefix = ''): void;

	public function dropPlace(string $prefix = ''): void;

	public function file(string $prefix = ''): void;

	public function dropFile(string $prefix = ''): void;
}
