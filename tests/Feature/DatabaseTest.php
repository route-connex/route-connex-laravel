<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
});

it('uses the database selected by DB_CONNECTION', function () {
    expect(DB::connection()->getDriverName())->toBe(getenv('DB_CONNECTION') ?: 'sqlite');
});

it('runs the package migrations', function () {
    expect(Schema::hasTable('route_connex_laravel_placeholder'))->toBeTrue();
});

it('writes and reads records on the migrated table', function () {
    DB::table('route_connex_laravel_placeholder')->insert(['name' => 'smoke']);

    expect(DB::table('route_connex_laravel_placeholder')->where('name', 'smoke')->count())->toBe(1);
});
