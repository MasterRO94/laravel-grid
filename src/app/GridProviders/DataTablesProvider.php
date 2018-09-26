<?php

declare(strict_types=1);

namespace MasterRO\Grid\GridProviders;

use Illuminate\Support\Arr;
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

		$search = Arr::get($requestData, 'search.value');
		$orderColumn = Arr::get($requestData, 'order.0.column');
		$orderDirection = Arr::get($requestData, 'order.0.dir');

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
		$skip = $this->requestData->get('start', 0);
		$length = $this->requestData->get('length', 10);

		$data = $query
			->when($length > 0, function ($query) use ($skip, $length) {
				return $query->skip($skip)->take($length);
			})
			->get();

		return collect([
			'recordsTotal'    => $count,
			'recordsFiltered' => $count,
			'data'            => $data,
		]);
	}
}
