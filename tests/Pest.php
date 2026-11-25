<?php

declare(strict_types=1);

use RouteConnex\Laravel\Models\RouteConnexRun;
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

/**
 * Run the package migrations on the extra `tenant` SQLite connection, for
 * Apps or Runs stored away from the default connection.
 */
function migrateTenantConnection(): void
{
    test()->artisan('migrate', [
        '--database' => 'tenant',
        '--path' => realpath(__DIR__.'/../database/migrations'),
        '--realpath' => true,
    ])->assertSuccessful();
}

/**
 * The configured Run model class: `RouteConnexRun`, or its MongoDB subclass
 * when the tests run on MongoDB.
 *
 * @return class-string<RouteConnexRun>
 */
function runModel(): string
{
    /** @var class-string<RouteConnexRun> */
    return config('route-connex-laravel.models.run');
}
