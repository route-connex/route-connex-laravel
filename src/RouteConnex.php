<?php

declare(strict_types=1);

namespace RouteConnex\Laravel;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\Container;
use RouteConnex\Laravel\Apps\RouteConnexConfigApp;
use RouteConnex\Laravel\Exceptions\RouteConnexConfigurationException;
use RouteConnex\RouteConnexSdkPhp\ErClient;

/**
 * The root of the RouteConnex facade: resolves Apps and keeps one client per
 * App. Calls that don't name an App go to the default one.
 */
class RouteConnex
{
    /**
     * @var array<string, RouteConnexClient>
     */
    protected array $clients = [];

    public function __construct(
        protected Container $container,
        protected Config $config,
    )
    {
        //
    }

    /**
     * The client of a config App, by name.
     *
     * @throws RouteConnexConfigurationException
     */
    public function app(string $name): RouteConnexClient
    {
        return $this->clients[$name] ??= $this->makeClient($this->configApp($name));
    }

    /**
     * The default App's SDK client, for anything the package doesn't cover yet.
     *
     * @throws RouteConnexConfigurationException
     */
    public function sdk(): ErClient
    {
        return $this->defaultApp()->sdk();
    }

    /**
     * @throws RouteConnexConfigurationException
     */
    protected function defaultApp(): RouteConnexClient
    {
        $name = $this->config->get('route-connex-laravel.default');

        if (!is_string($name) || $name === '') {
            throw RouteConnexConfigurationException::noDefaultApp();
        }

        return $this->app($name);
    }

    /**
     * @throws RouteConnexConfigurationException
     */
    protected function configApp(string $name): RouteConnexConfigApp
    {
        $config = $this->config->get("route-connex-laravel.apps.{$name}");

        if (!is_array($config)) {
            throw RouteConnexConfigurationException::unknownApp($name);
        }

        $baseUrl = $this->config->get('route-connex-laravel.base_url');

        return RouteConnexConfigApp::fromConfig($name, $config, is_string($baseUrl) ? $baseUrl : null);
    }

    protected function makeClient(RouteConnexConfigApp $app): RouteConnexClient
    {
        $factory = $this->container->make(RouteConnexClientFactory::class);

        return new RouteConnexClient($app, $factory->make($app));
    }
}
