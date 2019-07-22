<?php

declare(strict_types=1);

namespace MasterRO\Grid\Html;

use Illuminate\Contracts\View\Factory;
use MasterRO\Grid\Core\Factory as GridFactory;

/**
 * Class GridHeadRender
 *
 * @package MasterRO\Grid\Html
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
