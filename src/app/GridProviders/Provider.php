<?php

declare(strict_types=1);

namespace MasterRO\Grid\GridProviders;

use Illuminate\Support\Collection;

abstract class Provider
{
	/**
	 * @var Collection
	 */
	protected $requestData;


	/**
	 * Provider constructor.
	 *
	 * @param iterable $requestData
	 */
	public function __construct(iterable $requestData = [])
	{
		$this->requestData = collect($requestData);
	}


	/**
	 * @return array
	 */
	abstract public function options(): array;


	/**
	 * @param $query
	 *
	 * @return Collection
	 */
	abstract public function results($query): Collection;
}
