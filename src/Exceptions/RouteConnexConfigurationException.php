<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Exceptions;

use RouteConnex\Laravel\Contracts\RouteConnexAppContract;

/**
 * The package is not configured for the call: no default App, an unknown
 * App, empty Credentials, or a Run whose App is invalid or gone.
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

    /**
     * An App with no key (an unsaved model, say) calling a System Service:
     * its Run couldn't be stored, so the request isn't sent.
     */
    public static function appWithoutKey(RouteConnexAppContract $app): self
    {
        return new self('RouteConnex App ['.$app::class.'] has no key, so its Runs can\'t be stored. Save it before calling a System Service.');
    }

    /**
     * A Run saved with both `app_name` and `app_id`, or with neither.
     */
    public static function invalidRunApp(): self
    {
        return new self('A RouteConnex Run needs exactly one of [app_name] and [app_id].');
    }

    /**
     * The model App a Run belongs to can't be found through `models.app`.
     */
    public static function missingRunApp(string $model, string $id): self
    {
        return new self("RouteConnex App [{$model} #{$id}] of the Run can't be found.");
    }
}
