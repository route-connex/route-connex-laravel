<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use RouteConnex\Laravel\Contracts\RouteConnexAppContract;
use RouteConnex\Laravel\Enums\RouteConnexRunStatus;
use RouteConnex\Laravel\Events\RouteConnexRunCreated;
use RouteConnex\Laravel\Exceptions\RouteConnexConfigurationException;
use RouteConnex\Laravel\Exceptions\RouteConnexRequestException;
use RouteConnex\Laravel\Facades\RouteConnex;
use RouteConnex\Laravel\Http\RouteConnexResponse;
use RouteConnex\Laravel\Models\RouteConnexApp;
use RouteConnex\Laravel\Models\RouteConnexRun;
use RouteConnex\Laravel\Runs\RouteConnexRunRecorder;
use RouteConnex\Laravel\Tests\Fixtures\FakeApi;
use RouteConnex\Laravel\Tests\Fixtures\IntegrationApp;
use RouteConnex\Laravel\Tests\Fixtures\MongoConnectionRouteConnexRun;
use RouteConnex\Laravel\Tests\Fixtures\PlainApp;
use RouteConnex\Laravel\Tests\Fixtures\TenantRouteConnexApp;
use RouteConnex\Laravel\Tests\Fixtures\TenantRouteConnexRun;

beforeEach(function () {
    $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

    config()->set('route-connex-laravel.apps.default', ['client_id' => 'default-id', 'client_secret' => 'default-secret']);

    $this->api = fakeRouteConnexApi();
});

/**
 * @param  array<string, mixed>  $attributes
 */
function storedModelApp(array $attributes = []): RouteConnexApp
{
    /** @var class-string<RouteConnexApp> $model */
    $model = config('route-connex-laravel.models.app');

    return $model::create(array_merge(['name' => 'SharePoint', 'client_id' => 'model-id', 'client_secret' => 'model-secret'], $attributes));
}

it('stores the Run of a System call and returns it', function (string $method, array $args, string $service, array $request) {
    $this->api->queue(FakeApi::token(), FakeApi::run('run-42', [
        'status' => ['id' => 'queued'],
        'type' => ['name' => 'upload-file', 'system' => 'microsoft-sharepoint'],
    ]));

    $run = RouteConnex::microsoftSharepoint()->files()->{$method}(...$args, meta: ['invoice_id' => 42]);

    $stored = runModel()::query()->sole();

    expect($run)->toBeInstanceOf(runModel())
        ->and($run->exists)->toBeTrue()
        ->and($stored->getKey())->toEqual($run->getKey())
        ->and($stored->run_id)->toBe('run-42')
        ->and($stored->app_name)->toBe('default')
        ->and($stored->app_id)->toBeNull()
        ->and($stored->service)->toBe($service)
        ->and($stored->status)->toBe(RouteConnexRunStatus::Queued)
        ->and($stored->type)->toBe('upload-file')
        ->and($stored->meta)->toEqual(['invoice_id' => 42])
        ->and($stored->request)->toEqual($request)
        ->and($stored->response)->toEqual([
            'id' => 'run-42',
            'status' => ['id' => 'queued'],
            'type' => ['name' => 'upload-file', 'system' => 'microsoft-sharepoint'],
        ])
        ->and($stored->finished_at)->toBeNull();
})->with([
    'POST' => ['post', ['body' => ['file_name' => 'a.pdf']], 'POST microsoft-sharepoint/files', ['query' => [], 'body' => ['file_name' => 'a.pdf', '_meta_' => ['invoice_id' => 42]]]],
    'GET' => ['get', ['query' => ['site' => 'x']], 'GET microsoft-sharepoint/files', ['query' => ['site' => 'x', '_meta_' => '{"invoice_id":42}'], 'body' => []]],
]);

it('stores created and no type when the answer has neither', function () {
    $this->api->queue(FakeApi::token(), FakeApi::run());

    $run = RouteConnex::hpeContentManager()->records()->post();

    expect($run->status)->toBe(RouteConnexRunStatus::Created)
        ->and($run->type)->toBeNull()
        ->and($run->meta)->toBe([]);
});

it('stores a numeric Run ID as a string', function () {
    $this->api->queue(FakeApi::token(), FakeApi::json(['data' => ['id' => 123]]));

    expect(RouteConnex::hpeContentManager()->records()->post()->run_id)->toBe('123');
});

it('sets app_id for a model App', function () {
    $app = storedModelApp();
    $this->api->queue(FakeApi::token(), FakeApi::run());

    $run = RouteConnex::app($app)->microsoftSharepoint()->files()->post();

    expect($run->app_name)->toBeNull()
        ->and($run->app_id)->toBe((string) $app->getRouteConnexKey())
        ->and($run->fresh()?->app->getRouteConnexKey())->toBe($app->getRouteConnexKey());
});

it('sets app_id for any App with a key', function (RouteConnexAppContract $app) {
    $this->api->queue(FakeApi::token(), FakeApi::run());

    $run = RouteConnex::app($app)->microsoftSharepoint()->files()->post();

    expect($run->app_name)->toBeNull()
        ->and($run->app_id)->toBe('5')
        ->and($run->getConnection()->getName())->toBe(DB::getDefaultConnection())
        ->and(runModel()::query()->count())->toBe(1);
})->with([
    'a consumer model with the trait' => fn () => (new IntegrationApp)->forceFill(['id' => 5, 'client_id' => 'id', 'client_secret' => 'secret']),
    'an App that isn\'t a model' => fn () => new PlainApp('id', 'secret', key: 5),
]);

it('fires RouteConnexRunCreated once with the stored Run', function () {
    Event::fake([RouteConnexRunCreated::class]);
    $this->api->queue(FakeApi::token(), FakeApi::run());

    $run = RouteConnex::microsoftSharepoint()->files()->post();

    Event::assertDispatchedTimes(RouteConnexRunCreated::class, 1);
    Event::assertDispatched(RouteConnexRunCreated::class, fn (RouteConnexRunCreated $event): bool => $event->run === $run && $run->exists);
});

it('leaves out the payloads turned off in config', function (string $key, string $column) {
    config()->set("route-connex-laravel.runs.{$key}", false);
    $this->api->queue(FakeApi::token(), FakeApi::run());

    $run = RouteConnex::microsoftSharepoint()->files()->post(body: ['file_name' => 'a.pdf']);

    expect(runModel()::query()->sole()->{$column})->toBeNull()
        ->and($run->{$column === 'request' ? 'response' : 'request'})->not->toBeNull();
})->with([
    'request' => ['store_request', 'request'],
    'response' => ['store_response', 'response'],
]);

it('stores both payloads by default', function () {
    expect(config('route-connex-laravel.runs'))->toBe(['store_request' => true, 'store_response' => true]);
});

it('throws and stores nothing when the answer is unusable', function (Response $answer, string $message) {
    $this->api->queue(FakeApi::token(), $answer);

    try {
        RouteConnex::microsoftSharepoint()->files()->post();
        $this->fail('No exception thrown.');
    } catch (RouteConnexRequestException $e) {
        expect($e->getMessage())->toBe($message)
            ->and($e->response())->toBeInstanceOf(RouteConnexResponse::class)
            ->and($e->response()->status())->toBe(200);
    }

    expect(runModel()::query()->count())->toBe(0);
})->with([
    'no data' => [fn () => FakeApi::json(['status' => 'success']), 'The RouteConnex API answered without a Run ID.'],
    'no Run ID' => [fn () => FakeApi::json(['data' => ['status' => ['id' => 'created']]]), 'The RouteConnex API answered without a Run ID.'],
    'empty Run ID' => [fn () => FakeApi::run(''), 'The RouteConnex API answered without a Run ID.'],
    'unknown status' => [fn () => FakeApi::run('run-1', ['status' => ['id' => 'paused']]), 'The RouteConnex API answered with an unknown Run status [paused].'],
    'status without an ID' => [fn () => FakeApi::run('run-1', ['status' => ['name' => 'Queued']]), 'The RouteConnex API answered with an unknown Run status [null].'],
]);

it('stores nothing when the API answers with an error', function () {
    $this->api->queue(FakeApi::token(), FakeApi::json(['message' => 'Invalid site'], 422));

    expect(fn () => RouteConnex::microsoftSharepoint()->files()->post())
        ->toThrow(RouteConnexRequestException::class, 'The RouteConnex API answered with status 422.')
        ->and(runModel()::query()->count())->toBe(0);
});

it('refuses an App without a key before sending anything', function (RouteConnexAppContract $app) {
    expect(fn () => RouteConnex::app($app)->microsoftSharepoint()->files()->post())
        ->toThrow(RouteConnexConfigurationException::class, 'RouteConnex App ['.$app::class.'] has no key, so its Runs can\'t be stored. Save it before calling a System Service.')
        ->and($this->api->requests())->toBe([])
        ->and(runModel()::query()->count())->toBe(0);
})->with([
    'an unsaved model' => fn () => (new IntegrationApp)->forceFill(['client_id' => 'id', 'client_secret' => 'secret']),
    'an App with an empty key' => fn () => new PlainApp('id', 'secret', key: ''),
]);

it('still calls Services that create no Run with an App without a key', function () {
    $this->api->queue(FakeApi::token(), FakeApi::json(['data' => ['status' => 'ok']]));

    $response = RouteConnex::app(new PlainApp('id', 'secret'))->routeConnex()->testAuth()->get();

    expect($response->json('data.status'))->toBe('ok');
});

it('stores the Run of a model App on that App\'s connection', function () {
    migrateTenantConnection();
    // A Run model without a connection of its own (not the MongoDB fixture).
    config()->set('route-connex-laravel.models.run', RouteConnexRun::class);

    $app = TenantRouteConnexApp::create(['name' => 'Tenant', 'client_id' => 'tenant-id', 'client_secret' => 'tenant-secret']);
    $this->api->queue(FakeApi::token(), FakeApi::run());

    $run = RouteConnex::app($app)->microsoftSharepoint()->files()->post();

    expect($run->getConnectionName())->toBe('tenant')
        ->and(DB::connection('tenant')->table('route_connex_runs')->count())->toBe(1);
});

it('stores a config App\'s Run on the default connection', function () {
    $this->api->queue(FakeApi::token(), FakeApi::run());

    $run = RouteConnex::microsoftSharepoint()->files()->post();

    expect($run->getConnection()->getName())->toBe(DB::getDefaultConnection())
        ->and(DB::table('route_connex_runs')->count())->toBe(1);
});

it('stores the Run on the Run model\'s own connection first', function () {
    migrateTenantConnection();
    config()->set('route-connex-laravel.models.run', TenantRouteConnexRun::class);

    // The App is on the default connection; the Run model says `tenant`.
    $app = storedModelApp();
    $this->api->queue(FakeApi::token(), FakeApi::run());

    $run = RouteConnex::app($app)->microsoftSharepoint()->files()->post();

    expect($run->getConnectionName())->toBe('tenant')
        ->and(DB::connection('tenant')->table('route_connex_runs')->count())->toBe(1);
});

it('stores a Run on MongoDB whose App is on SQLite', function () {
    migrateTenantConnection();
    config()->set('route-connex-laravel.models.app', TenantRouteConnexApp::class);
    config()->set('route-connex-laravel.models.run', MongoConnectionRouteConnexRun::class);

    $app = TenantRouteConnexApp::create(['name' => 'Tenant', 'client_id' => 'tenant-id', 'client_secret' => 'tenant-secret']);
    $this->api->queue(FakeApi::token(), FakeApi::run());

    $run = RouteConnex::app($app)->microsoftSharepoint()->files()->post();
    $stored = MongoConnectionRouteConnexRun::query()->sole();

    expect($run->getConnectionName())->toBe('mongodb')
        ->and($stored->app_id)->toBe((string) $app->getKey())
        ->and($stored->app->getRouteConnexClientId())->toBe('tenant-id');
})->skip(fn () => DB::getDefaultConnection() !== 'mongodb', 'Needs MongoDB.');

it('uses a bound recorder subclass', function () {
    app()->bind(RouteConnexRunRecorder::class, fn () => new class extends RouteConnexRunRecorder
    {
        protected function type(array $data): ?string
        {
            return 'from-subclass';
        }
    });

    $this->api->queue(FakeApi::token(), FakeApi::run());

    expect(RouteConnex::microsoftSharepoint()->files()->post()->type)->toBe('from-subclass')
        ->and(runModel()::query()->sole()->type)->toBe('from-subclass');
});
