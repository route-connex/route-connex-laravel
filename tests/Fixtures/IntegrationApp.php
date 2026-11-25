<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use RouteConnex\Laravel\Concerns\InteractsWithRouteConnexApp;
use RouteConnex\Laravel\Contracts\RouteConnexAppContract;

/**
 * A consumer app's own model implementing the contract with the trait. The
 * tests never save it, so it needs no table.
 */
class IntegrationApp extends Model implements RouteConnexAppContract
{
    use InteractsWithRouteConnexApp;

    protected $guarded = [];
}
