<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Endpoints\RouteConnex;

use RouteConnex\Laravel\Endpoints\RouteConnexEndpoint;
use RouteConnex\Laravel\Http\RouteConnexResponse;
use RouteConnex\RouteConnexSdkPhp\Endpoints\Endpoint;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

/**
 * `GET route-connex/about`: information about the RouteConnex API. It needs
 * no token.
 */
class RouteConnexAboutEndpoint extends RouteConnexEndpoint
{
    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $headers
     */
    public function get(array $query = [], array $body = [], array $headers = []): RouteConnexResponse
    {
        return $this->send(HttpMethod::GET, $query, $body, $headers);
    }

    protected function sdkEndpoint(): Endpoint
    {
        return $this->client->sdk()->routeConnex()->about();
    }
}
