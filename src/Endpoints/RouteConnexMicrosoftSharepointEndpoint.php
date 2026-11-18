<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Endpoints;

use RouteConnex\Laravel\Endpoints\MicrosoftSharepoint\RouteConnexDriveColumnsEndpoint;
use RouteConnex\Laravel\Endpoints\MicrosoftSharepoint\RouteConnexFilesEndpoint;
use RouteConnex\Laravel\RouteConnexClient;

/**
 * `microsoft-sharepoint/*`: services on Microsoft SharePoint.
 */
class RouteConnexMicrosoftSharepointEndpoint
{
    public function __construct(
        protected RouteConnexClient $client,
    ) {
        //
    }

    public function files(): RouteConnexFilesEndpoint
    {
        return new RouteConnexFilesEndpoint($this->client);
    }

    public function driveColumns(): RouteConnexDriveColumnsEndpoint
    {
        return new RouteConnexDriveColumnsEndpoint($this->client);
    }
}
