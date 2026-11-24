<?php

declare(strict_types=1);

namespace RouteConnex\Laravel;

use ErrorException;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\InvalidArgumentException as GuzzleInvalidArgumentException;
use InvalidArgumentException;
use RouteConnex\Laravel\Apps\RouteConnexConfigApp;
use RouteConnex\Laravel\Endpoints\RouteConnexHpeContentManagerEndpoint;
use RouteConnex\Laravel\Endpoints\RouteConnexMicrosoftSharepointEndpoint;
use RouteConnex\Laravel\Endpoints\RouteConnexRouteConnexEndpoint;
use RouteConnex\Laravel\Endpoints\RouteConnexRunEndpoint;
use RouteConnex\Laravel\Exceptions\RouteConnexAuthenticationException;
use RouteConnex\Laravel\Exceptions\RouteConnexConnectionException;
use RouteConnex\Laravel\Exceptions\RouteConnexException;
use RouteConnex\Laravel\Exceptions\RouteConnexRequestException;
use RouteConnex\Laravel\Http\RouteConnexResponse;
use RouteConnex\RouteConnexSdkPhp\Endpoints\Endpoint;
use RouteConnex\RouteConnexSdkPhp\ErClient;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;
use TypeError;

/**
 * One App and its SDK client.
 */
class RouteConnexClient
{
    /**
     * Seconds before expiry from which a token is renewed instead of reused,
     * so it can't expire between getting it and the Service receiving it.
     */
    protected int $tokenRefreshMargin = 30;

    public function __construct(
        protected RouteConnexConfigApp $app,
        protected ErClient $sdk,
    ) {
        //
    }

    public function getApp(): RouteConnexConfigApp
    {
        return $this->app;
    }

    /**
     * The App's SDK client, for anything the package doesn't cover yet.
     * Calls made through it throw the SDK's and Guzzle's own exceptions.
     */
    public function sdk(): ErClient
    {
        return $this->sdk;
    }

    public function routeConnex(): RouteConnexRouteConnexEndpoint
    {
        return new RouteConnexRouteConnexEndpoint($this);
    }

    public function run(): RouteConnexRunEndpoint
    {
        return new RouteConnexRunEndpoint($this);
    }

    public function microsoftSharepoint(): RouteConnexMicrosoftSharepointEndpoint
    {
        return new RouteConnexMicrosoftSharepointEndpoint($this);
    }

    public function hpeContentManager(): RouteConnexHpeContentManagerEndpoint
    {
        return new RouteConnexHpeContentManagerEndpoint($this);
    }

    /**
     * Send a request to an SDK endpoint with this App's SDK client. Every
     * request the package makes goes through here: it gets a token first when
     * the endpoint needs one, and wraps the SDK's and Guzzle's exceptions in
     * package exceptions.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $headers
     *
     * @throws RouteConnexAuthenticationException
     * @throws RouteConnexConnectionException
     * @throws RouteConnexRequestException
     * @throws InvalidArgumentException When the request data can't be encoded (invalid UTF-8 in the body, say)
     */
    public function send(Endpoint $endpoint, HttpMethod $method, array $query = [], array $body = [], array $headers = []): RouteConnexResponse
    {
        if ($endpoint::requiresAuth($method)) {
            $this->authenticate();
        }

        try {
            return new RouteConnexResponse($endpoint->request($method, $query, $body, $headers));
        } catch (GuzzleInvalidArgumentException $e) {
            // The caller's data can't be encoded: not a failure of the API.
            throw $e;
        } catch (GuzzleException $e) {
            throw $this->wrap($e, authenticating: false);
        }
    }

    /**
     * Get a token if there is no valid one, or the one held expires within the
     * refresh margin. It is a separate step so a failure
     * to authenticate can't be confused with an error answer to a Service call.
     *
     * @throws RouteConnexAuthenticationException
     * @throws RouteConnexConnectionException
     */
    protected function authenticate(): void
    {
        try {
            $expiresIn = $this->sdk->getTokenExpiresIn();

            $this->sdk->authenticate(forceNewToken: $expiresIn !== null && $expiresIn < $this->tokenRefreshMargin);
        } catch (GuzzleException $e) {
            throw $this->wrap($e, authenticating: true);
        } catch (ErrorException|TypeError $e) {
            // The SDK reads the token response without checking it: a missing
            // field is a PHP warning (an ErrorException in Laravel) or a TypeError.
            throw RouteConnexAuthenticationException::noToken($e);
        }

        if (! $this->sdk->isAuthenticated()) {
            throw RouteConnexAuthenticationException::noToken();
        }
    }

    /**
     * Wrap a Guzzle failure. Only a 4xx or 5xx answer (a `BadResponseException`)
     * is an error answer from the API; every other failure, such as no
     * connection, too many redirects or a transfer that broke, is a connection
     * failure. Guzzle's `InvalidArgumentException` never gets here: `send()`
     * rethrows it, as it is the caller's data and not the API.
     */
    protected function wrap(GuzzleException $e, bool $authenticating): RouteConnexException
    {
        $response = $e instanceof BadResponseException ? $e->getResponse() : null;

        if ($response === null) {
            return new RouteConnexConnectionException($e);
        }

        $response = new RouteConnexResponse($response);

        return $authenticating
            ? RouteConnexAuthenticationException::rejected($response, $e)
            : new RouteConnexRequestException($response, $e);
    }
}
