<?php

declare(strict_types=1);

namespace RouteConnex\Laravel;

use ErrorException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
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
    public function __construct(
        protected RouteConnexConfigApp $app,
        protected ErClient $sdk,
    )
    {
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
     * @param array<string, mixed> $query
     * @param array<string, mixed> $body
     * @param array<string, string> $headers
     *
     * @throws RouteConnexAuthenticationException
     * @throws RouteConnexConnectionException
     * @throws RouteConnexRequestException
     */
    public function send(Endpoint $endpoint, HttpMethod $method, array $query = [], array $body = [], array $headers = []): RouteConnexResponse
    {
        if ($endpoint::requiresAuth($method)) {
            $this->authenticate();
        }

        try {
            return new RouteConnexResponse($endpoint->request($method, $query, $body, $headers));
        } catch (GuzzleException $e) {
            throw $this->wrap($e, authenticating: false);
        }
    }

    /**
     * Get a token if there is no valid one. It is a separate step so a failure
     * to authenticate can't be confused with an error answer to a Service call.
     *
     * @throws RouteConnexAuthenticationException
     * @throws RouteConnexConnectionException
     */
    protected function authenticate(): void
    {
        try {
            $this->sdk->authenticate();
        } catch (GuzzleException $e) {
            throw $this->wrap($e, authenticating: true);
        } catch (ErrorException|TypeError $e) {
            // The SDK reads the token response without checking it: a missing
            // field is a PHP warning (an ErrorException in Laravel) or a TypeError.
            throw RouteConnexAuthenticationException::noToken($e);
        }

        if (!$this->sdk->isAuthenticated()) {
            throw RouteConnexAuthenticationException::noToken();
        }
    }

    protected function wrap(GuzzleException $e, bool $authenticating): RouteConnexException
    {
        $response = $e instanceof RequestException ? $e->getResponse() : null;

        if ($response === null) {
            return new RouteConnexConnectionException($e);
        }

        $response = new RouteConnexResponse($response);

        return $authenticating
            ? RouteConnexAuthenticationException::rejected($response, $e)
            : new RouteConnexRequestException($response, $e);
    }
}
