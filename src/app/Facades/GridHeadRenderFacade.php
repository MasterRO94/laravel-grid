<?php

declare(strict_types=1);

namespace MasterRO\Grid\Facades;

use Illuminate\Support\Facades\Facade;
use MasterRO\Grid\Html\GridHeadRender;

/**
 * Class GridHeadRenderFacade
 *
 * @package MasterRO\Grid\Facades
 */
class GridHeadRenderFacade extends Facade
{
	/**
	 * Get Facade Accessor
	 *
	 * @return string
	 */
	public static function getFacadeAccessor()
	{
		return GridHeadRender::class;
	}
}
