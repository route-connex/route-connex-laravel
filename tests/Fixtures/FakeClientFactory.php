<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Tests\Fixtures;

use RouteConnex\Laravel\Contracts\RouteConnexAppContract;
use RouteConnex\Laravel\RouteConnexClientFactory;
use RouteConnex\RouteConnexSdkPhp\ErClient;

/**
 * Builds SDK clients that talk to a `FakeApi`.
 */
final class FakeClientFactory extends RouteConnexClientFactory
{
    public function __construct(
        private readonly FakeApi $api,
    ) {
        //
    }

    protected function newClient(RouteConnexAppContract $app): ErClient
    {
        return (new FakeErClient(clientId: (string) $app->getRouteConnexClientId(), clientSecret: (string) $app->getRouteConnexClientSecret()))
            ->useFakeApi($this->api);
    }
}
