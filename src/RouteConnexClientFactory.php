<?php

declare(strict_types=1);

namespace RouteConnex\Laravel;

use RouteConnex\Laravel\Contracts\RouteConnexAppContract;
use RouteConnex\RouteConnexSdkPhp\ErClient;

/**
 * Builds the SDK client for an App. Bind a subclass in the container to
 * change how clients are built, for example to add a proxy.
 */
class RouteConnexClientFactory
{
    public function make(RouteConnexAppContract $app): ErClient
    {
        return $this->configure($this->newClient($app), $app);
    }

    protected function newClient(RouteConnexAppContract $app): ErClient
    {
        return new ErClient(
            clientId: (string) $app->getRouteConnexClientId(),
            clientSecret: (string) $app->getRouteConnexClientSecret(),
        );
    }

    protected function configure(ErClient $client, RouteConnexAppContract $app): ErClient
    {
        $baseUrl = $this->baseUrl($app);

        if ($baseUrl !== null) {
            $client->setBaseUrl($baseUrl);
        }

        return $client;
    }

    /**
     * The App's own base URL, else the global `base_url`, else `null` (the
     * SDK's default).
     */
    protected function baseUrl(RouteConnexAppContract $app): ?string
    {
        $baseUrl = $app->getRouteConnexBaseUrl();

        if ($baseUrl !== null && $baseUrl !== '') {
            return $baseUrl;
        }

        $global = config('route-connex-laravel.base_url');

        return is_string($global) && $global !== '' ? $global : null;
    }
}
