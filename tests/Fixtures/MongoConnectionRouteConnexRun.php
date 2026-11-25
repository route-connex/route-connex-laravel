<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Tests\Fixtures;

use MongoDB\Laravel\Eloquent\DocumentModel;
use RouteConnex\Laravel\Models\RouteConnexRun;

/**
 * The MongoDB subclass the docs show for a split setup: Runs on MongoDB,
 * Apps on an SQL connection. It must set its connection.
 */
final class MongoConnectionRouteConnexRun extends RouteConnexRun
{
    use DocumentModel;

    protected $connection = 'mongodb';
}
