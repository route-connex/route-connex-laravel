# Overridable models and classes

**Status:** planned
**Target version:** 0.x

## Summary

The package's models are set in config, so a consumer app can extend them, and the package's other replaceable classes are resolved from the container, so a consumer app can swap them for its own subclasses. A complex app changes behavior this way instead of forking the package.

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

## Scope

- **Models in config:** `models.app` (default `RouteConnexApp`, see [Multiple Apps](apps.md)) and `models.run` (default `RouteConnexRun`, see [`RouteConnexRun` tracking](run-tracking.md)). The package always uses the configured class, never the default one directly: in queries, relations, Callbacks and the purge. A subclass can change the table, the connection, casts and fields, or add laravel-mongodb's `DocumentModel` trait.
- **Classes from the container:** each is resolved from the container, so binding a subclass replaces it everywhere:
  - `RouteConnexClientFactory`: builds the SDK client for an App, for example to add a proxy or a token store
  - `RouteConnexRunRecorder`: stores the `RouteConnexRun` after the API call, for example to fill extra columns
  - `RouteConnexCallbackHandler`: runs the Callback steps (see [Callbacks as events](callbacks.md)), for example to add checks
- Each of these classes has small `protected` methods per step, so a subclass overrides one step instead of copying the whole class.
- The `stancl/tenancy` preset (a Next theme) makes the same kind of bindings for the consumer app.
- The bundled Laravel Boost skill shows how to extend a model and bind a class.

## Non-goals

- **No `classes` map in config.** Only models are set in config, because Eloquent relations and queries need their class names; everything else uses the container, Laravel's own way.
- **Not every class is a public extension point.** Only the classes listed above are; the rest are internal and can change between minor versions before 1.0.
- **No morph map from the package.** The App model is always found through `models.app`, so the package stores no model class names.

## Database & compatibility notes

- **Databases:** a model subclass can move its table to any supported connection; Apps and Runs can be on different databases (for example MySQL and MongoDB). On MongoDB the subclass adds laravel-mongodb's `DocumentModel` trait; `mongodb/laravel-mongodb` stays a Composer suggestion.
- **Migrations:** an app that changes a table name or connection publishes the migrations (`route-connex-laravel-migrations`) and edits them, or writes its own.
- **Laravel 12 and 13:** container bindings, `$connection` and `$table` overrides exist in both.

## Open questions

None at the moment.

## Related

- Roadmap: [Overridable models and classes](../roadmap.md#now)
- Vision: "Everything is overridable" and "Defaults that work, everything overridable" ([vision.md](../vision.md))
- Features: [Laravel layer over the SDK](sdk-layer.md), [Multiple Apps](apps.md), [`RouteConnexRun` tracking](run-tracking.md), [Callbacks as events](callbacks.md)
