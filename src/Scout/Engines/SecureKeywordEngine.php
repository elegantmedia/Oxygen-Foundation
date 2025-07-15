<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Scout\Engines;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;

class SecureKeywordEngine extends Engine
{
    /**
     * Update the given model in the index.
     */
    public function update($models): void
    {
        // Not required for keyword search
    }

    /**
     * Remove the given model from the index.
     */
    public function delete($models): void
    {
        // Not required for keyword search
    }

    /**
     * Perform the given search on the engine.
     */
    public function search(Builder $builder): Collection
    {
        return $this->performSearch($builder)->get();
    }

    /**
     * Perform the given search on the engine.
     */
    public function paginate(Builder $builder, $perPage, $page)
    {
        return $this->performSearch($builder)->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Pluck and return the primary keys of the given results.
     */
    public function mapIds($results): \Illuminate\Support\Collection
    {
        return $results->modelKeys();
    }

    /**
     * Map the given results to instances of the given model.
     */
    public function map(Builder $builder, $results, $model): Collection
    {
        return $results;
    }

    /**
     * Map the given results to instances of the given model via a lazy collection.
     */
    public function lazyMap(Builder $builder, $results, $model): \Illuminate\Support\LazyCollection
    {
        return $results->lazy();
    }

    /**
     * Get the total count from a raw result returned by the engine.
     */
    public function getTotalCount($results): int
    {
        return $results->count();
    }

    /**
     * Flush all of the model's records from the engine.
     */
    public function flush($model): void
    {
        // Not required for keyword search
    }

    /**
     * Create a search index.
     */
    public function createIndex($name, array $options = []): mixed
    {
        // Not required for keyword search
        return null;
    }

    /**
     * Delete a search index.
     */
    public function deleteIndex($name): mixed
    {
        // Not required for keyword search
        return null;
    }

    /**
     * Perform the actual search query.
     */
    protected function performSearch(Builder $builder): \Illuminate\Database\Eloquent\Builder
    {
        $model = $builder->model;

        if (! method_exists($model, 'getSearchableFields')) {
            throw new \RuntimeException(
                'Model must implement getSearchableFields() method to use keyword search.'
            );
        }

        $searchableFields = $model->getSearchableFields();
        if (empty($searchableFields)) {
            throw new \RuntimeException('No searchable fields defined in the model.');
        }

        $query = $model::query();

        if (! empty($builder->query)) {
            $query->where(function ($q) use ($searchableFields, $builder) {
                $searchTerms = $this->prepareSearchTerms($builder->query);

                foreach ($searchableFields as $field) {
                    foreach ($searchTerms as $term) {
                        // Use parameter binding to prevent SQL injection
                        $q->orWhere($field, 'LIKE', $term);
                    }
                }
            });
        }

        // Apply where clauses
        foreach ($builder->wheres as $key => $value) {
            $query->where($key, $value);
        }

        // Apply order
        foreach ($builder->orders as $order) {
            $query->orderBy($order['column'], $order['direction']);
        }

        return $query;
    }

    /**
     * Prepare search terms with proper escaping.
     */
    protected function prepareSearchTerms(string $query): array
    {
        // Escape special characters for LIKE queries
        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $query);

        return [
            // Original search query with wildcards
            '%' . $escaped . '%',

            // Without spaces (for matching concatenated values)
            '%' . str_replace([' ', "\t", "\n", "\r"], '', $escaped) . '%',
        ];
    }
}
