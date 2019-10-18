<?php

declare(strict_types=1);

namespace MasterRO\Grid\GridProviders;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class DataTablesProvider
 *
 * @package MasterRO\Grid\GridProviders
 */
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
