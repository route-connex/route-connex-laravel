<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Tests\Fixtures;

use RouteConnex\Laravel\Models\RouteConnexRun;

/**
 * A Run model with its own connection (the `tenant` SQLite connection), which
 * wins over the App's.
 */
final class TenantRouteConnexRun extends RouteConnexRun
{
    protected $connection = 'tenant';
}
