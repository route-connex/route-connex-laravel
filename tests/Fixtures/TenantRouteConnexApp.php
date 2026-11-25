<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Tests\Fixtures;

use RouteConnex\Laravel\Models\RouteConnexApp;

/**
 * An App model on its own SQLite connection (`tenant`), whatever database
 * the tests run on, so Runs and Apps can live on different connections.
 */
final class TenantRouteConnexApp extends RouteConnexApp
{
    protected $connection = 'tenant';
}
