<?php

declare(strict_types=1);

use RouteConnex\Laravel\RouteConnex;

it('resolves the singleton', function () {
    expect(app(RouteConnex::class))->toBeInstanceOf(RouteConnex::class);
});

it('returns the same instance from the container', function () {
    expect(app(RouteConnex::class))->toBe(app(RouteConnex::class));
});

it('merges the package config', function () {
    expect(config('route-connex-laravel.placeholder'))->toBe('default');
});

it('loads the package translations', function () {
    expect(trans('route-connex-laravel::messages.placeholder'))->toBe('RouteConnex placeholder translation.');
});

it('loads the package views', function () {
    expect(view()->exists('route-connex-laravel::placeholder'))->toBeTrue();
});

it('registers the artisan command', function () {
    $this->artisan('route-connex-laravel:placeholder')
        ->expectsOutputToContain('RouteConnex placeholder command executed.')
        ->assertSuccessful();
});
