<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RouteConnex\Laravel\RouteConnex
 */
class RouteConnex extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \RouteConnex\Laravel\RouteConnex::class;
    }
}
