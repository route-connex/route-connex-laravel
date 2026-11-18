<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Endpoints;

use RouteConnex\Laravel\Endpoints\RouteConnex\RouteConnexAboutEndpoint;
use RouteConnex\Laravel\Endpoints\RouteConnex\RouteConnexTestAuthEndpoint;
use RouteConnex\Laravel\RouteConnexClient;

/**
 * `route-connex/*`: services that answer straight away.
 */
class RouteConnexRouteConnexEndpoint
{
    public function __construct(
        protected RouteConnexClient $client,
    ) {
        //
    }

    public function about(): RouteConnexAboutEndpoint
    {
        return new RouteConnexAboutEndpoint($this->client);
    }

    public function testAuth(): RouteConnexTestAuthEndpoint
    {
        return new RouteConnexTestAuthEndpoint($this->client);
    }
}
