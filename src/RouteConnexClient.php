<?php

declare(strict_types=1);

namespace RouteConnex\Laravel;

use RouteConnex\Laravel\Apps\RouteConnexConfigApp;
use RouteConnex\Laravel\Endpoints\RouteConnexHpeContentManagerEndpoint;
use RouteConnex\Laravel\Endpoints\RouteConnexMicrosoftSharepointEndpoint;
use RouteConnex\Laravel\Endpoints\RouteConnexRouteConnexEndpoint;
use RouteConnex\Laravel\Endpoints\RouteConnexRunEndpoint;
use RouteConnex\Laravel\Http\RouteConnexResponse;
use RouteConnex\RouteConnexSdkPhp\Endpoints\Endpoint;
use RouteConnex\RouteConnexSdkPhp\ErClient;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

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
     * request the package makes goes through here.
     *
     * @param array<string, mixed> $query
     * @param array<string, mixed> $body
     * @param array<string, string> $headers
     */
    public function send(Endpoint $endpoint, HttpMethod $method, array $query = [], array $body = [], array $headers = []): RouteConnexResponse
    {
        return new RouteConnexResponse($endpoint->request($method, $query, $body, $headers));
    }
}
