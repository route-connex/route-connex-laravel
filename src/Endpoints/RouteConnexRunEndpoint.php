<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Endpoints;

use RouteConnex\Laravel\Endpoints\Run\RouteConnexRunIdEndpoint;
use RouteConnex\Laravel\RouteConnexClient;

/**
 * `run/*`: Runs that already exist in RouteConnex.
 */
class RouteConnexRunEndpoint
{
    public function __construct(
        protected RouteConnexClient $client,
    ) {
        //
    }

    public function _id_(string $id): RouteConnexRunIdEndpoint
    {
        return new RouteConnexRunIdEndpoint($this->client, $id);
    }
}
