# Overridable models and classes

**Status:** planned
**Target version:** 0.x

## Summary

The package's models are set in config, so a consumer app can extend them, and the package's other replaceable classes are resolved from the container, so a consumer app can swap them for its own subclasses. One of them, `RouteConnexTenancy`, tells the package how the app's Tenants work. A complex app changes behavior this way instead of forking the package.

## Motivation

Consumer apps differ: multi-tenant setups, database connections chosen at runtime, tables that don't follow Laravel's naming conventions, Apps in one database and Runs in another, extra columns, their own integration structure. The vision promises defaults that work and everything overridable. This feature defines how, in one consistent way, and it's what the later Tenancy presets build on.

## Developer experience

Models, in config:

```php
// config/route-connex-laravel.php
'models' => [
    'app' => \App\Models\RouteConnexApp::class,
    'run' => \App\Models\RouteConnexRun::class,
],
```

```php
namespace App\Models;

class RouteConnexRun extends \RouteConnex\Laravel\Models\RouteConnexRun
{
    protected $connection = 'runs';

    protected $table = 'integration_runs';
}
```

Other classes, in a service provider:

```php
use RouteConnex\Laravel\Callbacks\RouteConnexCallbackHandler;

public function register(): void
{
    $this->app->bind(RouteConnexCallbackHandler::class, TenantCallbackHandler::class);
}
```

```php
class TenantCallbackHandler extends RouteConnexCallbackHandler
{
    // override only the step that differs
}
```

An app with its own tenancy binds its own `RouteConnexTenancy`, so Artisan commands and scheduled work can run for a Tenant:

```php
use RouteConnex\Laravel\Tenancy\RouteConnexTenancy;

class AppTenancy extends RouteConnexTenancy
{
    public function enabled(): bool
    {
        return true;
    }

    public function current(): mixed
    {
        return Tenant::current();
    }

    public function findTenant(string $id): mixed
    {
        return Tenant::where('slug', $id)->first()
            ?? throw new RouteConnexConfigurationException("Unknown Tenant [{$id}].");
    }

    public function run(mixed $tenant, Closure $callback): mixed
    {
        return $tenant->execute($callback); // the app's own way to switch Tenants
    }

    public function forEachTenant(Closure $callback): void
    {
        Tenant::each(fn (Tenant $tenant) => $this->run($tenant, $callback));
    }
}

// in a service provider's register()
$this->app->bind(RouteConnexTenancy::class, AppTenancy::class);
```

## Scope

- **Models in config:** `models.app` (default `RouteConnexApp`, see [Multiple Apps](apps.md)) and `models.run` (default `RouteConnexRun`, see [`RouteConnexRun` tracking](run-tracking.md)). The package always uses the configured class, never the default one directly: in queries, relations, Callbacks and the purge. A subclass can change the table, the connection, casts and fields, or add laravel-mongodb's `DocumentModel` trait.
- **Classes from the container:** each is resolved from the container, so binding a subclass replaces it everywhere:
  - `RouteConnexClientFactory`: builds the SDK client for an App, for example to add a proxy or a token store
  - `RouteConnexRunRecorder`: stores the `RouteConnexRun` after the API call, for example to fill extra columns
  - `RouteConnexCallbackHandler`: runs the Callback steps (see [Callbacks as events](callbacks.md)), for example to add checks
  - `RouteConnexCallbackUrlGenerator`: builds the callback URL of an App, for example on a Tenant's own subdomain
  - `RouteConnexTenancy`: how the consumer app's Tenants work, for code that runs without a request to set the Tenant (see below)
- Each of the first four classes has small `protected` methods per step, so a subclass overrides one step instead of copying the whole class. `RouteConnexTenancy` is a small set of public methods, each overridable.
- **`RouteConnexTenancy`** (`RouteConnex\Laravel\Tenancy`). The shipped class means no tenancy, so single-tenant apps change nothing:
  - `enabled(): bool`: `false` by default. When it's `true`, the Artisan commands ask for a Tenant instead of guessing.
  - `current(): mixed`: the Tenant in the current context, or `null`. `RouteConnexCallbackUrlGenerator` can use it to build a Tenant's URL.
  - `findTenant(string $id): mixed`: resolves the `--tenant` option of the Artisan commands. An unknown ID throws `RouteConnexConfigurationException`.
  - `run(mixed $tenant, Closure $callback): mixed`: runs the callback in that Tenant's context and restores the previous context afterwards.
  - `forEachTenant(Closure $callback): void`: runs the callback once per Tenant, or once with no Tenant when tenancy is off, for example to purge payloads.
  - A Tenant is whatever the consumer app uses for one (a model, an ID); the package only passes it back to these methods.
  - The package uses it only where nothing else sets the Tenant: Artisan commands and scheduled work. Calls through the facade still take the App the consumer app passes in (see [Multiple Apps](apps.md)), and Callbacks get their Tenant from the app's route group (see [Callbacks as events](callbacks.md)).
- The `stancl/tenancy` preset (a Next theme) is a shipped subclass of `RouteConnexTenancy` that the `tenancy` config section binds. An app with its own tenancy binds its own subclass instead.
- The bundled Laravel Boost skill shows how to extend a model, bind a class and implement `RouteConnexTenancy`.

## Non-goals

- **No `classes` map in config.** Only models are set in config, because Eloquent relations and queries need their class names; everything else uses the container, Laravel's own way.
- **Not every class is a public extension point.** Only the classes listed above are; the rest are internal and can change between minor versions before 1.0.
- **No morph map from the package.** The App model is always found through `models.app`, so the package stores no model class names.

## Database & compatibility notes

- **Databases:** a model subclass can move its table to any supported connection; Apps and Runs can be on different databases (for example MySQL and MongoDB). On MongoDB the subclass adds laravel-mongodb's `DocumentModel` trait; `mongodb/laravel-mongodb` stays a Composer suggestion.
- **Migrations:** an app that changes a table name or connection publishes the migrations (`route-connex-laravel-migrations`) and edits them, or writes its own.
- **Tenancy:** `run()` must restore the previous context even when the callback throws, so a long-running worker (queues, Octane) never stays in the wrong Tenant.
- **Laravel 12 and 13:** container bindings, `$connection` and `$table` overrides exist in both.

## Open questions

None at the moment.

## Related

- Roadmap: [Overridable models and classes](../roadmap.md#now)
- Vision: "Everything is overridable" and "Defaults that work, everything overridable" ([vision.md](../vision.md))
- Features: [Laravel layer over the SDK](sdk-layer.md), [Multiple Apps](apps.md), [`RouteConnexRun` tracking](run-tracking.md), [Callbacks as events](callbacks.md)
- Decisions: [0005: Models in config, other classes bound in the container](../decisions/0005-models-in-config-classes-in-container.md), [0006: MongoDB through the consumer app's model subclass](../decisions/0006-mongodb-through-app-subclass.md), [0010: A replaceable tenancy class](../decisions/0010-replaceable-tenancy-class.md)
