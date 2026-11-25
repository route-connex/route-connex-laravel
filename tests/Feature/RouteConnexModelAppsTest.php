<?php

declare(strict_types=1);

use RouteConnex\Laravel\Contracts\RouteConnexAppContract;
use RouteConnex\Laravel\Exceptions\RouteConnexConfigurationException;
use RouteConnex\Laravel\Facades\RouteConnex;
use RouteConnex\Laravel\RouteConnexClientFactory;
use RouteConnex\Laravel\Tests\Fixtures\FakeApi;
use RouteConnex\Laravel\Tests\Fixtures\IntegrationApp;
use RouteConnex\Laravel\Tests\Fixtures\PlainApp;
use RouteConnex\Laravel\Tests\Fixtures\SettingsIntegrationApp;
use RouteConnex\RouteConnexSdkPhp\ErClient;

/**
 * An App model as loaded from a database: the key is set, nothing is saved.
 *
 * @param  array<string, mixed>  $attributes
 */
function integrationApp(array $attributes = [], int|string|null $key = 1): IntegrationApp
{
    $app = (new IntegrationApp)->forceFill(array_merge([
        'client_id' => 'model-id',
        'client_secret' => 'model-secret',
        'callback_token' => 'model-token',
    ], $attributes));

    if ($key !== null) {
        $app->forceFill(['id' => $key]);
    }

    return $app;
}

function sdkCredential(ErClient $client, string $property): mixed
{
    return (new ReflectionProperty(ErClient::class, $property))->getValue($client);
}

it('builds the SDK client from the model\'s Credentials', function () {
    $sdk = RouteConnex::app(integrationApp())->sdk();

    expect(sdkCredential($sdk, 'clientId'))->toBe('model-id')
        ->and(sdkCredential($sdk, 'clientSecret'))->toBe('model-secret');
});

it('reads the contract from the model\'s attributes', function () {
    $app = integrationApp(['base_url' => 'https://model.example.com/api'], key: 7);

    expect($app->getRouteConnexKey())->toBe(7)
        ->and($app->getRouteConnexClientId())->toBe('model-id')
        ->and($app->getRouteConnexClientSecret())->toBe('model-secret')
        ->and($app->getRouteConnexCallbackToken())->toBe('model-token')
        ->and($app->getRouteConnexBaseUrl())->toBe('https://model.example.com/api');
});

it('treats empty attributes as unset', function () {
    $app = integrationApp(['callback_token' => '', 'base_url' => '']);

    expect($app->getRouteConnexCallbackToken())->toBeNull()
        ->and($app->getRouteConnexBaseUrl())->toBeNull();
});

it('uses a trait method the model overrides', function () {
    $app = (new SettingsIntegrationApp)->forceFill([
        'id' => 1,
        'settings' => ['route_connex_client_id' => 'settings-id'],
        'client_secret' => 'model-secret',
    ]);

    expect(sdkCredential(RouteConnex::app($app)->sdk(), 'clientId'))->toBe('settings-id');
});

it('uses the model\'s base URL, then the global one, then the SDK\'s', function () {
    config()->set('route-connex-laravel.base_url', 'https://global.example.com/api');

    expect(RouteConnex::app(integrationApp(['base_url' => 'https://model.example.com/api']))->sdk()->getBaseUrl())
        ->toBe('https://model.example.com/api')
        ->and(RouteConnex::app(integrationApp(['base_url' => null], key: 2))->sdk()->getBaseUrl())
        ->toBe('https://global.example.com/api');

    config()->set('route-connex-laravel.base_url', null);

    expect(RouteConnex::app(integrationApp(key: 3))->sdk()->getBaseUrl())
        ->toBe((new ErClient('id', 'secret'))->getBaseUrl());
});

it('binds the client to the App passed in', function () {
    $app = integrationApp();
    $sameApp = integrationApp();

    expect(RouteConnex::app($app)->getApp())->toBe($app)
        ->and(RouteConnex::app($sameApp)->getApp())->toBe($sameApp);
});

it('reuses the SDK client for the same App', function () {
    expect(RouteConnex::app(integrationApp())->sdk())->toBe(RouteConnex::app(integrationApp())->sdk());
});

it('never shares an SDK client between Apps with the same key and other Credentials', function () {
    // Two Tenants' databases: same model class and key, different Credentials.
    $tenantA = integrationApp(['client_id' => 'tenant-a-id', 'client_secret' => 'tenant-a-secret']);
    $tenantB = integrationApp(['client_id' => 'tenant-b-id', 'client_secret' => 'tenant-b-secret']);

    expect(sdkCredential(RouteConnex::app($tenantA)->sdk(), 'clientId'))->toBe('tenant-a-id')
        ->and(sdkCredential(RouteConnex::app($tenantB)->sdk(), 'clientId'))->toBe('tenant-b-id');
});

it('builds a new SDK client when the secret changes', function () {
    $app = integrationApp();
    $before = RouteConnex::app($app)->sdk();

    $app->forceFill(['client_secret' => 'rotated-secret']);
    $after = RouteConnex::app($app)->sdk();

    expect($after)->not->toBe($before)
        ->and(sdkCredential($after, 'clientSecret'))->toBe('rotated-secret');
});

it('keeps model Apps apart from config Apps', function () {
    config()->set('route-connex-laravel.apps.default', ['client_id' => 'model-id', 'client_secret' => 'model-secret']);

    expect(RouteConnex::app(integrationApp())->sdk())->not->toBe(RouteConnex::sdk());
});

it('works with an unsaved model', function () {
    $app = integrationApp(key: null);

    expect($app->getRouteConnexKey())->toBeNull()
        ->and(sdkCredential(RouteConnex::app($app)->sdk(), 'clientId'))->toBe('model-id');
});

it('works with an App that isn\'t a model', function () {
    $app = new PlainApp('plain-id', 'plain-secret', key: 'plain', baseUrl: 'https://plain.example.com/api');
    $client = RouteConnex::app($app);

    expect($client->getApp())->toBe($app)
        ->and(sdkCredential($client->sdk(), 'clientId'))->toBe('plain-id')
        ->and($client->sdk()->getBaseUrl())->toBe('https://plain.example.com/api');
});

it('calls the API with the model App\'s client', function () {
    $api = fakeRouteConnexApi()->queue(FakeApi::token(), FakeApi::json(['data' => ['status' => 'ok']]));

    $response = RouteConnex::app(integrationApp())->routeConnex()->testAuth()->get();

    expect($response->json('data.status'))->toBe('ok')
        ->and($api->tokenRequests())->toHaveCount(1)
        ->and($api->serviceRequests())->toHaveCount(1);
});

it('throws when a model App\'s Credentials are empty', function (string $key, mixed $value) {
    RouteConnex::app(integrationApp([$key => $value], key: 5));
})->with([
    'no client ID' => ['client_id', null],
    'empty client ID' => ['client_id', ''],
    'no client secret' => ['client_secret', null],
    'empty client secret' => ['client_secret', ''],
])->throws(RouteConnexConfigurationException::class, 'RouteConnex App ['.IntegrationApp::class.' #5] has no client ID or client secret.');

it('says when the App without Credentials is unsaved', function () {
    RouteConnex::app(integrationApp(['client_id' => null], key: null));
})->throws(RouteConnexConfigurationException::class, 'RouteConnex App ['.IntegrationApp::class.' (unsaved)] has no client ID or client secret.');

it('hands the App to a bound factory subclass', function () {
    $factory = new class extends RouteConnexClientFactory
    {
        /** @var list<RouteConnexAppContract> */
        public array $apps = [];

        protected function newClient(RouteConnexAppContract $app): ErClient
        {
            $this->apps[] = $app;

            return parent::newClient($app);
        }
    };

    app()->instance(RouteConnexClientFactory::class, $factory);

    $app = integrationApp();
    RouteConnex::app($app);

    expect($factory->apps)->toBe([$app]);
});
