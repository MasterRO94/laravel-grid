<?php

declare(strict_types=1);

return [

	// Your grids base namespace
	'namespace' => 'App\\Grids',

	// Default grid provider, you may implement your own
	'provider'  => \MasterRO\Grid\GridProviders\DataTablesProvider::class,

	// Middleware for built in route
	'middleware' => ['web'],

];