<?php

declare(strict_types=1);

namespace MasterRO\Grid\Core;

use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Illuminate\Support\Collection;
use MasterRO\Grid\Builders\Column;
use MasterRO\Grid\GridProviders\Provider;
use MasterRO\Grid\GridProviders\DataTablesProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

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
	 * Extenders
	 *
	 * @var
	 */
	protected static $extenders;

	/**
	 * Extender Instances
	 *
	 * @var Collection
	 */
	protected $extenderInstances;

	/**
	 * Columns
	 *
	 * @var Collection
	 */
	protected $columns;


	/**
	 * Grid constructor.
	 *
	 * @param QueryBuilder|EloquentBuilder|Relation|null $query
	 * @param iterable $requestData
	 *
	 * @throws \Throwable
	 */
	public function __construct($query, iterable $requestData = [])
	{
		$requestData = collect($requestData);

		$this->query = $query;
		$this->tableName = $query->getModel()->getTable();
		$this->requestData = $requestData->isEmpty() ? collect(request()->all()) : $requestData;
		$this->provider = $this->provider ?? config('grid.provider', DataTablesProvider::class);
		$this->setOptions();

		$this->extenderInstances = collect();

		$extenders = isset(static::$extenders[static::class]) ? static::$extenders[static::class] : [];

		foreach ($extenders as $extender) {
			$this->extenderInstances->add(app($extender));
		}

		$this->setColumns();
	}

	/**
	 * Extend
	 *
	 * @param string $extender
	 */
	public static function extend(string $extender)
	{
		static::$extenders[static::class][] = $extender;
	}

	/**
	 * Get Extenders
	 *
	 * @return Collection|GridExtender[]
	 */
	public function getExtenders(): Collection
	{
		return $this->extenderInstances;
	}


	/**
	 * @return array
	 */
	abstract public function initColumns(): void;

	/**
	 * Columns
	 *
	 * @return array
	 */
	public function columns(): array
	{
		return $this->columns ? $this->columns->toArray() : [];
	}


	/**
	 * @param $index
	 *
	 * @return mixed
	 */
	public function column($index)
	{
		$index = $index ?? -1;

		return array_get($this->columns(), $index);
	}

	/**
	 * Set Columns
	 */
	public function setColumns(): void
	{
		$this->initColumns();

		$this->addColumnsByExtender();

		$this->removeColumnsByExtender();
	}

	/**
	 * Remove Columns By Extender
	 */
	protected function removeColumnsByExtender(): void
	{
		$this->getExtenders()->map(function (GridExtender $extender) {
			foreach ($extender->removeColumns() as $column) {
				/* @var Column $column */
				$this->columns = $this->columns->filter(function (Column $item, $key) use ($column) {
					return $item->name != $column->name;
				});
			}
		});

		$this->columns = $this->columns->values();
	}

	/**
	 * Add Columns By Extender
	 */
	protected function addColumnsByExtender(): void
	{
		$this->getExtenders()->map(function (GridExtender $extender) {
			foreach ($extender->columns() as $column) {
				/* @var Column $column */
				if ($afterColumn = $column->before) {
					$index = $this->columns->search(function (Column $column, $key) use ($afterColumn) {
						return $column->name == $afterColumn;
					});

					$this->columns->splice($index, 0, [$column]);
				} else {
					$this->columns->add($column);
				}
			}
		});
	}


	/**
	 * Transform the results
	 *
	 * @param Collection $items
	 *
	 * @return Collection
	 */
	public function transform(Collection $items): Collection
	{
		$entities = $items->get('data');

		return $items->put('data', $entities->map(function (Model $entity) {
			$columns = [];
			foreach ($this->columns() as $column) {
				$columnObject = $column;
				/* @var Column $columnObject */
				$column = $column instanceof Column ? $column->name : $column;

				if ($columnObject instanceof Column && is_callable($columnObject->cellClosure)) {
					$columns[] = $columnObject->renderCell($entity);
				} elseif (method_exists($this, $method = camel_case($column) . 'Column')) {
					$columns[] = call_user_func([$this, $method], $entity);
				} elseif ($entity->{$column} && $entity->{$column} instanceof Carbon) {
					$columns[] = $this->columnWithDateFromCarbon($entity->{$column});
				} elseif ($entity->{$column} && $entity->{$column} instanceof Money) {
					$columns[] = (string)$entity->{$column};
				} else {
					$columns[] = $entity->{$column};
				}
			}

			return $columns;
		})->values());
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

		$this->search = array_get($options, 'search');
		$this->orderColumn = $this->column(array_get($options, 'orderColumn'));
		$this->orderDirection = array_get($options, 'orderDirection');

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
