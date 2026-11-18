<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Endpoints\MicrosoftSharepoint;

use RouteConnex\Laravel\Endpoints\RouteConnexSystemEndpoint;
use RouteConnex\Laravel\Http\RouteConnexResponse;
use RouteConnex\RouteConnexSdkPhp\Endpoints\Endpoint;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

/**
 * `GET` and `POST microsoft-sharepoint/files`: list and upload files.
 */
class RouteConnexFilesEndpoint extends RouteConnexSystemEndpoint
{
    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $headers
     * @param  array<string, mixed>  $meta
     */
    public function get(array $query = [], array $body = [], array $headers = [], array $meta = []): RouteConnexResponse
    {
        return $this->call(HttpMethod::GET, $query, $body, $headers, $meta);
    }

    /**
     * @param  array<string, mixed>  $body
     * @param  array<string, mixed>  $query
     * @param  array<string, string>  $headers
     * @param  array<string, mixed>  $meta
     */
    public function post(array $body = [], array $query = [], array $headers = [], array $meta = []): RouteConnexResponse
    {
        return $this->call(HttpMethod::POST, $query, $body, $headers, $meta);
    }

    protected function sdkEndpoint(): Endpoint
    {
        return $this->client->sdk()->microsoftSharepoint()->files();
    }
}
