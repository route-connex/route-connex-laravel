<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use RouteConnex\Laravel\Facades\RouteConnex;
use RouteConnex\Laravel\Models\RouteConnexApp;
use RouteConnex\RouteConnexSdkPhp\ErClient;

beforeEach(function () {
    $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
});

/**
 * The configured App model class: `RouteConnexApp`, or its MongoDB subclass
 * when the tests run on MongoDB.
 *
 * @return class-string<RouteConnexApp>
 */
function appModel(): string
{
    /** @var class-string<RouteConnexApp> */
    return config('route-connex-laravel.models.app');
}

/**
 * @param  array<string, mixed>  $attributes
 */
function createApp(array $attributes = []): RouteConnexApp
{
    return appModel()::create(array_merge([
        'name' => 'SharePoint',
        'client_id' => 'model-id',
        'client_secret' => 'model-secret',
    ], $attributes));
}

/**
 * The App's row as stored, without casts.
 *
 * @return array<string, mixed>
 */
function storedApp(): array
{
    return (array) DB::table('route_connex_apps')->first();
}

it('uses RouteConnexApp on the route_connex_apps table by default', function () {
    expect((require __DIR__.'/../../config/route-connex-laravel.php')['models']['app'])->toBe(RouteConnexApp::class)
        ->and((new RouteConnexApp)->getTable())->toBe('route_connex_apps');
});

it('generates a Callback token when an App is created without one', function (mixed $token) {
    $app = createApp(['callback_token' => $token]);

    expect($app->callback_token)->toMatch('/^[0-9a-f]{64}$/')
        ->and(appModel()::query()->findOrFail($app->getKey())->getRouteConnexCallbackToken())->toBe($app->callback_token);
})->with(['missing' => null, 'empty' => '']);

it('keeps a Callback token given on create', function () {
    expect(createApp(['callback_token' => 'given-token'])->getRouteConnexCallbackToken())->toBe('given-token');
});

it('generates a new 64-hex Callback token each time', function () {
    $token = RouteConnex::generateCallbackToken();

    expect($token)->toMatch('/^[0-9a-f]{64}$/')
        ->and(RouteConnex::generateCallbackToken())->not->toBe($token);
});

it('encrypts the client secret and the Callback token at rest', function () {
    createApp(['callback_token' => 'given-token']);

    $stored = storedApp();

    expect($stored['client_secret'])->not->toBe('model-secret')
        ->and(Crypt::decryptString($stored['client_secret']))->toBe('model-secret')
        ->and($stored['callback_token'])->not->toBe('given-token')
        ->and(Crypt::decryptString($stored['callback_token']))->toBe('given-token')
        ->and($stored['client_id'])->toBe('model-id');
});

it('hides the client secret and the Callback token', function () {
    $array = createApp()->toArray();

    expect($array)->not->toHaveKeys(['client_secret', 'callback_token'])
        ->and($array)->toHaveKeys(['name', 'client_id']);
});

it('calls the API with an App read back from the database', function () {
    $app = appModel()::query()->findOrFail(createApp(['base_url' => 'https://model.example.com/api'])->getKey());

    $sdk = RouteConnex::app($app)->sdk();

    expect((new ReflectionProperty(ErClient::class, 'clientId'))->getValue($sdk))->toBe('model-id')
        ->and((new ReflectionProperty(ErClient::class, 'clientSecret'))->getValue($sdk))->toBe('model-secret')
        ->and($sdk->getBaseUrl())->toBe('https://model.example.com/api');
});

it('keys Apps by integer on SQL databases and by string ObjectId on MongoDB', function () {
    $key = createApp()->getRouteConnexKey();

    if (DB::connection()->getDriverName() === 'mongodb') {
        expect($key)->toBeString()->toMatch('/^[0-9a-f]{24}$/');
    } else {
        expect($key)->toBeInt();
    }
});
