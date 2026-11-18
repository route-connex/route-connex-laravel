<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Endpoints;

use RouteConnex\Laravel\Http\RouteConnexResponse;
use RouteConnex\Laravel\RouteConnexClient;
use RouteConnex\RouteConnexSdkPhp\Endpoints\Endpoint;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

/**
 * A callable endpoint: mirrors one SDK endpoint and sends its requests
 * through the App's client.
 */
abstract class RouteConnexEndpoint
{
    public function __construct(
        protected RouteConnexClient $client,
    ) {
        //
    }

    /**
     * The SDK endpoint this one mirrors.
     */
    abstract protected function sdkEndpoint(): Endpoint;

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $headers
     */
    protected function send(HttpMethod $method, array $query, array $body, array $headers): RouteConnexResponse
    {
        return $this->client->send($this->sdkEndpoint(), $method, $query, $body, $headers);
    }
}
