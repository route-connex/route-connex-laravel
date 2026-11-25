<?php

declare(strict_types=1);

use RouteConnex\Laravel\Apps\RouteConnexConfigApp;
use RouteConnex\Laravel\Contracts\RouteConnexAppContract;
use RouteConnex\Laravel\Exceptions\RouteConnexConfigurationException;
use RouteConnex\Laravel\Exceptions\RouteConnexException;
use RouteConnex\Laravel\Facades\RouteConnex;
use RouteConnex\Laravel\RouteConnexClient;
use RouteConnex\Laravel\RouteConnexClientFactory;
use RouteConnex\RouteConnexSdkPhp\ErClient;

/**
 * Reads a protected property of the SDK client, which has no getters for its
 * Credentials.
 */
function sdkProperty(ErClient $client, string $property): mixed
{
    return (new ReflectionProperty(ErClient::class, $property))->getValue($client);
}

beforeEach(function () {
    config()->set('route-connex-laravel.apps', [
        'default' => ['client_id' => 'default-id', 'client_secret' => 'default-secret'],
        'hpe' => ['client_id' => 'hpe-id', 'client_secret' => 'hpe-secret', 'base_url' => 'https://hpe.example.com/api'],
    ]);
});

it('builds the default App\'s SDK client from config', function () {
    $sdk = RouteConnex::sdk();

    expect(sdkProperty($sdk, 'clientId'))->toBe('default-id')
        ->and(sdkProperty($sdk, 'clientSecret'))->toBe('default-secret')
        ->and($sdk->getBaseUrl())->toBe('https://app.routeconnex.com/api');
});

it('uses the App named by the default config', function () {
    config()->set('route-connex-laravel.default', 'hpe');

    expect(sdkProperty(RouteConnex::sdk(), 'clientId'))->toBe('hpe-id');
});

it('returns the client of an App by name', function () {
    $client = RouteConnex::app('hpe');

    expect($client)->toBeInstanceOf(RouteConnexClient::class)
        ->and($client->getApp())->toEqual(new RouteConnexConfigApp('hpe', 'hpe-id', 'hpe-secret', 'https://hpe.example.com/api'))
        ->and(sdkProperty($client->sdk(), 'clientId'))->toBe('hpe-id');
});

it('implements the App contract for config Apps, keyed by name', function () {
    config()->set('route-connex-laravel.apps.hpe.callback_token', 'hpe-token');

    $app = RouteConnex::app('hpe')->getApp();

    expect($app)->toBeInstanceOf(RouteConnexAppContract::class)
        ->and($app->getRouteConnexKey())->toBe('hpe')
        ->and($app->getRouteConnexClientId())->toBe('hpe-id')
        ->and($app->getRouteConnexClientSecret())->toBe('hpe-secret')
        ->and($app->getRouteConnexCallbackToken())->toBe('hpe-token')
        ->and($app->getRouteConnexBaseUrl())->toBe('https://hpe.example.com/api');
});

it('has no Callback token when the config App\'s is empty', function (mixed $token) {
    config()->set('route-connex-laravel.apps.hpe.callback_token', $token);

    expect(RouteConnex::app('hpe')->getApp()->getRouteConnexCallbackToken())->toBeNull();
})->with(['null' => null, 'empty' => '']);

it('lets an App override the global base URL', function () {
    config()->set('route-connex-laravel.base_url', 'https://global.example.com/api');

    expect(RouteConnex::app('hpe')->sdk()->getBaseUrl())->toBe('https://hpe.example.com/api')
        ->and(RouteConnex::app('default')->sdk()->getBaseUrl())->toBe('https://global.example.com/api');
});

it('falls back to the global base URL when the App\'s is empty', function () {
    config()->set('route-connex-laravel.base_url', 'https://global.example.com/api');
    config()->set('route-connex-laravel.apps.hpe.base_url', '');

    expect(RouteConnex::app('hpe')->sdk()->getBaseUrl())->toBe('https://global.example.com/api');
});

it('falls back to the SDK\'s base URL when none is configured', function () {
    config()->set('route-connex-laravel.base_url', null);

    expect(RouteConnex::sdk()->getBaseUrl())->toBe((new ErClient('id', 'secret'))->getBaseUrl());
});

it('keeps one client per App', function () {
    expect(RouteConnex::app('hpe'))->toBe(RouteConnex::app('hpe'))
        ->and(RouteConnex::sdk())->toBe(RouteConnex::app('default')->sdk())
        ->and(RouteConnex::app('hpe'))->not->toBe(RouteConnex::app('default'));
});

it('builds SDK clients with the factory bound in the container', function () {
    $factory = new class extends RouteConnexClientFactory
    {
        /** @var list<string> */
        public array $built = [];

        protected function configure(ErClient $client, RouteConnexAppContract $app): ErClient
        {
            $this->built[] = (string) $app->getRouteConnexKey();

            return parent::configure($client, $app)->setBaseUrl('https://proxy.example.com/api');
        }
    };

    app()->instance(RouteConnexClientFactory::class, $factory);

    expect(RouteConnex::app('hpe')->sdk()->getBaseUrl())->toBe('https://proxy.example.com/api')
        ->and($factory->built)->toBe(['hpe']);
});

it('throws when no default App is configured', function (mixed $default) {
    config()->set('route-connex-laravel.default', $default);

    RouteConnex::sdk();
})->with(['null' => null, 'empty' => ''])
    ->throws(RouteConnexConfigurationException::class, 'No default RouteConnex App is configured.');

it('throws when the default App is not configured', function () {
    config()->set('route-connex-laravel.default', 'missing');

    RouteConnex::sdk();
})->throws(RouteConnexConfigurationException::class, 'RouteConnex App [missing] is not configured');

it('throws for an unknown App', function () {
    RouteConnex::app('missing');
})->throws(RouteConnexConfigurationException::class, 'RouteConnex App [missing] is not configured');

it('throws when an App\'s Credentials are empty', function (string $key, mixed $value) {
    config()->set("route-connex-laravel.apps.hpe.{$key}", $value);

    RouteConnex::app('hpe');
})->with([
    'no client ID' => ['client_id', null],
    'empty client ID' => ['client_id', ''],
    'no client secret' => ['client_secret', null],
    'empty client secret' => ['client_secret', ''],
])->throws(RouteConnexConfigurationException::class, 'RouteConnex App [hpe] has no client ID or client secret.');

it('throws configuration errors as RouteConnex exceptions', function () {
    expect(fn () => RouteConnex::app('missing'))->toThrow(RouteConnexException::class);
});

it('boots without Credentials', function () {
    config()->set('route-connex-laravel.apps.default', ['client_id' => null, 'client_secret' => null]);

    expect(app(\RouteConnex\Laravel\RouteConnex::class))->toBeInstanceOf(\RouteConnex\Laravel\RouteConnex::class);
});
