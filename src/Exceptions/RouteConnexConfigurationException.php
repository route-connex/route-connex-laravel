<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Exceptions;

use RouteConnex\Laravel\Contracts\RouteConnexAppContract;

/**
 * The package is not configured for the call: no default App, an unknown
 * App or empty Credentials.
 */
class RouteConnexConfigurationException extends RouteConnexException
{
    public static function noDefaultApp(): self
    {
        return new self('No default RouteConnex App is configured. Set [route-connex-laravel.default] or ROUTE_CONNEX_APP.');
    }

    public static function unknownApp(string $name): self
    {
        return new self("RouteConnex App [{$name}] is not configured in [route-connex-laravel.apps].");
    }

    public static function missingCredentials(string $name): self
    {
        return new self("RouteConnex App [{$name}] has no client ID or client secret.");
    }

    /**
     * An App passed to `RouteConnex::app()` (a model, say) without Credentials.
     */
    public static function missingAppCredentials(RouteConnexAppContract $app): self
    {
        $key = $app->getRouteConnexKey();

        return self::missingCredentials($app::class.($key === null ? ' (unsaved)' : " #{$key}"));
    }
}
