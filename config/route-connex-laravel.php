<?php

declare(strict_types=1);

use RouteConnex\Laravel\Models\RouteConnexApp;

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
    | Credentials (client ID and secret), may override the base URL, and has
    | the Callback token RouteConnex sends back in its Callbacks: a long
    | random value, also set in the App's callback configuration in RouteConnex.
    |
    */

    'apps' => [

        'default' => [
            'client_id' => env('ROUTE_CONNEX_CLIENT_ID'),
            'client_secret' => env('ROUTE_CONNEX_CLIENT_SECRET'),
            'callback_token' => env('ROUTE_CONNEX_CALLBACK_TOKEN'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | The App model, used to find model Apps again (for example when a
    | Callback arrives). Set the app's own model that implements
    | RouteConnexAppContract here, or a subclass of RouteConnexApp, such as
    | one with laravel-mongodb's DocumentModel trait.
    |
    */

    'models' => [

        'app' => RouteConnexApp::class,

    ],

];
