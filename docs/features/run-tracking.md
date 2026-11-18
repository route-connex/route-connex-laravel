# `RouteConnexRun` tracking

**Status:** planned
**Target version:** 0.x

## Summary

Every Run the package starts is stored as a `RouteConnexRun` in the consumer app's database, with its App, Service, Run type, Run status, Meta, request and response, and kept up to date from [Callbacks](callbacks.md) or on demand. Large payloads of old successful Runs can be purged.

## Motivation

A Run finishes in the background, so the consumer app needs a local record to know what it started, follow its status and react when it finishes. Without the package, every app builds its own runs table. The record has to work with Apps from config or from a Tenant's database, on every supported database, and without growing forever with payloads nobody will read again.

## Developer experience

Calling a Service that works with a System returns the stored Run:

```php
use RouteConnex\Laravel\Enums\RouteConnexRunStatus;
use RouteConnex\Laravel\Facades\RouteConnex;

$run = RouteConnex::app($integration)->microsoftSharepoint()->files()->post([...], meta: ['invoice_id' => $invoice->id]);

$run->run_id;    // RouteConnex's Run ID
$run->status;    // RouteConnexRunStatus::Created
$run->meta;      // ['invoice_id' => 42]
$run->app;       // the RouteConnexAppContract that started it

$run->refreshFromRouteConnex(); // asks RouteConnex for the Run's current state and saves it

$run->status->isFinished(); // true for succeeded, dry-run, failed and errored
```

Config:

```php
// config/route-connex-laravel.php
'models' => [
    'app' => \RouteConnex\Laravel\Models\RouteConnexApp::class,
    'run' => \RouteConnex\Laravel\Models\RouteConnexRun::class,
],

'runs' => [
    'store_request' => true,
    'store_response' => true,
    'purge_after_days' => 30, // null keeps payloads forever
],
```

Purging the payloads of old successful Runs, from the consumer app's scheduler:

```php
// routes/console.php
use RouteConnex\Laravel\Tenancy\RouteConnexTenancy;

Schedule::call(fn () => app(RouteConnexTenancy::class)
    ->forEachTenant(fn () => RouteConnexRun::purgePayloads())
)->daily();
```

`forEachTenant()` runs the purge once per Tenant in that Tenant's context, or once when tenancy is off (see [Overridable models and classes](overrides.md)).

Storing Runs on another connection than the Apps, for example Apps in MySQL and Runs in MongoDB:

```php
class RouteConnexRun extends \RouteConnex\Laravel\Models\RouteConnexRun
{
    use \MongoDB\Laravel\Eloquent\DocumentModel;

    protected $connection = 'mongodb';
}
```

## Scope

- `RouteConnexRun` (`RouteConnex\Laravel\Models`) on the `route_connex_runs` table, set in `models.run` in config (see [Overridable models and classes](overrides.md)):
  - `run_id`: RouteConnex's Run ID, a string, indexed for matching Callbacks
  - `app_name` for a config App, or `app_id` for a model App. Exactly one of them is set: the model throws `RouteConnexConfigurationException` when saving a Run with both or neither. No model class name is stored, since the App model is always found through `models.app`, so replacing or renaming that model never breaks stored Runs.
  - `service`: the Service and HTTP method, e.g. `POST microsoft-sharepoint/files`
  - `type`: the Run type from RouteConnex
  - `status`: cast to the `RouteConnexRunStatus` enum (`created`, `queued`, `running`, `succeeded`, `dry-run`, `failed`, `errored`), with `isFinished()`
  - `meta`, `request`, `response`: JSON, cast to arrays
  - `finished_at`, `purged_at` and timestamps
- A Run is stored only once the API has returned a Run ID. If the call fails, the package throws (see [Laravel layer over the SDK](sdk-layer.md)) and stores nothing.
- `RouteConnexRunCreated` (`RouteConnex\Laravel\Events`) fires once the Run is stored, with the `RouteConnexRun`. The events for later status changes are in [Callbacks as events](callbacks.md).
- `runs.store_request` and `runs.store_response` turn off storing either payload.
- `$run->app` returns the App that started the Run: the config App by `app_name`, or the model App by `app_id` through `models.app`, loaded from the App model's own connection.
- **Connection:** a Run model that sets its own connection always uses it. Otherwise a Run of a model App is stored on that App's connection, so a Tenant's Runs stay in the Tenant's database, and a Run of a config App uses the default connection.
- `$run->refreshFromRouteConnex()`: gets the Run from RouteConnex through the SDK with the Run's own App, then updates its status, type, response and `finished_at`. It fires the same status events as a Callback (see [Callbacks as events](callbacks.md)).
- `RouteConnexRun::purgePayloads()`: clears `request` and `response` on `succeeded` and `dry-run` Runs that finished more than `runs.purge_after_days` days ago, and sets `purged_at`. `failed`, `errored` and unfinished Runs keep their payloads. Returns the number of Runs purged.
- A publish-only migration for `route_connex_runs`, under the existing `route-connex-laravel-migrations` tag.
- The bundled Laravel Boost skill covers reading Runs, refreshing them and scheduling the purge for every Tenant.

## Non-goals

- **No deleting Runs here.** `route-connex:prune` belongs to the Artisan commands theme, as does `route-connex:purge`, which will call the method above.
- **No automatic polling.** `refreshFromRouteConnex()` is called by the consumer app; re-checking stuck Runs on a schedule is the polling theme.
- **No links to the consumer app's models.** That's the "Link Runs to models" theme; until then, Meta carries the consumer app's IDs.
- **No queued Runs** (see the vision's non-goals).

## Database & compatibility notes

- **Migration:** `id`, `run_id` (string, indexed), `app_name` (nullable string), `app_id` (nullable string, indexed), `service` (string), `type` (nullable string), `status` (string), `meta`, `request` and `response` (nullable JSON), `finished_at` and `purged_at` (nullable timestamps), timestamps, and an index on `status` and `finished_at` for the purge.
  - `app_id` is a string, not a bigint, so it can hold integer, UUID and MongoDB ObjectId keys.
  - `app_name` and `app_id` being exclusive is checked by the model, not by a database `CHECK` constraint: MySQL only enforces `CHECK` from 8.0.16, Laravel's schema builder has no portable way to declare one, and MongoDB has none.
  - `status` is a string, not a database enum, so a new RouteConnex status needs no migration.
  - `run_id` is not unique: Runs from several Tenants can share one table.
- **MySQL 8.0+:** native `json` columns.
- **PostgreSQL 14+:** `json` columns. A string `app_id` compared with an integer App key must work; the tests cover it.
- **SQLite:** JSON is stored as text; nothing queries inside it.
- **MongoDB 7.0+:** the consumer app extends the model with laravel-mongodb's `DocumentModel` trait. JSON values are stored as subdocuments, and purging sets them to null. A Run on MongoDB can belong to an App on an SQL connection, and the other way round.
- **Laravel 12 and 13:** enum casts, `array` casts, model events and the scheduler's `Schedule::call()` exist in both.

## Already in the code

[Laravel layer over the SDK](sdk-layer.md) already sends every System call through one protected step (`RouteConnexSystemEndpoint::result()`), which receives the App, the Service, the request data, the Meta and the response, and returns the `RouteConnexResponse`. This feature changes that step to store the Run and return a `RouteConnexRun`.

## Open questions

None at the moment.

## Related

- Roadmap: [`RouteConnexRun` tracking](../roadmap.md#now)
- Concepts: [RouteConnexRun](../concepts.md#routeconnexrun), [Run](../concepts.md#run), [Run status](../concepts.md#run-status), [Meta](../concepts.md#meta)
- Ideas: [Polling as a fallback for missed Callbacks](../ideas.md#2026-11-17-polling-as-a-fallback-for-missed-callbacks), [Link Runs to models](../ideas.md#2026-11-17-link-runs-to-models), [More Artisan commands](../ideas.md#2026-11-17-more-artisan-commands)
- Features: [Laravel layer over the SDK](sdk-layer.md), [Multiple Apps](apps.md), [Callbacks as events](callbacks.md), [Overridable models and classes](overrides.md)
- Decisions: [0002: RouteConnex prefix on everything the package adds](../decisions/0002-routeconnex-prefix.md), [0004: Runs store `app_name` or `app_id`, resolved through `models.app`](../decisions/0004-runs-store-app-name-or-id.md), [0006: MongoDB through the consumer app's model subclass](../decisions/0006-mongodb-through-app-subclass.md), [0009: Purge payloads of old successful Runs instead of deleting Runs](../decisions/0009-purge-payloads-of-old-successful-runs.md), [0010: A replaceable tenancy class](../decisions/0010-replaceable-tenancy-class.md)
