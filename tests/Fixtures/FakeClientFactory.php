<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Tests\Fixtures;

use RouteConnex\Laravel\Apps\RouteConnexConfigApp;
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

    protected function newClient(RouteConnexConfigApp $app): ErClient
    {
        return (new FakeErClient(clientId: $app->clientId, clientSecret: $app->clientSecret))
            ->useFakeApi($this->api);
    }
}
