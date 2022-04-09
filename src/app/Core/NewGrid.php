<?php

declare(strict_types=1);

namespace MasterRO\Grid\Core;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Class NewGrid
 *
 * @package MasterRO\Grid\Core
 */
abstract class NewGrid
{
    /**
     * List of columns to render
     *
     * @return array
     */
    abstract public static function columns(): array;

    /**
     * Query
     *
     * @return EloquentBuilder
     */
    abstract public function query();

    /**
     * @param $index
     *
     * @return mixed
     */
    public static function column($index)
    {
        $index = $index ?? -1;

        return Arr::get(static::columns(), $index);
    }

    /**
     * Custom Headers
     *
     * @return array
     */
    public function customHeaders(): array
    {
        return [];
    }

    /**
     * Headers
     *
     * @return array
     */
    public function headers(): array
    {
        return array_map(function (string $column) {
            return Arr::get($this->customHeaders(), $column, Str::title($column));
        }, static::columns());
    }
}
