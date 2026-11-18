<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Endpoints;

use RouteConnex\Laravel\Endpoints\HpeContentManager\RouteConnexRecordsEndpoint;
use RouteConnex\Laravel\RouteConnexClient;

/**
 * `hpe-content-manager/*`: services on HPE Content Manager.
 */
class RouteConnexHpeContentManagerEndpoint
{
    public function __construct(
        protected RouteConnexClient $client,
    )
    {
        //
    }

    public function records(): RouteConnexRecordsEndpoint
    {
        return new RouteConnexRecordsEndpoint($this->client);
    }
}
