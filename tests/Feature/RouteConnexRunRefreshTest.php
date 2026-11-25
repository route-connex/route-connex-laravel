<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RouteConnex\Laravel\Enums\RouteConnexRunStatus;
use RouteConnex\Laravel\Exceptions\RouteConnexRequestException;
use RouteConnex\Laravel\Tests\Fixtures\FakeApi;
use RouteConnex\Laravel\Tests\Fixtures\TenantRouteConnexRun;

beforeEach(function () {
    $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

    config()->set('route-connex-laravel.apps.default', ['client_id' => 'default-id', 'client_secret' => 'default-secret']);

    $this->api = fakeRouteConnexApi();
});

/**
 * The answer of `GET run/{id}`.
 *
 * @param  array<string, mixed>  $data
 */
function runAnswer(string $status, array $data = []): Response
{
    return FakeApi::json(['data' => array_merge([
        'id' => 'run-1',
        'status' => ['id' => $status],
        'type' => ['name' => 'upload-file', 'system' => 'microsoft-sharepoint'],
    ], $data)]);
}

it('gets the Run from RouteConnex with the Run\'s own App and updates it', function () {
    Carbon::setTestNow('2026-11-26 10:00:00');
    $this->api->queue(FakeApi::token(), runAnswer('succeeded', ['response_data' => ['url' => 'https://example.com/a.pdf']]));

    $run = createRun(['status' => RouteConnexRunStatus::Queued]);
    $returned = $run->refreshFromRouteConnex();

    [$request] = $this->api->serviceRequests();
    $stored = freshRun($run);

    expect($returned)->toBe($run)
        ->and($request->getMethod())->toBe('GET')
        ->and($request->getUri()->getPath())->toBe('/api/v1/run/run-1')
        ->and($request->getHeaderLine('Authorization'))->toBe('Bearer test-token')
        ->and($stored->status)->toBe(RouteConnexRunStatus::Succeeded)
        ->and($stored->type)->toBe('upload-file')
        ->and($stored->response)->toEqual([
            'id' => 'run-1',
            'status' => ['id' => 'succeeded'],
            'type' => ['name' => 'upload-file', 'system' => 'microsoft-sharepoint'],
            'response_data' => ['url' => 'https://example.com/a.pdf'],
        ])
        ->and($stored->finished_at?->toDateTimeString())->toBe('2026-11-26 10:00:00');
});

it('uses the model App\'s Credentials for a model App\'s Run', function () {
    $app = createModelApp(['client_id' => 'model-id']);
    $this->api->queue(FakeApi::token(), runAnswer('running'));

    createRun(['app_name' => null, 'app_id' => (string) $app->getRouteConnexKey()])->refreshFromRouteConnex();

    parse_str((string) $this->api->tokenRequests()[0]->getBody(), $form);

    expect($form['client_id'])->toBe('model-id')
        ->and($this->api->serviceRequests())->toHaveCount(1);
});

it('sets finished_at once, when a finished status first arrives', function () {
    $run = createRun();

    $this->api->queue(FakeApi::token(), runAnswer('running'), runAnswer('failed'), runAnswer('failed'));

    $run->refreshFromRouteConnex();
    expect(freshRun($run)->finished_at)->toBeNull();

    Carbon::setTestNow('2026-11-26 10:00:00');
    $run->refreshFromRouteConnex();
    expect(freshRun($run)->finished_at?->toDateTimeString())->toBe('2026-11-26 10:00:00');

    Carbon::setTestNow('2026-11-26 11:00:00');
    $run->refreshFromRouteConnex();
    expect(freshRun($run)->finished_at?->toDateTimeString())->toBe('2026-11-26 10:00:00');
});

it('keeps a finished Run as it is on a later unfinished status, without a warning', function () {
    Log::spy();
    Carbon::setTestNow('2026-11-26 10:00:00');

    $run = createRun([
        'status' => RouteConnexRunStatus::Succeeded,
        'type' => 'upload-file',
        'response' => ['final' => true],
        'finished_at' => now(),
    ]);
    $this->api->queue(FakeApi::token(), runAnswer('running', ['type' => ['name' => 'other']]));

    $run->refreshFromRouteConnex();
    $stored = freshRun($run);

    expect($stored->status)->toBe(RouteConnexRunStatus::Succeeded)
        ->and($stored->type)->toBe('upload-file')
        ->and($stored->response)->toBe(['final' => true])
        ->and($stored->finished_at?->toDateTimeString())->toBe('2026-11-26 10:00:00');

    Log::shouldNotHaveReceived('warning');
});

it('keeps a finished Run as it is on a different finished status, and logs a warning', function () {
    Log::spy();

    $run = createRun(['status' => RouteConnexRunStatus::Succeeded, 'response' => ['final' => true], 'finished_at' => now()]);
    $this->api->queue(FakeApi::token(), runAnswer('failed'));

    $run->refreshFromRouteConnex();

    expect(freshRun($run)->status)->toBe(RouteConnexRunStatus::Succeeded)
        ->and(freshRun($run)->response)->toBe(['final' => true]);

    Log::shouldHaveReceived('warning')->once()->with('RouteConnex Run [run-1] is already succeeded; ignored the status failed.');
});

it('doesn\'t warn about the same finished status again', function () {
    Log::spy();

    $run = createRun(['status' => RouteConnexRunStatus::Failed, 'finished_at' => now()]);

    expect($run->updateFromRouteConnex(RouteConnexRunStatus::Failed, []))->toBeFalse();

    Log::shouldNotHaveReceived('warning');
});

it('returns whether the status changed', function (RouteConnexRunStatus $from, RouteConnexRunStatus $to, bool $changed) {
    expect(createRun(['status' => $from])->updateFromRouteConnex($to, []))->toBe($changed);
})->with([
    'created to running' => [RouteConnexRunStatus::Created, RouteConnexRunStatus::Running, true],
    'running to running' => [RouteConnexRunStatus::Running, RouteConnexRunStatus::Running, false],
    'running to errored' => [RouteConnexRunStatus::Running, RouteConnexRunStatus::Errored, true],
    'succeeded to failed' => [RouteConnexRunStatus::Succeeded, RouteConnexRunStatus::Failed, false],
]);

it('updates the type and response of an unfinished Run without a status change', function () {
    $run = createRun(['status' => RouteConnexRunStatus::Running, 'type' => null]);

    expect($run->updateFromRouteConnex(RouteConnexRunStatus::Running, ['type' => ['name' => 'upload-file']]))->toBeFalse()
        ->and(freshRun($run)->type)->toBe('upload-file')
        ->and(freshRun($run)->response)->toBe(['type' => ['name' => 'upload-file']]);
});

it('keeps the type when the update has none', function () {
    $run = createRun(['status' => RouteConnexRunStatus::Queued, 'type' => 'upload-file']);

    $run->updateFromRouteConnex(RouteConnexRunStatus::Running, ['id' => 'run-1']);

    expect(freshRun($run)->type)->toBe('upload-file');
});

it('keeps the response null when storing responses is off', function () {
    config()->set('route-connex-laravel.runs.store_response', false);
    $this->api->queue(FakeApi::token(), runAnswer('succeeded'));

    $run = createRun(['status' => RouteConnexRunStatus::Queued]);
    $run->refreshFromRouteConnex();

    expect(freshRun($run)->response)->toBeNull()
        ->and(freshRun($run)->status)->toBe(RouteConnexRunStatus::Succeeded);
});

it('throws and leaves the Run unchanged when the status is missing or unknown', function (Response $answer, string $message) {
    $this->api->queue(FakeApi::token(), $answer);

    $run = createRun(['status' => RouteConnexRunStatus::Running, 'type' => 'upload-file']);

    expect(fn () => $run->refreshFromRouteConnex())->toThrow(RouteConnexRequestException::class, $message);

    $stored = freshRun($run);

    expect($stored->status)->toBe(RouteConnexRunStatus::Running)
        ->and($stored->type)->toBe('upload-file')
        ->and($stored->response)->toBeNull()
        ->and($stored->finished_at)->toBeNull();
})->with([
    'no data' => [fn () => FakeApi::json(['status' => 'success']), 'The RouteConnex API answered with an unknown Run status [null].'],
    'no status' => [fn () => FakeApi::json(['data' => ['id' => 'run-1']]), 'The RouteConnex API answered with an unknown Run status [null].'],
    'unknown status' => [fn () => runAnswer('paused'), 'The RouteConnex API answered with an unknown Run status [paused].'],
]);

it('lets API failures through', function () {
    $this->api->queue(FakeApi::token(), FakeApi::json(['message' => 'Not found'], 404));

    $run = createRun();

    expect(fn () => $run->refreshFromRouteConnex())->toThrow(RouteConnexRequestException::class, 'The RouteConnex API answered with status 404.')
        ->and(freshRun($run)->status)->toBe(RouteConnexRunStatus::Created);
});

it('updates a Run on the connection it was stored on', function () {
    migrateTenantConnection();
    config()->set('route-connex-laravel.models.run', TenantRouteConnexRun::class);
    $this->api->queue(FakeApi::token(), runAnswer('succeeded'));

    $run = createRun();
    $run->refreshFromRouteConnex();

    expect($run->getConnectionName())->toBe('tenant')
        ->and(DB::connection('tenant')->table('route_connex_runs')->value('status'))->toBe('succeeded')
        ->and(DB::connection('tenant')->table('route_connex_runs')->value('finished_at'))->not->toBeNull();
});
