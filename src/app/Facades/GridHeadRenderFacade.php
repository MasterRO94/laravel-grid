<?php

declare(strict_types=1);

namespace MasterRO\Grid\Facades;

use Illuminate\Support\Facades\Facade;
use LenderKit\Admin\Services\Builders\GridHeadRender;

/**
 * Class HeadBuilderFacade
 *
 * @package LenderKit\Admin\Facades
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
