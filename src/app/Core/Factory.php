<?php

declare(strict_types=1);

namespace MasterRO\Grid\Core;

use Illuminate\Support\Str;

/**
 * Class Factory
 *
 * @package MasterRO\Grid\Core
 */
class Factory
{
	/**
	 * @param string $grid
	 *
	 * @return Grid
	 */
	public static function make(string $grid): Grid
	{
		if (Str::startsWith($grid, '.')) {
			$grid = ltrim($grid, '.');
			$namespace = '';
		}

		$pieces = explode('.', $grid);
		$grid = implode('\\', array_map('studly_case', $pieces));

		$gridClass = (isset($namespace) ? $namespace : config('grid.namespace')) . "\\{$grid}";

		abort_unless(class_exists($gridClass), 404);

		return app($gridClass);
	}
}
