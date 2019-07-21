<?php

declare(strict_types=1);

namespace MasterRO\Grid\Core;

/**
 * Class ResourceExtender
 *
 * @package LenderKit\Api\V1\Http\Resources
 */
abstract class GridExtender
{
	/**
	 * Columns
	 *
	 * @return array
	 */
	public function columns(): array
	{
		return [];
	}


	/**
	 * Remove Columns
	 *
	 * @return array
	 */
	public function removeColumns(): array
	{
		return [];
	}
}
