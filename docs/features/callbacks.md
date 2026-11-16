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

Route::middleware(['tenant'])->group(function () {
    RouteConnex::callbackRoutes();
});
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

- **Route:** `POST {prefix}/callbacks/{app}`, named `route-connex.callbacks`. `{app}` is a config App's name or a model App's key: a config App with that name wins, otherwise the key is looked up through `models.app` (see [Multiple Apps](apps.md)).
- **Registration:** the package registers the route unless `callbacks.register_routes` is false; `RouteConnex::callbackRoutes()` registers it inside the consumer app's own group. The route doesn't use the `web` middleware group.
- **Handling, in this order:**
  1. Find the App. Unknown App: `404`.
  2. Compare `route_connex_callback_token` with the App's Callback token in constant time (`hash_equals`). Missing or wrong token, or an App with no token: `403`. The code keeps a TODO where RouteConnex's own Callback authentication will go.
  3. Find the `RouteConnexRun` by `run_id` and App. Unknown Run: `404`.
  4. Update the Run's status, type, response and `finished_at`. A finished Run never goes back to an unfinished status, so a late `running` Callback after `succeeded` is ignored.
  5. Fire the events, only if the status changed, so a repeated Callback fires nothing. Answer `200`.
- **Events** (`RouteConnex\Laravel\Events`), each with the `RouteConnexRun` as `$event->run`:
  - `RouteConnexRunCreated`: the Run was stored after the API call (see [`RouteConnexRun` tracking](run-tracking.md))
  - `RouteConnexRunStatusChanged`: every status change, with `$event->previousStatus`
  - `RouteConnexRunFinished`: the Run reached a finished status (`succeeded`, `dry-run`, `failed` or `errored`)
  - `RouteConnexRunSucceeded`: the Run succeeded
  - `RouteConnexRunFailed`: the Run `failed` or `errored`
- The same events fire when `$run->refreshFromRouteConnex()` changes a Run's status.
- For work that should run as a job, the consumer app uses queued listeners (`ShouldQueue`); the package has no separate job mechanism.
- The bundled Laravel Boost skill covers the events, the route options and the callback setup in RouteConnex.

## Non-goals

- **No Callback signature check** until RouteConnex authenticates Callbacks (see Open questions in [concepts.md](../concepts.md#open-questions)).
- **No retries or dead-letter handling.** A Callback that never arrives is the polling theme.
- **No business logic.** The package updates the Run and fires events; what happens next is the consumer app's.

## Database & compatibility notes

- **Databases:** one lookup by `run_id` and App (indexed) and one update per Callback; the same on MySQL, PostgreSQL, MongoDB and SQLite. The Run is updated on its own connection (see [`RouteConnexRun` tracking](run-tracking.md)).
- **Tenancy:** the route must run in the Tenant's context, so multi-tenant apps register it in their Tenant route group. Queued listeners receive the Run through `SerializesModels`, which restores it on the same connection name, so that connection must exist in the queue worker; with `stancl/tenancy` its queue tenancy takes care of that.
- **CSRF:** the package's route doesn't use the `web` group. An app that puts it in a group with `web` excludes it with `$middleware->validateCsrfTokens(except: [...])` in `bootstrap/app.php`.
- **Laravel 12 and 13:** routes, route groups, events, queued listeners and `validateCsrfTokens()` exist in both.
- **RouteConnex:** Callback handling in RouteConnex is still being completed. The body format above (`data` shaped like the SDK's get-Run response, and the token as an extra field) is the one the package is built for.

## Open questions

None at the moment.

## Related

- Roadmap: [Callbacks as events](../roadmap.md#now)
- Concepts: [Callback](../concepts.md#callback), [Callback token](../concepts.md#callback-token), [Run status](../concepts.md#run-status)
- Ideas: [Polling as a fallback for missed Callbacks](../ideas.md#2026-11-17-polling-as-a-fallback-for-missed-callbacks), [More Artisan commands](../ideas.md#2026-11-17-more-artisan-commands)
- Features: [Multiple Apps](apps.md), [`RouteConnexRun` tracking](run-tracking.md), [Overridable models and classes](overrides.md)
