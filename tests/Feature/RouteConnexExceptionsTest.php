<?php

declare(strict_types=1);

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use RouteConnex\Laravel\Exceptions\RouteConnexAuthenticationException;
use RouteConnex\Laravel\Exceptions\RouteConnexConfigurationException;
use RouteConnex\Laravel\Exceptions\RouteConnexConnectionException;
use RouteConnex\Laravel\Exceptions\RouteConnexException;
use RouteConnex\Laravel\Exceptions\RouteConnexRequestException;
use RouteConnex\Laravel\Facades\RouteConnex;
use RouteConnex\Laravel\Http\RouteConnexResponse;
use RouteConnex\Laravel\Tests\Fixtures\FakeApi;

function connectionFailure(): ConnectException
{
    return new ConnectException('cURL error 7: Could not connect', new Request('GET', 'https://app.routeconnex.com/api'));
}

/**
 * Run a call and return the exception it throws.
 */
function thrownBy(Closure $call): Throwable
{
    try {
        $call();
    } catch (Throwable $e) {
        return $e;
    }

    throw new RuntimeException('The call did not throw.');
}

beforeEach(function () {
    config()->set('route-connex-laravel.apps', [
        'default' => ['client_id' => 'default-id', 'client_secret' => 'default-secret'],
    ]);

    $this->api = fakeRouteConnexApi();
});

describe('getting the token', function () {
    it('throws an authentication exception when the API rejects the Credentials', function (int $status) {
        $this->api->queue(FakeApi::json(['error' => 'invalid_client'], $status));

        $e = thrownBy(fn () => RouteConnex::routeConnex()->testAuth()->get());

        expect($e)->toBeInstanceOf(RouteConnexAuthenticationException::class)
            ->and($e->response())->toBeInstanceOf(RouteConnexResponse::class)
            ->and($e->response()->status())->toBe($status)
            ->and($e->response()->json('error'))->toBe('invalid_client')
            ->and($e->getPrevious())->toBeInstanceOf(in_array($status, [401, 400]) ? ClientException::class : ServerException::class)
            ->and($this->api->serviceRequests())->toBeEmpty();
    })->with([401, 400, 500]);

    it('throws an authentication exception when the token response has no token', function (Response $answer) {
        $this->api->queue($answer);

        $e = thrownBy(fn () => RouteConnex::routeConnex()->testAuth()->get());

        expect($e)->toBeInstanceOf(RouteConnexAuthenticationException::class)
            ->and($e->response())->toBeNull()
            ->and($e->getPrevious())->toBeInstanceOf(Throwable::class)
            ->and($this->api->serviceRequests())->toBeEmpty();
    })->with([
        'empty JSON' => fn () => FakeApi::json(),
        'not JSON' => fn () => new Response(200, [], 'not json'),
        'no expiry' => fn () => FakeApi::json(['access_token' => 'abc', 'token_type' => 'Bearer']),
    ]);

    it('throws an authentication exception when the token has already expired', function () {
        $this->api->queue(FakeApi::json(['access_token' => 'abc', 'token_type' => 'Bearer', 'expires_in' => 0]));

        expect(fn () => RouteConnex::routeConnex()->testAuth()->get())
            ->toThrow(RouteConnexAuthenticationException::class, 'issued no usable access token');

        expect($this->api->serviceRequests())->toBeEmpty();
    });

    it('words the message by the status of the token answer', function (int $status) {
        $this->api->queue(FakeApi::json([], $status));

        expect(fn () => RouteConnex::routeConnex()->testAuth()->get())
            ->toThrow(RouteConnexAuthenticationException::class, "The RouteConnex API answered the token request with status {$status}.");
    })->with([401, 500]);

    it('renews a token that expires within the margin before the next call', function () {
        $this->api->queue(
            FakeApi::json(['access_token' => 'old', 'token_type' => 'Bearer', 'expires_in' => 10]),
            FakeApi::json(),
            FakeApi::token('new'),
            FakeApi::json(),
        );

        RouteConnex::routeConnex()->testAuth()->get();
        RouteConnex::routeConnex()->testAuth()->get();

        [$first, $second] = $this->api->serviceRequests();

        expect($this->api->tokenRequests())->toHaveCount(2)
            ->and($first->getHeaderLine('Authorization'))->toBe('Bearer old')
            ->and($second->getHeaderLine('Authorization'))->toBe('Bearer new');
    });

    it('reuses a token well within its lifetime', function () {
        $this->api->queue(FakeApi::token('abc'), FakeApi::json(), FakeApi::json());

        RouteConnex::routeConnex()->testAuth()->get();
        RouteConnex::routeConnex()->testAuth()->get();

        expect($this->api->tokenRequests())->toHaveCount(1);
    });

    it('throws a connection exception when the API can\'t be reached', function () {
        $this->api->queue($failure = connectionFailure());

        $e = thrownBy(fn () => RouteConnex::routeConnex()->testAuth()->get());

        expect($e)->toBeInstanceOf(RouteConnexConnectionException::class)
            ->and($e->getPrevious())->toBe($failure)
            ->and($e->getMessage())->toContain('Could not connect')
            ->and($this->api->serviceRequests())->toBeEmpty();
    });

    it('asks for a token again after a failure', function () {
        $this->api->queue(FakeApi::json([], 401), FakeApi::token(), FakeApi::json(['data' => 'ok']));

        expect(fn () => RouteConnex::routeConnex()->testAuth()->get())->toThrow(RouteConnexAuthenticationException::class);

        $response = RouteConnex::routeConnex()->testAuth()->get();

        expect($response->json('data'))->toBe('ok')
            ->and($this->api->tokenRequests())->toHaveCount(2);
    });
});

describe('calling a Service', function () {
    it('throws a request exception with the API\'s error answer', function (int $status, string $class) {
        $this->api->queue(FakeApi::token(), FakeApi::json(['message' => 'Something is wrong'], $status));

        $e = thrownBy(fn () => RouteConnex::microsoftSharepoint()->files()->post(['site_name' => 'Finance']));

        expect($e)->toBeInstanceOf(RouteConnexRequestException::class)
            ->and($e->getMessage())->toBe("The RouteConnex API answered with status {$status}.")
            ->and($e->response())->toBeInstanceOf(RouteConnexResponse::class)
            ->and($e->response()->status())->toBe($status)
            ->and($e->response()->json('message'))->toBe('Something is wrong')
            ->and($e->response()->failed())->toBeTrue()
            ->and($e->getPrevious())->toBeInstanceOf($class);
    })->with([
        'bad request' => [400, ClientException::class],
        'not found' => [404, ClientException::class],
        'unprocessable' => [422, ClientException::class],
        'server error' => [500, ServerException::class],
        'unavailable' => [503, ServerException::class],
    ]);

    it('throws a request exception, not an authentication one, for a 401 or 403 after a token was issued', function (int $status) {
        $this->api->queue(FakeApi::token(), FakeApi::json(['message' => 'App is blocked'], $status));

        $e = thrownBy(fn () => RouteConnex::hpeContentManager()->records()->get());

        expect($e)->toBeInstanceOf(RouteConnexRequestException::class)
            ->and($e)->not->toBeInstanceOf(RouteConnexAuthenticationException::class)
            ->and($e->response()->status())->toBe($status)
            ->and($e->response()->json('message'))->toBe('App is blocked')
            ->and($this->api->tokenRequests())->toHaveCount(1);
    })->with([401, 403]);

    it('throws a request exception for an endpoint that needs no token', function () {
        $this->api->queue(FakeApi::json(['message' => 'Down for maintenance'], 503));

        $e = thrownBy(fn () => RouteConnex::routeConnex()->about()->get());

        expect($e)->toBeInstanceOf(RouteConnexRequestException::class)
            ->and($e->response()->json('message'))->toBe('Down for maintenance')
            ->and($this->api->tokenRequests())->toBeEmpty();
    });

    it('throws a connection exception when the API can\'t be reached', function () {
        $this->api->queue(FakeApi::token(), $failure = connectionFailure());

        $e = thrownBy(fn () => RouteConnex::run()->_id_('run-1')->get());

        expect($e)->toBeInstanceOf(RouteConnexConnectionException::class)
            ->and($e->getPrevious())->toBe($failure);
    });

    it('throws a connection exception for an endpoint that needs no token', function () {
        $this->api->queue(connectionFailure());

        expect(fn () => RouteConnex::routeConnex()->about()->get())->toThrow(RouteConnexConnectionException::class);
    });

    it('keeps the token after a failed Service call', function () {
        $this->api->queue(FakeApi::token(), FakeApi::json([], 500), FakeApi::json(['data' => 'ok']));

        expect(fn () => RouteConnex::run()->_id_('run-1')->get())->toThrow(RouteConnexRequestException::class);

        expect(RouteConnex::run()->_id_('run-1')->get()->json('data'))->toBe('ok')
            ->and($this->api->tokenRequests())->toHaveCount(1);
    });
});

describe('request data that can\'t be encoded', function () {
    it('throws an InvalidArgumentException for Meta that can\'t be encoded on GET, without sending a request', function () {
        $this->api->queue(FakeApi::token());

        $e = thrownBy(fn () => RouteConnex::hpeContentManager()->records()->get(meta: ['name' => "\xB1\x31"]));

        expect($e)->toBeInstanceOf(InvalidArgumentException::class)
            ->and($e)->not->toBeInstanceOf(RouteConnexException::class)
            ->and($e->getPrevious())->toBeInstanceOf(JsonException::class)
            ->and($this->api->serviceRequests())->toBeEmpty();
    });

    it('throws an InvalidArgumentException for a POST body that can\'t be encoded', function () {
        $this->api->queue(FakeApi::token());

        $e = thrownBy(fn () => RouteConnex::microsoftSharepoint()->files()->post(['name' => "\xB1\x31"]));

        expect($e)->toBeInstanceOf(InvalidArgumentException::class)
            ->and($e)->not->toBeInstanceOf(RouteConnexException::class)
            ->and($this->api->serviceRequests())->toBeEmpty();
    });
});

it('makes every package exception a RouteConnexException', function (string $class) {
    expect(is_subclass_of($class, RouteConnexException::class))->toBeTrue();
})->with([
    RouteConnexAuthenticationException::class,
    RouteConnexConfigurationException::class,
    RouteConnexConnectionException::class,
    RouteConnexRequestException::class,
]);

it('lets one catch handle every failure of a call', function (string $failure) {
    match ($failure) {
        'rejected Credentials' => $this->api->queue(FakeApi::json([], 401)),
        'no connection' => $this->api->queue(connectionFailure()),
        'error answer' => $this->api->queue(FakeApi::token(), FakeApi::json([], 500)),
    };

    expect(fn () => RouteConnex::microsoftSharepoint()->files()->get())->toThrow(RouteConnexException::class);
})->with(['rejected Credentials', 'no connection', 'error answer']);

it('throws the SDK\'s own exceptions through sdk()', function () {
    $this->api->queue(FakeApi::json(['message' => 'Down for maintenance'], 503));

    $e = thrownBy(fn () => RouteConnex::sdk()->routeConnex()->about()->get());

    expect($e)->toBeInstanceOf(ServerException::class)
        ->and($e)->not->toBeInstanceOf(RouteConnexException::class);
});
