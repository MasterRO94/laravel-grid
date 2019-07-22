<?php

declare(strict_types=1);

namespace MasterRO\Grid\Builders;

use LenderKit\Models\Model;

/**
 * Class Column
 *
 * @package MasterRO\Grid\Builders
 */
class Column
{
	/**
	 * Name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Title
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Orderable
	 *
	 * @var bool
	 */
	public $orderable = true;

	/**
	 * Render
	 *
	 * @var callable
	 */
	public $cellClosure;

	/**
	 * Sorting Closure
	 *
	 * @var callable
	 */
	public $sortingClosure;

	/**
	 * After
	 *
	 * @var string
	 */
	public $before;

	/**
	 * Column constructor.
	 *
	 * @param string $name
	 */
	public function __construct(string $name)
	{
		$this->name = $name;
	}

	/**
	 * Make
	 *
	 * @param string $name
	 *
	 * @return Column
	 */
	public static function make(string $name): self
	{
		return new static($name);
	}

	/**
	 * Title
	 *
	 * @param string $title
	 *
	 * @return Column
	 */
	public function title(string $title): self
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * Orderable
	 *
	 * @param bool $flag
	 *
	 * @return Column
	 */
	public function orderable(bool $flag = true): self
	{
		$this->orderable = $flag;

		return $this;
	}

	/**
	 * Before
	 *
	 * @param string $column
	 *
	 * @return $this
	 */
	public function before(string $column)
	{
		$this->before = $column;

		return $this;
	}

	/**
	 * Set Render
	 *
	 * @param callable $closure
	 *
	 * @return $this
	 */
	public function setCellClosure(callable $closure)
	{
		$this->cellClosure = $closure;

		return $this;
	}

	/**
	 * Set Sorting Closure
	 *
	 * @param callable $closure
	 *
	 * @return $this
	 */
	public function setSortingClosure(callable $closure)
	{
		$this->sortingClosure = $closure;

		return $this;
	}

	/**
	 * Render Column
	 *
	 * @param Model $entity
	 *
	 * @return mixed
	 */
	public function renderCell(Model $entity)
	{
		$closure = $this->cellClosure;

		return $closure($entity);
	}

	/**
	 * Apply Sorting
	 *
	 * @param $query
	 * @param $orderDirection
	 */
	public function applySorting($query, $orderDirection): void
	{
		$closure = $this->sortingClosure;

		if (is_callable($closure)) {
			$closure($this, $query, $orderDirection);
		}
	}
}
