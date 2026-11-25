<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use RouteConnex\Laravel\RouteConnexServiceProvider;

beforeEach(function () {
    $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
});

it('uses the database selected by DB_CONNECTION', function () {
    expect(DB::connection()->getDriverName())->toBe(getenv('DB_CONNECTION') ?: 'sqlite');
});

it('runs the package migrations', function () {
    expect(Schema::hasTable('route_connex_apps'))->toBeTrue()
        ->and(Schema::hasTable('route_connex_runs'))->toBeTrue();
});

it('publishes the runs migration with the migrations tag', function () {
    $source = (string) realpath(__DIR__.'/../../database/migrations');

    $published = array_map('realpath', array_keys(ServiceProvider::pathsToPublish(RouteConnexServiceProvider::class, 'route-connex-laravel-migrations')));

    expect($published)->toContain($source)
        ->and(glob($source.'/*_create_route_connex_runs_table.php'))->toHaveCount(1);
});

it('writes and reads records on the migrated table', function () {
    DB::table('route_connex_apps')->insert([
        'name' => 'smoke',
        'client_id' => 'id',
        'client_secret' => 'secret',
        'callback_token' => 'token',
    ]);

    expect(DB::table('route_connex_apps')->where('name', 'smoke')->count())->toBe(1);
});

it('writes and reads Runs on the migrated table', function () {
    DB::table('route_connex_runs')->insert([
        'run_id' => 'run-1',
        'app_name' => 'default',
        'service' => 'POST microsoft-sharepoint/files',
        'status' => 'created',
    ]);

    expect(DB::table('route_connex_runs')->where('run_id', 'run-1')->count())->toBe(1);
});
