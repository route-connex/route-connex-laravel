<?php

declare(strict_types=1);

namespace RouteConnex\Laravel;

use Illuminate\Support\ServiceProvider;
use RouteConnex\Laravel\Console\Commands\RouteConnexCommand;

class RouteConnexServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/route-connex-laravel.php', 'route-connex-laravel');

        $this->app->singleton(RouteConnex::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/route-connex-laravel.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'route-connex-laravel');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'route-connex-laravel');

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/route-connex-laravel.php' => config_path('route-connex-laravel.php'),
        ], ['route-connex-laravel', 'route-connex-laravel-config']);

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/route-connex-laravel'),
        ], ['route-connex-laravel', 'route-connex-laravel-views']);

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/route-connex-laravel'),
        ], ['route-connex-laravel', 'route-connex-laravel-lang']);

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/route-connex-laravel'),
        ], ['route-connex-laravel', 'route-connex-laravel-assets']);

        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], ['route-connex-laravel', 'route-connex-laravel-migrations']);

        $this->commands([
            RouteConnexCommand::class,
        ]);
    }
}
