<?php

declare(strict_types=1);

namespace MasterRO\Grid\ServiceProviders;

use Illuminate\Foundation\AliasLoader;
use MasterRO\Grid\Html\GridHeadRender;
use Illuminate\Support\ServiceProvider;
use MasterRO\Grid\Facades\GridHeadRenderFacade;

/**
 * Class GridServiceProvider
 *
 * @package MasterRO\Grid\ServiceProviders
 */
class GridServiceProvider extends ServiceProvider
{
	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/../../resources/config/grid.php' => config_path('grid.php'),
		]);

		$this->loadViewsFrom(__DIR__ . '/../../resources/views', 'grid');

		$this->mergeConfigFrom(
			__DIR__ . '/../../resources/config/grid.php', 'grid'
		);

		$this->loadRoutesFrom(__DIR__ . '/../../resources/routes/routes.php');
	}


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton(GridHeadRender::class);

		$loader = AliasLoader::getInstance();
		$loader->alias('GridHeadRenderFacade', GridHeadRenderFacade::class);
	}
}
