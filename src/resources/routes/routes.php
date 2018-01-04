<?php

declare(strict_types=1);

Route::get('/grid/{grid}/items', '\\MasterRO\\Grid\\Controllers\\GridController@items')
	->middleware(config('grid.middleware'))
	->name('grid::items');