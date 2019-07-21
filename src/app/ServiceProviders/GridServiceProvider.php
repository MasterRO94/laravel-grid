<?php

declare(strict_types=1);

namespace MasterRO\Grid\ServiceProviders;

use Illuminate\Support\ServiceProvider;

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
		//
	}
}
