<?php

declare(strict_types=1);

namespace MasterRO\Grid\Core;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Illuminate\Support\Collection;
use MasterRO\Grid\GridProviders\Provider;
use MasterRO\Grid\GridProviders\DataTablesProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Class Grid
 *
 * @package MasterRO\Grid\Core
 */
abstract class Grid
{
	/**
	 * @var string
	 */
	protected $provider;

	/**
	 * @var QueryBuilder|EloquentBuilder|Relation
	 */
	protected $query;

	/**
	 * @var Collection
	 */
	protected $requestData;

	/**
	 * @var string|null
	 */
	protected $search;

	/**
	 * @var string|null
	 */
	protected $orderColumn;

	/**
	 * @var string|null
	 */
	protected $orderDirection;

	/**
	 * @var string
	 */
	protected $tableName;

	/**
	 * @var Provider
	 */
	private $resolvedProvider;

	/**
	 * Iterable Provider Keys
	 * Needed for transformation
	 *
	 * @var array
	 */
	protected $iterableProviderKeys = [
		DataTablesProvider::class => 'data',
	];

	/**
	 * Grid constructor.
	 *
	 * @param iterable $requestData
	 *
	 * @throws \Throwable
	 */
	public function __construct(array $requestData = [])
	{
		$requestData = collect($requestData);

		$this->query = $this->query();
		$this->tableName = $this->query->getModel()->getTable();
		$this->requestData = $requestData->isEmpty() ? collect(request()->all()) : $requestData;
		$this->provider = $this->provider ?? config('grid.provider', DataTablesProvider::class);
		$this->setOptions();
	}

	/**
	 * @return array
	 */
	abstract public static function columns(): array;

	/**
	 * Query
	 *
	 * @return EloquentBuilder
	 */
	abstract public function query();

	/**
	 * @param $index
	 *
	 * @return mixed
	 */
	public static function column($index)
	{
		$index = $index ?? -1;

		return Arr::get(static::columns(), $index);
	}

	/**
	 * @param Collection $items
	 *
	 * @return Collection
	 */
	public function transform(Collection $items): Collection
	{
		if ($providerDataKey = Arr::get($this->iterableProviderKeys, $this->provider)) {
			$entities = $items->get($providerDataKey);

			return $items->put($providerDataKey, $entities->map(function ($entity) {
				$columns = [];
				foreach (static::columns() as $column) {
					if (method_exists($this, $method = camel_case($column) . 'Column')) {
						$columns[] = call_user_func([$this, $method], $entity);
					} elseif ($entity->{$column} && $entity->{$column} instanceof Carbon) {
						$columns[] = method_exists($this, 'formatDate')
							? $this->formatDate($entity->{$column})
							: $entity->{$column}->toFormattedDateString();
					} else {
						$columns[] = $entity->{$column};
					}
				}

				return $columns;
			})->values());
		}

		return $items;
	}

	/**
	 * @param bool $withFilters
	 *
	 * @return QueryBuilder|EloquentBuilder|Relation
	 */
	public function getQuery($withFilters = true)
	{
		return $withFilters
			? $this->applyFilters()->orderBy()->query
			: $this->query;
	}

	/**
	 * @return Collection
	 * @throws \Throwable
	 */
	public function get(): Collection
	{
		return $this->transform(
			$this->provider()->results(
				$this->getQuery()
			)
		);
	}

	/**
	 * Add query filters
	 *
	 * @return Grid
	 */
	protected function applyFilters(): Grid
	{
		return $this;
	}

	/**
	 * Add query ordering
	 *
	 * @return Grid
	 */
	protected function orderBy(): Grid
	{
		$this->query->orderBy("{$this->tableName}.{$this->orderColumn}", $this->orderDirection);

		return $this;
	}

	/**
	 * @return Grid
	 * @throws \Throwable
	 */
	protected function setOptions(): Grid
	{
		$options = $this->provider()->options();

		$this->search = Arr::get($options, 'search');
		$this->orderColumn = $this->column(Arr::get($options, 'orderColumn'));
		$this->orderDirection = Arr::get($options, 'orderDirection');

		return $this;
	}

	/**
	 * @return \Illuminate\Foundation\Application|mixed
	 * @throws \Throwable
	 */
	protected function provider(): Provider
	{
		if (! $this->resolvedProvider) {
			throw_unless(
				class_exists($this->provider),
				new InvalidArgumentException("Provider [{$this->provider}] is not supported.")
			);

			$this->resolvedProvider = app($this->provider, ['requestData' => $this->requestData]);
		}

		return $this->resolvedProvider;
	}
}
