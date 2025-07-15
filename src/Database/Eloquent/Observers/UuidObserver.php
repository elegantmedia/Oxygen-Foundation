<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Database\Eloquent\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UuidObserver
{
    /**
     * Handle the model "creating" event.
     */
    public function creating(Model $model): void
    {
        if ($this->shouldAssignUuid($model)) {
            $model->setAttribute($this->getUuidColumn($model), (string) Str::uuid());
        }
    }

    /**
     * Determine if the model should receive a UUID.
     */
    protected function shouldAssignUuid(Model $model): bool
    {
        $column = $this->getUuidColumn($model);

        return empty($model->getAttribute($column)) &&
               in_array($column, $model->getFillable(), true);
    }

    /**
     * Get the UUID column name for the model.
     */
    protected function getUuidColumn(Model $model): string
    {
        if (property_exists($model, 'uuidColumn')) {
            return $model->uuidColumn;
        }

        return 'uuid';
    }
}
