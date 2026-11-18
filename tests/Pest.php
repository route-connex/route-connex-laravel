<?php

declare(strict_types=1);

use RouteConnex\Laravel\RouteConnexClientFactory;
use RouteConnex\Laravel\Tests\Fixtures\FakeApi;
use RouteConnex\Laravel\Tests\Fixtures\FakeClientFactory;
use RouteConnex\Laravel\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * Replace the RouteConnex API with a fake: every SDK client the package
 * builds from now on talks to it.
 */
function fakeRouteConnexApi(): FakeApi
{
    $api = new FakeApi;

    app()->instance(RouteConnexClientFactory::class, new FakeClientFactory($api));

    return $api;
}
