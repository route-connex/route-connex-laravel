<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \RouteConnex\Laravel\RouteConnexClient app(string|\RouteConnex\Laravel\Contracts\RouteConnexAppContract $app)
 * @method static \RouteConnex\Laravel\Endpoints\RouteConnexRouteConnexEndpoint routeConnex()
 * @method static \RouteConnex\Laravel\Endpoints\RouteConnexRunEndpoint run()
 * @method static \RouteConnex\Laravel\Endpoints\RouteConnexMicrosoftSharepointEndpoint microsoftSharepoint()
 * @method static \RouteConnex\Laravel\Endpoints\RouteConnexHpeContentManagerEndpoint hpeContentManager()
 * @method static \RouteConnex\RouteConnexSdkPhp\ErClient sdk()
 *
 * @see \RouteConnex\Laravel\RouteConnex
 */
class RouteConnex extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \RouteConnex\Laravel\RouteConnex::class;
    }
}
