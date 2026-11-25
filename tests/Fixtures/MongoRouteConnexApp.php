<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Tests\Fixtures;

use MongoDB\Laravel\Eloquent\DocumentModel;
use RouteConnex\Laravel\Models\RouteConnexApp;

/**
 * The subclass the docs tell MongoDB apps to write and set in `models.app`.
 * `TestCase` sets it when the tests run on MongoDB.
 */
final class MongoRouteConnexApp extends RouteConnexApp
{
    use DocumentModel;
}
