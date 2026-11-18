---
name: route-connex-laravel-development
description: >
  Configure and apply the RouteConnex package in Laravel applications: Credentials in .env, the RouteConnex facade, named Apps, Meta, responses and exceptions.
license: MIT
metadata:
  author: Leo Oberle
---

# RouteConnex

Use this skill when a Laravel application calls the RouteConnex API through `route-connex/route-connex-laravel`, a Laravel layer over `route-connex/route-connex-sdk-php`.

## Primary Goal

- call RouteConnex through the `RouteConnex` facade, with Credentials from `.env` and the package's exceptions handled

## Workflow

### 1. Configure the Credentials

Set them in `.env`:

```dotenv
ROUTE_CONNEX_CLIENT_ID=your-client-id
ROUTE_CONNEX_CLIENT_SECRET=your-client-secret
# Optional: another RouteConnex environment (default https://app.routeconnex.com/api)
ROUTE_CONNEX_BASE_URL=https://app.routeconnex.com/api
# Optional: the App used when a call doesn't name one (default "default")
ROUTE_CONNEX_APP=default
```

Publish the config only to add more Apps or change it:

```bash
php artisan vendor:publish --tag="route-connex-laravel-config"
```

`config/route-connex-laravel.php` has `base_url`, `default` (the default App's name) and `apps`, a list of named Apps, each with `client_id`, `client_secret` and an optional `base_url` that wins over the global one:

```php
'apps' => [
    'default' => [
        'client_id' => env('ROUTE_CONNEX_CLIENT_ID'),
        'client_secret' => env('ROUTE_CONNEX_CLIENT_SECRET'),
    ],
    'hpe' => [
        'client_id' => env('ROUTE_CONNEX_HPE_CLIENT_ID'),
        'client_secret' => env('ROUTE_CONNEX_HPE_CLIENT_SECRET'),
        'base_url' => env('ROUTE_CONNEX_HPE_BASE_URL'),
    ],
],
```

The config is checked when an App is first used, not at boot.

### 2. Call the API through the facade

The facade mirrors the SDK's endpoints with the SDK's own names. Calls go to the default App; `RouteConnex::app('hpe')` returns the same API for another config App.

```php
use RouteConnex\Laravel\Facades\RouteConnex;

RouteConnex::routeConnex()->about()->get();                 // no token needed
RouteConnex::routeConnex()->testAuth()->get();
RouteConnex::run()->_id_($runId)->get();

RouteConnex::microsoftSharepoint()->files()->get();
RouteConnex::microsoftSharepoint()->files()->post($body);
RouteConnex::microsoftSharepoint()->driveColumns()->get();
RouteConnex::hpeContentManager()->records()->get();
RouteConnex::hpeContentManager()->records()->post($body);

RouteConnex::app('hpe')->hpeContentManager()->records()->get();
```

Signatures (only the HTTP methods the SDK allows are offered):

- `routeConnex()->about()`, `->testAuth()` and `run()->_id_($id)`: `get(array $query = [], array $body = [], array $headers = [])`
- System endpoints (`microsoftSharepoint()->files()`, `->driveColumns()` and `hpeContentManager()->records()`): `get(array $query = [], array $body = [], array $headers = [], array $meta = [])`, and `post(array $body = [], array $query = [], array $headers = [], array $meta = [])` where the endpoint allows `POST` (`files()` and `records()`)

Use named arguments for the optional ones: `->post($body, meta: [...])`.

### 3. Meta on System endpoints

`meta:` is the data you want back in the Run. The package sends it where the API expects it: `_meta_` as JSON in the query for `GET`, as an array in the body otherwise. A non-empty `meta:` replaces any `_meta_` already in `$query` or `$body`; an empty one leaves them alone.

```php
RouteConnex::microsoftSharepoint()->files()->post([
    'site_name' => 'Finance',
    'channel' => 'Invoices',
    'file_path' => $path,
    'file_name' => 'invoice.pdf',
], meta: ['invoice_id' => $invoice->id]);
```

### 4. Read the response

Every call returns a `RouteConnex\Laravel\Http\RouteConnexResponse`, which extends Laravel's HTTP client `Response`:

```php
$response = RouteConnex::routeConnex()->about()->get();

$response->status();
$response->successful();
$response->json('data');
```

A returned response is always successful: 4xx and 5xx answers throw.

### 5. Handle exceptions

Every exception extends `RouteConnex\Laravel\Exceptions\RouteConnexException` (a `RuntimeException`) and keeps the original as `getPrevious()`:

| Exception                            | When                                                                                                          |
|--------------------------------------|---------------------------------------------------------------------------------------------------------------|
| `RouteConnexConfigurationException`  | no default App, an unknown App name, or an empty client ID or secret                                          |
| `RouteConnexAuthenticationException` | the token request failed or issued no usable token; `response()` is the `RouteConnexResponse` or `null` |
| `RouteConnexConnectionException`     | the API couldn't be reached                                                                                   |
| `RouteConnexRequestException`        | the API answered a call with 4xx or 5xx, including 401 and 403; `response()` has the API's error body         |

```php
try {
    RouteConnex::microsoftSharepoint()->files()->post($body);
} catch (RouteConnexRequestException $e) {
    $e->response()->status();
    $e->response()->json();
}
```

Request data that can't be encoded as JSON (invalid UTF-8 in a body, or Meta on `GET`) throws `\InvalidArgumentException`, not a package exception. It's a bug in the calling code: fix the data rather than catching it as a `RouteConnexException`.

To silence them all in the app's `bootstrap/app.php`:

```php
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->dontReport(RouteConnexException::class);
})
```

### 6. Use the SDK client for anything else

`RouteConnex::sdk()` (or `RouteConnex::app('hpe')->sdk()`) returns the configured SDK `ErClient` as it is. Calls made through it return raw PSR-7 responses and throw the SDK's and Guzzle's own exceptions, not the package's.

### 7. Test code that calls the API

`Http::fake()` doesn't see these calls, because the SDK sends them with its own Guzzle client. Mock the facade instead, with the chained method names:

```php
use GuzzleHttp\Psr7\Response;
use RouteConnex\Laravel\Facades\RouteConnex;
use RouteConnex\Laravel\Http\RouteConnexResponse;

RouteConnex::shouldReceive('microsoftSharepoint->files->post')
    ->andReturn(new RouteConnexResponse(new Response(200, [], '{"data":"ok"}')));
```

### 8. Change how SDK clients are built

Extend `RouteConnex\Laravel\RouteConnexClientFactory` and bind it in a service provider, for example to add a proxy. The factory is resolved from the container each time an App's client is first built:

```php
$this->app->bind(RouteConnexClientFactory::class, MyClientFactory::class);
```

Override its protected `newClient()` or `configure()` steps. It receives a `RouteConnexConfigApp` with `name`, `clientId`, `clientSecret` and `baseUrl`.

## Rules, References, and Templates

Read before executing:

- the package README for installation and publish tags
- `config/route-connex-laravel.php` in the app, if it was published

Rules:

- one client per App is kept for the life of the PHP process; its token is reused until it is about to expire
- the SDK requires Guzzle 7 (`guzzlehttp/guzzle` `^7.9`): on Laravel 13, an app that needs Guzzle 8 can't install the package yet
- Apps are config-only for now; do not pass models or objects to `RouteConnex::app()`
- the package does not store Runs or handle Callbacks yet

## Examples

- Check the Credentials work: `RouteConnex::routeConnex()->testAuth()->get()->successful()`
- Upload a file with Meta: see step 3
- Use a second App: `RouteConnex::app('hpe')->hpeContentManager()->records()->get()`

## Anti-patterns

- do not build an `ErClient` by hand or call the API with `Http::`; use the facade
- do not catch Guzzle exceptions around facade calls; catch the package's exceptions
- do not read the Credentials with `env()` outside config files
- do not document package internals here; keep the skill focused on adoption in Laravel apps
