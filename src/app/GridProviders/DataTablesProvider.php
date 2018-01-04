<?php

declare(strict_types=1);

namespace MasterRO\Grid\GridProviders;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class DataTablesProvider extends Provider
{
	/**
	 * @return array
	 */
	public function options(): array
	{
		$requestData = $this->requestData->all();

		$search = array_get($requestData, 'search.value');
		$orderColumn = array_get($requestData, 'order.0.column');
		$orderDirection = array_get($requestData, 'order.0.dir');

		return compact('search', 'orderColumn', 'orderDirection');
	}


	/**
	 * @param Builder $query
	 *
	 * @return Collection
	 */
	public function results($query): Collection
	{
		$count = $query->count();
		$data = $query->skip($this->requestData->get('start', 0))
			->take($this->requestData->get('length', 10))
			->get();

		return collect([
			'recordsTotal'    => $count,
			'recordsFiltered' => $count,
			'data'            => $data,
		]);
	}
}
