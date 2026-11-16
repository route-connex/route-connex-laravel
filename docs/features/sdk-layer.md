# Laravel layer over the SDK

**Status:** planned
**Target version:** 0.x

## Summary

The `RouteConnex` facade and container binding over `route-connex/route-connex-sdk-php`: credentials from config and `.env`, a fluent API that mirrors the SDK's endpoints but returns Laravel results, and package exceptions in place of the SDK's and Guzzle's.

## Motivation

The SDK is framework-agnostic: an app builds an `ErClient` by hand, gets raw PSR-7 responses back and catches Guzzle exceptions. Every Laravel app repeats that plumbing. This feature does it once, the Laravel way, and is the base the other features build on: [Multiple Apps](apps.md), [`RouteConnexRun` tracking](run-tracking.md), [Callbacks as events](callbacks.md) and [Overridable models and classes](overrides.md).

## Developer experience

A single-App consumer app sets its Credentials in `.env`:

```dotenv
ROUTE_CONNEX_CLIENT_ID=your-client-id
ROUTE_CONNEX_CLIENT_SECRET=your-client-secret
# Optional, for another RouteConnex environment:
ROUTE_CONNEX_BASE_URL=https://app.routeconnex.com/api
```

and can publish the config with `php artisan vendor:publish --tag=route-connex-laravel-config`:

```php
// config/route-connex-laravel.php
return [
    'base_url' => env('ROUTE_CONNEX_BASE_URL', 'https://app.routeconnex.com/api'),

    'default' => env('ROUTE_CONNEX_APP', 'default'),

    'apps' => [
        'default' => [
            'client_id' => env('ROUTE_CONNEX_CLIENT_ID'),
            'client_secret' => env('ROUTE_CONNEX_CLIENT_SECRET'),
        ],
    ],
];
```

The facade mirrors the SDK's endpoint tree. A Service that works with a System returns the `RouteConnexRun` the package stored; the others return a `RouteConnexResponse`. Meta is a named argument, and the package puts it where the API expects it (JSON in the query for `GET`, in the body otherwise):

```php
use RouteConnex\Laravel\Facades\RouteConnex;

$run = RouteConnex::microsoftSharepoint()->files()->post([
    'site_name' => 'Finance',
    'channel' => 'Invoices',
    'file_path' => $path,
    'file_name' => 'invoice.pdf',
], meta: ['invoice_id' => $invoice->id]);

$run->status; // 'created'

$about = RouteConnex::routeConnex()->about()->get();
$about->json('data');
```

For anything the layer doesn't cover yet, the configured SDK client is available:

```php
RouteConnex::sdk()->run()->_id_($runId)->get();
```

Every exception the package throws extends `RouteConnexException`, so a consumer app can handle or silence them all at once:

```php
// bootstrap/app.php
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->dontReport(RouteConnexException::class);
})
```

## Scope

- `config/route-connex-laravel.php` with the base URL, the default App and the `apps` list (Apps are detailed in [Multiple Apps](apps.md)). The base URL is global; an App can override it.
- The `RouteConnex` facade over a container singleton, which builds one SDK `ErClient` per App.
- A fluent API that mirrors the SDK's endpoints, each one mapped explicitly, so return types are known and the Boost skill can list them. A new SDK endpoint needs a small change here before the facade offers it; until then it's reachable through `RouteConnex::sdk()`.
  - endpoints under a System (`microsoftSharepoint()`, `hpeContentManager()`) create a Run and return a `RouteConnexRun` (see [`RouteConnexRun` tracking](run-tracking.md))
  - the others (`routeConnex()`, `run()`) return a `RouteConnexResponse`, which extends Laravel's HTTP client `Response` (`json()`, `status()`, `successful()`, `throw()`)
  - a `meta:` argument on every call that creates a Run
- `RouteConnex::sdk()` returns the configured `ErClient` as a fallback.
- Exceptions, all extending `RouteConnexException`, which wrap the SDK's and Guzzle's:
  - `RouteConnexRequestException`: the API answered with an error; carries the `RouteConnexResponse`
  - `RouteConnexConnectionException`: the API couldn't be reached
  - `RouteConnexAuthenticationException`: the Credentials were rejected or no token was issued
  - `RouteConnexConfigurationException`: missing Credentials, an unknown App, or no default App when a call doesn't name one
- The bundled Laravel Boost skill (`resources/boost/skills/route-connex-laravel-development`) documents the config, the facade and the exceptions for coding agents in consumer apps.
- New Composer requirements: `route-connex/route-connex-sdk-php` and `illuminate/http`.

**Needs from the SDK:** a way to give an `ErClient` an access token from outside (or a token store), so the package can cache each App's token in Laravel's cache instead of requesting a new one in every PHP request. Until the SDK has it, each new client requests its own token.

## Non-goals

- **No direct API calls.** Everything goes through the SDK; a missing endpoint is added to the SDK first.
- **No named convenience methods yet** (such as `RouteConnex::sharepoint()->uploadFile(...)`). They could be added later on top of the mirrored tree.
- **Sync calls** are a separate roadmap theme.
- **Credentials per Tenant or supplied at runtime** belong to [Multiple Apps](apps.md).

## Database & compatibility notes

- **Databases:** this feature doesn't touch the database itself; storing Runs is covered in [`RouteConnexRun` tracking](run-tracking.md). It behaves the same on MySQL, PostgreSQL, MongoDB and SQLite.
- **Laravel 12 and 13:** uses only APIs both have: `mergeConfigFrom`, container singletons, facades, `Illuminate\Http\Client\Response` (which wraps a PSR-7 response) and `withExceptions()->dontReport()` in `bootstrap/app.php`.
- **PHP 8.3+:** the SDK requires PHP 8.1+, so it fits.
- **Guzzle:** the SDK requires `guzzlehttp/guzzle` `^7.9`. Laravel 13 allows `^7.8.2 || ^8.0`; Laravel 12's constraint and the `prefer-lowest` lane must resolve to Guzzle 7.9 or later, which CI checks.
- **Windows:** nothing path- or shell-specific.

## Open questions

None at the moment.

## Related

- Roadmap: [Laravel layer over the SDK](../roadmap.md#now)
- Vision goals: a Laravel-flavoured API over the SDK, credentials from `.env`, ready for AI agents ([vision.md](../vision.md#goals))
- Features: [Multiple Apps](apps.md), [`RouteConnexRun` tracking](run-tracking.md), [Callbacks as events](callbacks.md), [Overridable models and classes](overrides.md)
