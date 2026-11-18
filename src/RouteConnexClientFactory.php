<?php

declare(strict_types=1);

namespace RouteConnex\Laravel;

use RouteConnex\Laravel\Apps\RouteConnexConfigApp;
use RouteConnex\RouteConnexSdkPhp\ErClient;

/**
 * Builds the SDK client for an App. Bind a subclass in the container to
 * change how clients are built, for example to add a proxy.
 */
class RouteConnexClientFactory
{
    public function make(RouteConnexConfigApp $app): ErClient
    {
        return $this->configure($this->newClient($app), $app);
    }

    protected function newClient(RouteConnexConfigApp $app): ErClient
    {
        return new ErClient(
            clientId: $app->clientId,
            clientSecret: $app->clientSecret,
        );
    }

    protected function configure(ErClient $client, RouteConnexConfigApp $app): ErClient
    {
        if ($app->baseUrl !== null) {
            $client->setBaseUrl($app->baseUrl);
        }

        return $client;
    }
}
