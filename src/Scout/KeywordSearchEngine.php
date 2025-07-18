<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Scout;

use Laravel\Scout\Builder;

class KeywordSearchEngine extends \Laravel\Scout\Engines\Engine
{
	/**
	 * Update the given model in the index.
	 */
	public function update($models)
	{
		// Not required
	}

	/**
	 * Remove the given model from the index.
	 */
	public function delete($models)
	{
		// Not required
	}

	/**
	 * {@inheritDoc}
	 */
	public function search(Builder $builder)
	{
		$query = $this->performSearch($builder);

		return $query->get();
	}

	protected function performSearch(Builder $builder, array $options = [])
	{
		$model = $builder->model;

		if (! method_exists($model, 'getSearchableFields')) {
			throw new \Exception('searchable property not defined.');
		}

		$query = $model->newQuery()->where(function ($q) use ($model, $builder) {
			// build a few common variations of the terms
			$searchTerms = [
				// the search query as it's given
				$builder->query,

				// without all spaces, tabs and line endings
				preg_replace('/\s+/', '', $builder->query),
			];

			// loop through all columns
			foreach ($model->getSearchableFields() as $columnName) {
				foreach ($searchTerms as $searchTerm) {
					$q->orWhere($columnName, 'LIKE', '%' . $searchTerm . '%');
				}
			}
		});

		return $query;
	}

	/**
	 * {@inheritDoc}
	 */
	public function paginate(Builder $builder, $perPage, $page)
	{
		$query = $this->performSearch($builder);

		return $query->paginate();
	}

	/**
	 * Pluck and return the primary keys of the given results.
	 */
	public function mapIds($results)
	{
		return $results->map(function ($result) {
			return $result->getKey();
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function map(Builder $builder, $results, $model)
	{
		return $results;
	}

	/**
	 * Map the given results to instances of the given model.
	 */
	public function getTotalCount($results)
	{
		return $results->count();
	}

	/**
	 * Flush all of the model's records from the engine.
	 */
	public function flush($model)
	{
		// Not required
	}

	public function lazyMap(Builder $builder, $results, $model)
	{
		return $results->lazy();
	}

	public function createIndex($name, array $options = [])
	{
		// TODO: Implement createIndex() method.
	}

	public function deleteIndex($name)
	{
		// TODO: Implement deleteIndex() method.
	}
}
