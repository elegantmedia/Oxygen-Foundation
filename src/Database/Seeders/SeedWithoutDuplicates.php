<?php


namespace ElegantMedia\OxygenFoundation\Database\Seeders;

use Illuminate\Support\Str;

trait SeedWithoutDuplicates
{

	protected function seedWithoutDuplicates(
		array $entityDataList,
		string $className,
		string $nameField = 'name',
		string $whereField = 'slug',
		bool $isSlug = true
	): void {
		foreach ($entityDataList as $entityData) {
			if (is_array($entityData)) {
				$entityName = $entityData[$nameField];
			} else {
				$entityName = $entityData;
			}

			if ($isSlug) {
				$whereValue = Str::slug($entityName);
			} else {
				$whereValue = $entityData[$nameField];
			}

			$entityModel = app($className);
			$existingEntity = $entityModel->where($whereField, $whereValue)->first();

			if (!$existingEntity) {
				if (!is_array($entityData)) {
					$entityData = [$nameField => $entityData];
				}
				$entityModel->create($entityData);
			}
		}
	}
}
