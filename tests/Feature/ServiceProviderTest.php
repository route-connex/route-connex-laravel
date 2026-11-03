<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use RouteConnex\Laravel\Facades\RouteConnex as RouteConnexFacade;
use RouteConnex\Laravel\RouteConnex;
use RouteConnex\Laravel\RouteConnexServiceProvider;

it('resolves the facade to the container singleton', function () {
    expect(RouteConnexFacade::getFacadeRoot())->toBe(app(RouteConnex::class));
});

dataset('publish tags', function () {
    $root = dirname(__DIR__, 2);

    return [
        'config' => ['route-connex-laravel-config', "{$root}/config/route-connex-laravel.php", fn () => config_path('route-connex-laravel.php')],
        'views' => ['route-connex-laravel-views', "{$root}/resources/views", fn () => resource_path('views/vendor/route-connex-laravel')],
        'lang' => ['route-connex-laravel-lang', "{$root}/lang", fn () => lang_path('vendor/route-connex-laravel')],
        'assets' => ['route-connex-laravel-assets', "{$root}/public", fn () => public_path('vendor/route-connex-laravel')],
        'migrations' => ['route-connex-laravel-migrations', "{$root}/database/migrations", fn () => database_path('migrations')],
    ];
});

/**
 * @return array<string, string>
 */
function publishedPaths(?string $tag): array
{
    $paths = [];

    foreach (ServiceProvider::pathsToPublish(RouteConnexServiceProvider::class, $tag) as $from => $to) {
        $paths[(string) realpath($from)] = $to;
    }

    return $paths;
}

it('publishes each resource to its target path', function (string $tag, string $source, Closure $target) {
    expect(publishedPaths($tag))->toBe([(string) realpath($source) => $target()]);
})->with('publish tags');

it('includes every resource in the group tag', function (string $tag, string $source, Closure $target) {
    expect(publishedPaths('route-connex-laravel'))
        ->toHaveKey((string) realpath($source), $target());
})->with('publish tags');

it('publishes the config with vendor:publish', function () {
    $target = config_path('route-connex-laravel.php');

    File::delete($target);

    try {
        $this->artisan('vendor:publish', ['--tag' => 'route-connex-laravel-config'])->assertSuccessful();

        expect(File::exists($target))->toBeTrue()
            ->and(File::get($target))->toBe(File::get(dirname(__DIR__, 2).'/config/route-connex-laravel.php'));
    } finally {
        File::delete($target);
    }
});
