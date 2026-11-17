# Callbacks as events

**Status:** planned
**Target version:** 0.x

## Summary

The package receives RouteConnex Callbacks on a route it registers, or that the consumer app places in its own route group, checks the App's Callback token, updates the matching `RouteConnexRun` and fires Laravel events the consumer app listens to.

## Motivation

A Run finishes in the background, and RouteConnex reports it through a Callback. Without the package, every consumer app writes its own endpoint, its own token check and its own Run lookup. Turning Callbacks into events lets the app react with ordinary listeners, queued or not. In multi-tenant apps the Callback must reach the right Tenant, so the route has to fit into the app's own routing.

## Developer experience

Config:

```php
// config/route-connex-laravel.php
'callbacks' => [
    'register_routes' => true,     // false: the app registers the route in its own group
    'prefix' => 'route-connex',    // the route is POST {prefix}/callbacks/{app}
    'middleware' => [],            // no `web` group, so no session or CSRF
],
```

Listening to Run events:

```php
use RouteConnex\Laravel\Events\RouteConnexRunSucceeded;

class AttachSharePointLink implements ShouldQueue
{
    public function handle(RouteConnexRunSucceeded $event): void
    {
        $invoice = Invoice::find($event->run->meta['invoice_id']);
        // ...
    }
}
```

Placing the route in the consumer app's own group, for example a Tenant's routes:

```php
// config: 'register_routes' => false

// Whatever the app already uses to resolve its Tenant: a subdomain group,
// its own middleware, a tenancy package's middleware...
Route::domain('{tenant}.example.com')->middleware(['resolve-tenant'])->group(function () {
    RouteConnex::callbackRoutes();
});

// Registered in a second group too? Give each registration its own route name:
Route::domain('{tenant}.example.org')->middleware(['resolve-tenant'])->group(function () {
    RouteConnex::callbackRoutes(name: 'org.route-connex.callbacks');
});
```

An app whose Apps live in a central database and whose Runs live in each Tenant's database can't find the Tenant from the route. It uses one central callback URL and switches to the App's Tenant in the handler, through its own [`RouteConnexTenancy`](overrides.md). Its `RouteConnexRun` subclass sets its own connection (for example `tenant`), so Runs are stored and found in the Tenant's database, not on the App's central connection:

```php
use Closure;
use RouteConnex\Laravel\Callbacks\RouteConnexCallbackHandler;
use RouteConnex\Laravel\Contracts\RouteConnexAppContract;
use RouteConnex\Laravel\Tenancy\RouteConnexTenancy;

class TenantCallbackHandler extends RouteConnexCallbackHandler
{
    // Wraps finding the Run, updating it and firing the events.
    // $app is the app's own App model (from the central database), with a `tenant` relation.
    protected function runInAppContext(RouteConnexAppContract $app, Closure $callback): mixed
    {
        return app(RouteConnexTenancy::class)->run($app->tenant, $callback);
    }
}
```

The Callback RouteConnex sends, configured for the App in RouteConnex (the [callback configuration command](../roadmap.md#next) prints this setup):

```json
{
    "route_connex_callback_token": "the App's Callback token",
    "data": {
        "id": "the Run ID",
        "status": { "id": "succeeded" },
        "type": { "...": "..." },
        "response_data": { "...": "..." }
    }
}
```

## Scope

- **Route:** `POST {prefix}/callbacks/{app}`, named `route-connex.callbacks` by default. `{app}` is a config App's name or a model App's key: a config App with that name wins, otherwise the key is looked up through `models.app` (see [Multiple Apps](apps.md)).
- **Registration:** the package registers the route unless `callbacks.register_routes` is false; `RouteConnex::callbackRoutes(?string $name = null)` registers it inside the consumer app's own group, named `route-connex.callbacks` unless a name is given. The route doesn't use the `web` middleware group.
- **One name per registration:** `php artisan route:cache` refuses two routes with the same name. An app that calls `callbackRoutes()` turns `register_routes` off, and an app that registers the route in several groups gives each one its own name.
- **Any tenancy setup:** the package assumes no tenancy library. How the Tenant is found (a subdomain, a path, a header, a package) is the consumer app's group and middleware. The Callback controller reads only the `app` route parameter and ignores any other, such as a `{tenant}` domain parameter that the app's middleware may or may not have removed.
- **Callback URL:** the URL RouteConnex should call for an App (printed by the callback configuration command) is built by `RouteConnexCallbackUrlGenerator`, which by default uses `route('route-connex.callbacks', ...)` in the current URL context. When tenancy is enabled, the command runs it inside `RouteConnexTenancy::run()` for the `--tenant` it was given. An app whose Callbacks live on each Tenant's own domain, or under another route name, binds its own subclass that builds the URL, for example from `RouteConnexTenancy::current()` (see [Overridable models and classes](overrides.md)).
- **Handling, in this order.** `RouteConnexCallbackHandler` runs each step in its own `protected` method, so a subclass overrides one step (see [Overridable models and classes](overrides.md)):
  1. `findApp()`: find the App. Unknown App: `404`.
  2. `checkToken()`: compare `route_connex_callback_token` with the App's Callback token in constant time (`hash_equals`). Missing or wrong token, or an App with no token: `403`. The code keeps a TODO where RouteConnex's own Callback authentication will go.
  3. `runInAppContext()`: runs steps 4 to 6 inside a callback. By default it just calls it; a subclass can switch to the App's Tenant here.
  4. `findRun()`: find the `RouteConnexRun` whose `run_id` is the body's `data.id`, for that App. The lookup uses the same connection rule as storing a Run: the Run model's own connection, else the model App's connection, else the default one (see [`RouteConnexRun` tracking](run-tracking.md)). Unknown Run: `404`.
  5. `updateRun()`: update the Run's status, type, response and `finished_at`. A finished Run's status is final, as it is in RouteConnex. A late unfinished status (such as `running` after `succeeded`) is ignored. A different finished status (such as `failed` after `succeeded`) is ignored too and logs a warning. Either way, nothing is updated and the Callback still gets `200`, so RouteConnex doesn't retry it.
  6. `fireEvents()`: fire the events, only if the status changed, so a repeated Callback fires nothing.
  7. Answer `200`.
- **Events** (`RouteConnex\Laravel\Events`), each with the `RouteConnexRun` as `$event->run`:
  - `RouteConnexRunCreated`: the Run was stored after the API call (see [`RouteConnexRun` tracking](run-tracking.md))
  - `RouteConnexRunStatusChanged`: every status change, with `$event->previousStatus`
  - `RouteConnexRunFinished`: the Run reached a finished status (`succeeded`, `dry-run`, `failed` or `errored`)
  - `RouteConnexRunSucceeded`: the Run `succeeded` or finished as a `dry-run`
  - `RouteConnexRunFailed`: the Run `failed` or `errored`
- The same events fire when `$run->refreshFromRouteConnex()` changes a Run's status.
- For work that should run as a job, the consumer app uses queued listeners (`ShouldQueue`); the package has no separate job mechanism.
- The bundled Laravel Boost skill covers the events, the route options, the handler's steps and the callback setup in RouteConnex.

## Non-goals

- **No Callback signature check** until RouteConnex authenticates Callbacks (see Open questions in [concepts.md](../concepts.md#open-questions)).
- **No retries or dead-letter handling.** A Callback that never arrives is the polling theme.
- **No business logic.** The package updates the Run and fires events; what happens next is the consumer app's.

## Database & compatibility notes

- **Databases:** one lookup by `run_id` and App (indexed) and one update per Callback; the same on MySQL, PostgreSQL, MongoDB and SQLite. The Run is updated on its own connection (see [`RouteConnexRun` tracking](run-tracking.md)).
- **Tenancy:** the route must run in the Tenant's context, so multi-tenant apps register it in their Tenant route group, or switch to the App's Tenant in `runInAppContext()`. Queued listeners receive the Run through `SerializesModels`, which restores it by connection name and key. A connection name that exists in every worker isn't enough: a shared name such as `tenant` points at whichever Tenant the worker used last. The queue worker must restore the same Tenant's context before the listener runs, or `$event->run` and `$run->app` load another Tenant's records. The consumer app's own tenancy, or a tenancy package's queue support, does that.
- **CSRF:** the package's route doesn't use the `web` group. An app that puts it in a group with `web` excludes it with `$middleware->validateCsrfTokens(except: [...])` in `bootstrap/app.php`.
- **Laravel 12 and 13:** routes, route groups, named routes, `route:cache`, events, queued listeners, logging and `validateCsrfTokens()` exist in both.
- **RouteConnex:** Callback handling in RouteConnex is still being completed. The body format above (`data` shaped like the SDK's get-Run response, and the token as an extra field) is the one the package is built for.

## Open questions

None at the moment.

## Related

- Roadmap: [Callbacks as events](../roadmap.md#now)
- Concepts: [Callback](../concepts.md#callback), [Callback token](../concepts.md#callback-token), [Run status](../concepts.md#run-status)
- Ideas: [Polling as a fallback for missed Callbacks](../ideas.md#2026-11-17-polling-as-a-fallback-for-missed-callbacks), [More Artisan commands](../ideas.md#2026-11-17-more-artisan-commands)
- Features: [Multiple Apps](apps.md), [`RouteConnexRun` tracking](run-tracking.md), [Overridable models and classes](overrides.md)
- Decisions: [0007: Callbacks: token check, route outside `web`, events on change](../decisions/0007-callbacks.md), [0010: A replaceable tenancy class](../decisions/0010-replaceable-tenancy-class.md)
