<?php

declare(strict_types=1);

namespace MasterRO\Grid\Controllers;

use Illuminate\Routing\Controller;

class GridController extends Controller
{
	/**
	 * @param $grid
	 *
	 * @return mixed
	 * @throws \Illuminate\Container\EntryNotFoundException
	 */
	public function items($grid)
	{
		$pieces = explode('.', $grid);
		$grid = implode('\\', array_map('studly_case', $pieces));

		$gridClass = config('grid.namespace') . "\\{$grid}";

		abort_unless(class_exists($gridClass), 404);

		return app($gridClass)->get();
	}
}
