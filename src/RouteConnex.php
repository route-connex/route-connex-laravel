<?php

declare(strict_types=1);

namespace RouteConnex\Laravel;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\Container;
use RouteConnex\Laravel\Apps\RouteConnexConfigApp;
use RouteConnex\Laravel\Contracts\RouteConnexAppContract;
use RouteConnex\Laravel\Endpoints\RouteConnexHpeContentManagerEndpoint;
use RouteConnex\Laravel\Endpoints\RouteConnexMicrosoftSharepointEndpoint;
use RouteConnex\Laravel\Endpoints\RouteConnexRouteConnexEndpoint;
use RouteConnex\Laravel\Endpoints\RouteConnexRunEndpoint;
use RouteConnex\Laravel\Exceptions\RouteConnexConfigurationException;
use RouteConnex\RouteConnexSdkPhp\ErClient;

/**
 * The root of the RouteConnex facade: resolves Apps and keeps one client per
 * App. Calls that don't name an App go to the default one.
 */
class RouteConnex
{
    /**
     * Config Apps' clients, by App name.
     *
     * @var array<string, RouteConnexClient>
     */
    protected array $clients = [];

    /**
     * Other Apps' SDK clients, by `appCacheKey()`.
     *
     * @var array<string, ErClient>
     */
    protected array $sdkClients = [];

    public function __construct(
        protected Container $container,
        protected Config $config,
    ) {
        //
    }

    /**
     * The client of an App: a config App by name, or any App passed in, such
     * as a model loaded from the Tenant's database.
     *
     * @throws RouteConnexConfigurationException
     */
    public function app(string|RouteConnexAppContract $app): RouteConnexClient
    {
        if (is_string($app)) {
            return $this->clients[$app] ??= $this->makeClient($this->configApp($app));
        }

        return $this->contractClient($app);
    }

    /**
     * The default App's `route-connex/*` services.
     *
     * @throws RouteConnexConfigurationException
     */
    public function routeConnex(): RouteConnexRouteConnexEndpoint
    {
        return $this->defaultApp()->routeConnex();
    }

    /**
     * The default App's `run/*` services.
     *
     * @throws RouteConnexConfigurationException
     */
    public function run(): RouteConnexRunEndpoint
    {
        return $this->defaultApp()->run();
    }

    /**
     * The default App's `microsoft-sharepoint/*` services.
     *
     * @throws RouteConnexConfigurationException
     */
    public function microsoftSharepoint(): RouteConnexMicrosoftSharepointEndpoint
    {
        return $this->defaultApp()->microsoftSharepoint();
    }

    /**
     * The default App's `hpe-content-manager/*` services.
     *
     * @throws RouteConnexConfigurationException
     */
    public function hpeContentManager(): RouteConnexHpeContentManagerEndpoint
    {
        return $this->defaultApp()->hpeContentManager();
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

        if (! is_string($name) || $name === '') {
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

        if (! is_array($config)) {
            throw RouteConnexConfigurationException::unknownApp($name);
        }

        $baseUrl = $this->config->get('route-connex-laravel.base_url');

        return RouteConnexConfigApp::fromConfig($name, $config, is_string($baseUrl) ? $baseUrl : null);
    }

    /**
     * A client bound to the App passed in, around an SDK client shared by
     * every App with the same class, key and Credentials. The Credentials are
     * part of the cache key, so Tenants whose Apps share a connection name and
     * a key never share a client, and a rotated secret takes effect at once.
     *
     * @throws RouteConnexConfigurationException
     */
    protected function contractClient(RouteConnexAppContract $app): RouteConnexClient
    {
        if (blank($app->getRouteConnexClientId()) || blank($app->getRouteConnexClientSecret())) {
            throw RouteConnexConfigurationException::missingAppCredentials($app);
        }

        $sdk = $this->sdkClients[$this->appCacheKey($app)] ??= $this->container
            ->make(RouteConnexClientFactory::class)
            ->make($app);

        return new RouteConnexClient($app, $sdk);
    }

    protected function appCacheKey(RouteConnexAppContract $app): string
    {
        return $app::class.'|'.$app->getRouteConnexKey().'|'.hash('sha256', serialize([
            $app->getRouteConnexClientId(),
            $app->getRouteConnexClientSecret(),
            $app->getRouteConnexBaseUrl(),
        ]));
    }

    protected function makeClient(RouteConnexConfigApp $app): RouteConnexClient
    {
        $factory = $this->container->make(RouteConnexClientFactory::class);

        return new RouteConnexClient($app, $factory->make($app));
    }
}
