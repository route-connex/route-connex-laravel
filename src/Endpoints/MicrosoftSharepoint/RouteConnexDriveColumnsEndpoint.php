<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Endpoints\MicrosoftSharepoint;

use RouteConnex\Laravel\Endpoints\RouteConnexSystemEndpoint;
use RouteConnex\Laravel\Http\RouteConnexResponse;
use RouteConnex\RouteConnexSdkPhp\Endpoints\Endpoint;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

/**
 * `GET microsoft-sharepoint/drive-columns`: list a drive's columns.
 */
class RouteConnexDriveColumnsEndpoint extends RouteConnexSystemEndpoint
{
    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $body
     * @param array<string, string> $headers
     * @param array<string, mixed> $meta
     */
    public function get(array $query = [], array $body = [], array $headers = [], array $meta = []): RouteConnexResponse
    {
        return $this->call(HttpMethod::GET, $query, $body, $headers, $meta);
    }

    protected function sdkEndpoint(): Endpoint
    {
        return $this->client->sdk()->microsoftSharepoint()->driveColumns();
    }
}
