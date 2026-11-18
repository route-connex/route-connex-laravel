<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Endpoints\Run;

use RouteConnex\Laravel\Endpoints\RouteConnexEndpoint;
use RouteConnex\Laravel\Http\RouteConnexResponse;
use RouteConnex\Laravel\RouteConnexClient;
use RouteConnex\RouteConnexSdkPhp\Endpoints\Endpoint;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

/**
 * `GET run/{id}`: a Run's current state in RouteConnex.
 */
class RouteConnexRunIdEndpoint extends RouteConnexEndpoint
{
    public function __construct(
        RouteConnexClient $client,
        protected string $runId,
    )
    {
        parent::__construct($client);
    }

    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $body
     * @param array<string, string> $headers
     */
    public function get(array $query = [], array $body = [], array $headers = []): RouteConnexResponse
    {
        return $this->send(HttpMethod::GET, $query, $body, $headers);
    }

    protected function sdkEndpoint(): Endpoint
    {
        return $this->client->sdk()->run()->_id_($this->runId);
    }
}
