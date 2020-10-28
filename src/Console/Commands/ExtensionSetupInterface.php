<?php


namespace ElegantMedia\OxygenFoundation\Console\Commands;

interface ExtensionSetupInterface
{

	public function getExtensionServiceProvider() : string;

	public function getExtensionDisplayName() : string;

}
