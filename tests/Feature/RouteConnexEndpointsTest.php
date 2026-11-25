<?php

declare(strict_types=1);

use RouteConnex\Laravel\Facades\RouteConnex;
use RouteConnex\Laravel\Http\RouteConnexResponse;
use RouteConnex\Laravel\Models\RouteConnexRun;
use RouteConnex\Laravel\Tests\Fixtures\FakeApi;
use RouteConnex\Laravel\Tests\Fixtures\RecordingFilesEndpoint;

beforeEach(function () {
    $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

    config()->set('route-connex-laravel.apps', [
        'default' => ['client_id' => 'default-id', 'client_secret' => 'default-secret'],
        'hpe' => ['client_id' => 'hpe-id', 'client_secret' => 'hpe-secret', 'base_url' => 'https://hpe.example.com/api'],
    ]);

    $this->api = fakeRouteConnexApi();
});

dataset('endpoints', [
    'about' => [fn () => RouteConnex::routeConnex()->about()->get(), 'GET', '/api/v1/route-connex/about', false, RouteConnexResponse::class],
    'test-auth' => [fn () => RouteConnex::routeConnex()->testAuth()->get(), 'GET', '/api/v1/route-connex/test-auth', true, RouteConnexResponse::class],
    'run by ID' => [fn () => RouteConnex::run()->_id_('run-123')->get(), 'GET', '/api/v1/run/run-123', true, RouteConnexResponse::class],
    'sharepoint files GET' => [fn () => RouteConnex::microsoftSharepoint()->files()->get(), 'GET', '/api/v1/microsoft-sharepoint/files', true, RouteConnexRun::class],
    'sharepoint files POST' => [fn () => RouteConnex::microsoftSharepoint()->files()->post(), 'POST', '/api/v1/microsoft-sharepoint/files', true, RouteConnexRun::class],
    'sharepoint drive columns' => [fn () => RouteConnex::microsoftSharepoint()->driveColumns()->get(), 'GET', '/api/v1/microsoft-sharepoint/drive-columns', true, RouteConnexRun::class],
    'content manager records GET' => [fn () => RouteConnex::hpeContentManager()->records()->get(), 'GET', '/api/v1/hpe-content-manager/records', true, RouteConnexRun::class],
    'content manager records POST' => [fn () => RouteConnex::hpeContentManager()->records()->post(), 'POST', '/api/v1/hpe-content-manager/records', true, RouteConnexRun::class],
]);

it('sends each endpoint\'s request to the right URI and returns its result', function (Closure $call, string $method, string $path, bool $authenticated, string $returns) {
    if ($authenticated) {
        $this->api->queue(FakeApi::token('abc'));
    }

    $this->api->queue(FakeApi::run());

    $response = $call();

    [$request] = $this->api->serviceRequests();

    expect($response)->toBeInstanceOf($returns)
        ->and($request->getMethod())->toBe($method)
        ->and((string) $request->getUri()->withQuery(''))->toBe("https://app.routeconnex.com{$path}")
        ->and($this->api->tokenRequests())->toHaveCount($authenticated ? 1 : 0)
        ->and($request->getHeaderLine('Authorization'))->toBe($authenticated ? 'Bearer abc' : '');
})->with('endpoints');

it('exposes the response with Laravel\'s HTTP client helpers', function () {
    $this->api->queue(FakeApi::json(['data' => ['name' => 'RouteConnex']], 200));

    $response = RouteConnex::routeConnex()->about()->get();

    expect($response->status())->toBe(200)
        ->and($response->successful())->toBeTrue()
        ->and($response->json('data.name'))->toBe('RouteConnex');
});

it('sends the query, JSON body and headers', function () {
    $this->api->queue(FakeApi::token(), FakeApi::run());

    RouteConnex::microsoftSharepoint()->files()->post(
        body: ['site_name' => 'Finance'],
        query: ['page' => '2'],
        headers: ['X-Test' => 'yes'],
    );

    [$request] = $this->api->serviceRequests();

    expect($request->getUri()->getQuery())->toBe('page=2')
        ->and(json_decode((string) $request->getBody(), true))->toBe(['site_name' => 'Finance'])
        ->and($request->getHeaderLine('X-Test'))->toBe('yes')
        ->and($request->getHeaderLine('Authorization'))->toBe('Bearer test-token');
});

it('sends the query of GET calls on endpoints without Meta', function () {
    $this->api->queue(FakeApi::token(), FakeApi::json());

    RouteConnex::run()->_id_('run-1')->get(query: ['include' => 'response'], headers: ['X-Test' => 'yes']);

    [$request] = $this->api->serviceRequests();

    expect($request->getUri()->getQuery())->toBe('include=response')
        ->and($request->getHeaderLine('X-Test'))->toBe('yes');
});

it('puts Meta as JSON in the query of GET calls', function () {
    $this->api->queue(FakeApi::token(), FakeApi::run());

    RouteConnex::hpeContentManager()->records()->get(query: ['query' => 'invoices'], meta: ['invoice_id' => 42]);

    [$request] = $this->api->serviceRequests();
    parse_str($request->getUri()->getQuery(), $query);

    expect($query)->toBe(['query' => 'invoices', '_meta_' => '{"invoice_id":42}'])
        ->and((string) $request->getBody())->toBe('');
});

it('puts Meta as an array in the body of POST calls', function () {
    $this->api->queue(FakeApi::token(), FakeApi::run());

    RouteConnex::microsoftSharepoint()->files()->post(body: ['file_name' => 'invoice.pdf'], meta: ['invoice_id' => 42]);

    [$request] = $this->api->serviceRequests();

    expect(json_decode((string) $request->getBody(), true))
        ->toBe(['file_name' => 'invoice.pdf', '_meta_' => ['invoice_id' => 42]])
        ->and($request->getUri()->getQuery())->toBe('');
});

it('replaces a _meta_ already in the data when Meta is given', function () {
    $this->api->queue(FakeApi::token(), FakeApi::run(), FakeApi::run());

    RouteConnex::hpeContentManager()->records()->get(query: ['_meta_' => '{"old":true}'], meta: ['new' => true]);
    RouteConnex::hpeContentManager()->records()->post(body: ['_meta_' => ['old' => true]], meta: ['new' => true]);

    [$get, $post] = $this->api->serviceRequests();
    parse_str($get->getUri()->getQuery(), $query);

    expect($query['_meta_'])->toBe('{"new":true}')
        ->and(json_decode((string) $post->getBody(), true))->toBe(['_meta_' => ['new' => true]]);
});

it('leaves the data alone when Meta is empty', function () {
    $this->api->queue(FakeApi::token(), FakeApi::run(), FakeApi::run());

    RouteConnex::hpeContentManager()->records()->get(query: ['_meta_' => '{"mine":true}']);
    RouteConnex::hpeContentManager()->records()->post(body: ['_meta_' => ['mine' => true]], meta: []);

    [$get, $post] = $this->api->serviceRequests();
    parse_str($get->getUri()->getQuery(), $query);

    expect($query['_meta_'])->toBe('{"mine":true}')
        ->and(json_decode((string) $post->getBody(), true))->toBe(['_meta_' => ['mine' => true]]);
});

it('adds nothing to the request without Meta', function () {
    $this->api->queue(FakeApi::token(), FakeApi::run(), FakeApi::run());

    RouteConnex::microsoftSharepoint()->files()->get();
    RouteConnex::microsoftSharepoint()->files()->post();

    [$get, $post] = $this->api->serviceRequests();

    expect($get->getUri()->getQuery())->toBe('')
        ->and((string) $post->getBody())->toBe('');
});

it('requests one token per App across calls', function () {
    $this->api->queue(
        FakeApi::token('default-token'), FakeApi::json(), FakeApi::json(),
        FakeApi::token('hpe-token'), FakeApi::json(), FakeApi::json(),
    );

    RouteConnex::run()->_id_('1')->get();
    RouteConnex::run()->_id_('2')->get();
    RouteConnex::app('hpe')->run()->_id_('3')->get();
    RouteConnex::app('hpe')->run()->_id_('4')->get();

    $authorization = array_map(fn ($request) => $request->getHeaderLine('Authorization'), $this->api->serviceRequests());

    expect($this->api->tokenRequests())->toHaveCount(2)
        ->and($authorization)->toBe(['Bearer default-token', 'Bearer default-token', 'Bearer hpe-token', 'Bearer hpe-token']);
});

it('sends requests with the Credentials and base URL of the App', function () {
    $this->api->queue(FakeApi::token(), FakeApi::run(), FakeApi::token(), FakeApi::run());

    RouteConnex::app('hpe')->hpeContentManager()->records()->get();
    RouteConnex::hpeContentManager()->records()->get();

    [$hpeToken, $defaultToken] = $this->api->tokenRequests();
    [$hpeCall, $defaultCall] = $this->api->serviceRequests();

    parse_str((string) $hpeToken->getBody(), $hpeForm);
    parse_str((string) $defaultToken->getBody(), $defaultForm);

    expect($hpeToken->getUri()->getHost())->toBe('hpe.example.com')
        ->and($hpeForm)->toMatchArray(['client_id' => 'hpe-id', 'client_secret' => 'hpe-secret'])
        ->and($hpeCall->getUri()->getHost())->toBe('hpe.example.com')
        ->and($defaultToken->getUri()->getHost())->toBe('app.routeconnex.com')
        ->and($defaultForm)->toMatchArray(['client_id' => 'default-id', 'client_secret' => 'default-secret'])
        ->and($defaultCall->getUri()->getHost())->toBe('app.routeconnex.com');
});

it('hands the App, Service, request, Meta and response to result() and returns what it returns', function (string $method, array $args, string $service, array $request) {
    $this->api->queue(FakeApi::token(), FakeApi::json(['data' => 'ok']));

    $endpoint = new RecordingFilesEndpoint(RouteConnex::app('default'));

    $returned = $endpoint->{$method}(...$args, meta: ['invoice_id' => 42]);

    expect($endpoint->received)->toHaveCount(1);

    $received = $endpoint->received[0];

    expect($returned->run_id)->toBe('marker')
        ->and($received['app']->name)->toBe('default')
        ->and($received['service'])->toBe($service)
        ->and($received['request'])->toBe($request)
        ->and($received['meta'])->toBe(['invoice_id' => 42])
        ->and($received['response']->json('data'))->toBe('ok');
})->with([
    'POST' => ['post', ['body' => ['file_name' => 'a.pdf']], 'POST microsoft-sharepoint/files', ['query' => [], 'body' => ['file_name' => 'a.pdf', '_meta_' => ['invoice_id' => 42]]]],
    'GET' => ['get', ['query' => ['site' => 'x']], 'GET microsoft-sharepoint/files', ['query' => ['site' => 'x', '_meta_' => '{"invoice_id":42}'], 'body' => []]],
]);
