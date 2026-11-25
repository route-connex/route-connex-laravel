<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RouteConnex\Laravel\Apps\RouteConnexConfigApp;
use RouteConnex\Laravel\Enums\RouteConnexRunStatus;
use RouteConnex\Laravel\Exceptions\RouteConnexConfigurationException;
use RouteConnex\Laravel\Models\RouteConnexRun;
use RouteConnex\Laravel\Tests\Fixtures\TenantRouteConnexApp;

beforeEach(function () {
    $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
});

it('uses RouteConnexRun on the route_connex_runs table by default', function () {
    expect((require __DIR__.'/../../config/route-connex-laravel.php')['models']['run'])->toBe(RouteConnexRun::class)
        ->and((new RouteConnexRun)->getTable())->toBe('route_connex_runs');
});

it('round-trips every column with its cast', function () {
    Carbon::setTestNow('2026-11-26 10:00:00');

    $run = freshRun(createRun([
        'type' => 'upload-file',
        'status' => RouteConnexRunStatus::DryRun,
        'meta' => ['invoice_id' => 42],
        'request' => ['query' => [], 'body' => ['file_name' => 'invoice.pdf', '_meta_' => ['invoice_id' => 42]]],
        'response' => ['id' => 'run-1', 'response_data' => ['files' => [['name' => 'a'], ['name' => 'b']]]],
        'finished_at' => now(),
    ]));

    // MySQL's json type reorders object keys, so arrays compare by value.
    expect($run->run_id)->toBe('run-1')
        ->and($run->app_name)->toBe('default')
        ->and($run->app_id)->toBeNull()
        ->and($run->service)->toBe('POST microsoft-sharepoint/files')
        ->and($run->type)->toBe('upload-file')
        ->and($run->status)->toBe(RouteConnexRunStatus::DryRun)
        ->and($run->meta)->toEqual(['invoice_id' => 42])
        ->and($run->request)->toEqual(['query' => [], 'body' => ['file_name' => 'invoice.pdf', '_meta_' => ['invoice_id' => 42]]])
        ->and($run->response)->toEqual(['id' => 'run-1', 'response_data' => ['files' => [['name' => 'a'], ['name' => 'b']]]])
        ->and($run->finished_at?->toDateTimeString())->toBe('2026-11-26 10:00:00')
        ->and($run->purged_at)->toBeNull();
});

it('stores null payloads and type', function () {
    $run = freshRun(createRun());

    expect($run->type)->toBeNull()
        ->and($run->meta)->toBeNull()
        ->and($run->request)->toBeNull()
        ->and($run->response)->toBeNull()
        ->and($run->finished_at)->toBeNull();
});

it('reads the status from its string value', function (RouteConnexRunStatus $status) {
    expect(freshRun(createRun(['status' => $status->value]))->status)->toBe($status);
})->with(RouteConnexRunStatus::cases());

it('tells finished statuses apart', function (RouteConnexRunStatus $status, bool $finished) {
    expect($status->isFinished())->toBe($finished);
})->with([
    'created' => [RouteConnexRunStatus::Created, false],
    'queued' => [RouteConnexRunStatus::Queued, false],
    'running' => [RouteConnexRunStatus::Running, false],
    'succeeded' => [RouteConnexRunStatus::Succeeded, true],
    'dry-run' => [RouteConnexRunStatus::DryRun, true],
    'failed' => [RouteConnexRunStatus::Failed, true],
    'errored' => [RouteConnexRunStatus::Errored, true],
]);

it('has the RouteConnex status values', function () {
    expect(array_map(fn (RouteConnexRunStatus $status): string => $status->value, RouteConnexRunStatus::cases()))
        ->toBe(['created', 'queued', 'running', 'succeeded', 'dry-run', 'failed', 'errored']);
});

it('refuses to save a Run without exactly one App column', function (array $apps) {
    createRun($apps);
})->with([
    'both' => [['app_name' => 'default', 'app_id' => '1']],
    'neither' => [['app_name' => null, 'app_id' => null]],
    'empty name, no ID' => [['app_name' => '', 'app_id' => null]],
])->throws(RouteConnexConfigurationException::class, 'A RouteConnex Run needs exactly one of [app_name] and [app_id].');

it('refuses to update a Run into both App columns', function () {
    createRun()->update(['app_id' => '1']);
})->throws(RouteConnexConfigurationException::class);

it('stores nothing when the App columns are invalid', function () {
    try {
        createRun(['app_id' => '1']);
    } catch (RouteConnexConfigurationException) {
        //
    }

    expect(runModel()::query()->count())->toBe(0);
});

it('lets Runs share a run_id', function () {
    createRun(['run_id' => 'shared', 'app_name' => 'default']);
    createRun(['run_id' => 'shared', 'app_name' => null, 'app_id' => '7']);

    expect(runModel()::query()->where('run_id', 'shared')->count())->toBe(2);
});

it('returns the config App by app_name', function () {
    config()->set('route-connex-laravel.apps.hpe', ['client_id' => 'hpe-id', 'client_secret' => 'hpe-secret']);

    $app = freshRun(createRun(['app_name' => 'hpe']))->app;

    expect($app)->toBeInstanceOf(RouteConnexConfigApp::class)
        ->and($app->getRouteConnexKey())->toBe('hpe')
        ->and($app->getRouteConnexClientId())->toBe('hpe-id');
});

it('returns the model App by app_id, whatever its key type', function () {
    $model = createModelApp();
    $run = freshRun(createRun(['app_name' => null, 'app_id' => (string) $model->getRouteConnexKey()]));

    expect($run->app_id)->toBeString()
        ->and($run->app)->toBeInstanceOf(config('route-connex-laravel.models.app'))
        ->and($run->app->getRouteConnexKey())->toBe($model->getRouteConnexKey())
        ->and($run->app->getRouteConnexClientSecret())->toBe('model-secret');
});

it('throws when the config App is no longer configured', function () {
    freshRun(createRun(['app_name' => 'gone']))->app;
})->throws(RouteConnexConfigurationException::class, 'RouteConnex App [gone] is not configured in [route-connex-laravel.apps].');

it('throws when the model App no longer exists', function () {
    $model = createModelApp();
    $id = (string) $model->getRouteConnexKey();
    $model->delete();

    expect(fn () => freshRun(createRun(['app_name' => null, 'app_id' => $id]))->app)
        ->toThrow(RouteConnexConfigurationException::class, 'RouteConnex App ['.config('route-connex-laravel.models.app')." #{$id}] of the Run can't be found.");
});

it('loads the model App from the App model\'s own connection', function () {
    // On the MongoDB job, this is a Run on MongoDB whose App is on SQLite.
    migrateTenantConnection();
    config()->set('route-connex-laravel.models.app', TenantRouteConnexApp::class);

    $model = TenantRouteConnexApp::create(['name' => 'Tenant', 'client_id' => 'tenant-id', 'client_secret' => 'tenant-secret']);
    $run = freshRun(createRun(['app_name' => null, 'app_id' => (string) $model->getKey()]));

    expect($run->getConnection()->getName())->toBe(DB::getDefaultConnection())
        ->and($run->app)->toBeInstanceOf(TenantRouteConnexApp::class)
        ->and($run->app->getRouteConnexClientId())->toBe('tenant-id')
        ->and(DB::connection('tenant')->table('route_connex_apps')->count())->toBe(1);
});
