<?php

declare(strict_types=1);

namespace LenderKit\Admin\Services\Builders;

use LenderKit\Admin\Grids\BaseGrid;
use Illuminate\Contracts\View\Factory;

/**
 * Class GridHeadRender
 *
 * @package LenderKit\Admin\Services\Builders
 */
class GridHeadRender
{
	/**
	 * View
	 *
	 * @var Factory
	 */
	protected $view;

	/**
	 * Template
	 *
	 * @var string
	 */
	protected $template;

	/**
	 * HeadBuilder constructor.
	 *
	 * @param Factory $view
	 * @param string $template
	 */
	public function __construct(Factory $view, string $template = 'grid::table._thead')
	{
		$this->view = $view;
		$this->template = $template;
	}


	/**
	 * Render
	 *
	 * @param string $grid
	 *
	 * @return string
	 */
	public function render(string $grid): string
	{
		$gridObject = GridFactory::make($grid);

		return $this->view->make($this->template, [
			'columns' => $gridObject->columns(),
		])->render();
	}
}
