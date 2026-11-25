<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Tests\Fixtures;

use MongoDB\Laravel\Eloquent\DocumentModel;
use RouteConnex\Laravel\Models\RouteConnexRun;

/**
 * The subclass the docs tell MongoDB apps to write and set in `models.run`.
 * `TestCase` sets it when the tests run on MongoDB.
 */
final class MongoRouteConnexRun extends RouteConnexRun
{
    use DocumentModel;
}
