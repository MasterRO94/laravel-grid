<?php

declare(strict_types=1);

namespace MasterRO\Grid\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use MasterRO\Grid\Core\Factory;

class GridController extends Controller
{
	/**
	 * @param $grid
	 *
	 * @return Collection
	 * @throws \Throwable
	 */
	public function items($grid)
	{
		return Factory::make($grid)->get();
	}
}
