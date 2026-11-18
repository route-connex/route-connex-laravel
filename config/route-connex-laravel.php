<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | RouteConnex API base URL
    |--------------------------------------------------------------------------
    |
    | The URL of the RouteConnex API. Every App uses it unless the App sets
    | its own `base_url`.
    |
    */

    'base_url' => env('ROUTE_CONNEX_BASE_URL', 'https://app.routeconnex.com/api'),

    /*
    |--------------------------------------------------------------------------
    | Default App
    |--------------------------------------------------------------------------
    |
    | The App used by calls that don't name one with RouteConnex::app().
    |
    */

    'default' => env('ROUTE_CONNEX_APP', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Apps
    |--------------------------------------------------------------------------
    |
    | The RouteConnex API clients this app uses, by name. Each App has its own
    | Credentials (client ID and secret) and may override the base URL.
    |
    */

    'apps' => [

        'default' => [
            'client_id' => env('ROUTE_CONNEX_CLIENT_ID'),
            'client_secret' => env('ROUTE_CONNEX_CLIENT_SECRET'),
        ],

    ],

];
