<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Endpoints\HpeContentManager;

use RouteConnex\Laravel\Endpoints\RouteConnexSystemEndpoint;
use RouteConnex\Laravel\Http\RouteConnexResponse;
use RouteConnex\RouteConnexSdkPhp\Endpoints\Endpoint;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

/**
 * `GET` and `POST hpe-content-manager/records`: list and create records.
 */
class RouteConnexRecordsEndpoint extends RouteConnexSystemEndpoint
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
        return $this->client->sdk()->hpeContentManager()->records();
    }
}
