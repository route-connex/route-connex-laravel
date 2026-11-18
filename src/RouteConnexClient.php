<?php

declare(strict_types=1);

namespace RouteConnex\Laravel;

use RouteConnex\Laravel\Apps\RouteConnexConfigApp;
use RouteConnex\RouteConnexSdkPhp\ErClient;

/**
 * One App and its SDK client.
 */
class RouteConnexClient
{
    public function __construct(
        protected RouteConnexConfigApp $app,
        protected ErClient $sdk,
    )
    {
        //
    }

    public function getApp(): RouteConnexConfigApp
    {
        return $this->app;
    }

    /**
     * The App's SDK client, for anything the package doesn't cover yet.
     * Calls made through it throw the SDK's and Guzzle's own exceptions.
     */
    public function sdk(): ErClient
    {
        return $this->sdk;
    }
}
