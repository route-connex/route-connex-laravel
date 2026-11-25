---
name: route-connex-laravel-development
description: >
  Configure and apply the RouteConnex package in Laravel applications: Credentials in .env, the RouteConnex facade, config and model Apps, Callback tokens, Meta, responses and exceptions.
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
# Optional: the default App's Callback token, a long random value (see step 6)
ROUTE_CONNEX_CALLBACK_TOKEN=
```

Publish the config only to add more Apps or change it:

```bash
php artisan vendor:publish --tag="route-connex-laravel-config"
```

`config/route-connex-laravel.php` has `base_url`, `default` (the default App's name) and `apps`, a list of named Apps, each with `client_id`, `client_secret`, a `callback_token` and an optional `base_url` that wins over the global one:

```php
'apps' => [
    'default' => [
        'client_id' => env('ROUTE_CONNEX_CLIENT_ID'),
        'client_secret' => env('ROUTE_CONNEX_CLIENT_SECRET'),
        'callback_token' => env('ROUTE_CONNEX_CALLBACK_TOKEN'),
    ],
    'hpe' => [
        'client_id' => env('ROUTE_CONNEX_HPE_CLIENT_ID'),
        'client_secret' => env('ROUTE_CONNEX_HPE_CLIENT_SECRET'),
        'callback_token' => env('ROUTE_CONNEX_HPE_CALLBACK_TOKEN'),
        'base_url' => env('ROUTE_CONNEX_HPE_BASE_URL'),
    ],
],
```

Empty values count as unset. The config is checked when an App is first used, not at boot. The Callback token is only stored for now: nothing reads it until Callbacks ship.

### 2. Call the API through the facade

The facade mirrors the SDK's endpoints with the SDK's own names. Calls go to the default App; `RouteConnex::app('hpe')` returns the same API for another config App, and `RouteConnex::app($model)` for an App model (step 6).

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
| `RouteConnexConnectionException`     | the API couldn't be reached, or a request failed without a 4xx or 5xx answer                                  |
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

### 6. Use Apps stored in the database (multi-tenant apps)

`RouteConnex::app()` also takes any `RouteConnex\Laravel\Contracts\RouteConnexAppContract`: a model, an unsaved model or any object that implements it. The package doesn't find Tenants or resolve Apps; load the model (from the Tenant's database, say) and pass it in:

```php
RouteConnex::app($app)->microsoftSharepoint()->files()->post($body, meta: [...]);
```

With the shipped model, publish and run its migration (creates `route_connex_apps`: `name`, `client_id`, `client_secret`, `callback_token`, `base_url`):

```bash
php artisan vendor:publish --tag="route-connex-laravel-migrations"
php artisan migrate
```

```php
use RouteConnex\Laravel\Models\RouteConnexApp;

$app = RouteConnexApp::create([
    'name' => 'SharePoint',
    'client_id' => $clientId,
    'client_secret' => $clientSecret, // encrypted and hidden, like callback_token
]);

$app->callback_token; // a 64-character hex token, generated on create when none is given
```

With the app's own model, use the trait. It reads `client_id`, `client_secret`, `callback_token` and `base_url`; override a method to read another column. The trait doesn't encrypt or generate anything:

```php
use RouteConnex\Laravel\Concerns\InteractsWithRouteConnexApp;
use RouteConnex\Laravel\Contracts\RouteConnexAppContract;
use RouteConnex\Laravel\Facades\RouteConnex;

class Integration extends Model implements RouteConnexAppContract
{
    use InteractsWithRouteConnexApp;

    public function getRouteConnexClientId(): ?string
    {
        return $this->settings['route_connex_client_id'] ?? null;
    }
}

RouteConnex::app($integration)->hpeContentManager()->records()->get();
```

The contract's methods are `getRouteConnexKey()` (`string|int|null`, `null` while unsaved), `getRouteConnexClientId()`, `getRouteConnexClientSecret()`, `getRouteConnexCallbackToken()` and `getRouteConnexBaseUrl()`, all nullable strings. A `null` or empty client ID or secret throws `RouteConnexConfigurationException`, naming the class and key. An App's own base URL wins over the global `base_url`.

`RouteConnex::generateCallbackToken()` returns a new random 64-hex Callback token, for own models or to fill `ROUTE_CONNEX_CALLBACK_TOKEN`.

Set the model Callbacks will use to find Apps in `config/route-connex-laravel.php` (publish the config first). It defaults to `RouteConnexApp`:

```php
'models' => [
    'app' => \App\Models\Integration::class,
],
```

On MongoDB (needs `mongodb/laravel-mongodb`), extend the shipped model with `DocumentModel` and set the subclass in `models.app`:

```php
class RouteConnexApp extends \RouteConnex\Laravel\Models\RouteConnexApp
{
    use \MongoDB\Laravel\Eloquent\DocumentModel;
}
```

Each `RouteConnex::app($model)` call returns a client bound to that model. Clients are shared per class, key and Credentials, so Tenants with the same IDs never share one and a rotated secret applies on the next call.

### 7. Use the SDK client for anything else

`RouteConnex::sdk()` (or `RouteConnex::app('hpe')->sdk()`) returns the configured SDK `ErClient` as it is. Calls made through it return raw PSR-7 responses and throw the SDK's and Guzzle's own exceptions, not the package's.

### 8. Test code that calls the API

`Http::fake()` doesn't see these calls, because the SDK sends them with its own Guzzle client. Mock the facade instead, with the chained method names:

```php
use GuzzleHttp\Psr7\Response;
use RouteConnex\Laravel\Facades\RouteConnex;
use RouteConnex\Laravel\Http\RouteConnexResponse;

RouteConnex::shouldReceive('microsoftSharepoint->files->post')
    ->andReturn(new RouteConnexResponse(new Response(200, [], '{"data":"ok"}')));
```

### 9. Change how SDK clients are built

Extend `RouteConnex\Laravel\RouteConnexClientFactory` and bind it in a service provider, for example to add a proxy. The factory is resolved from the container each time an App's client is first built:

```php
$this->app->bind(RouteConnexClientFactory::class, MyClientFactory::class);
```

Override its protected `newClient()` or `configure()` steps. It receives a `RouteConnexAppContract` (a config App or a model); read its Credentials and base URL through the contract's getters.

## Rules, References, and Templates

Read before executing:

- the package README for installation and publish tags
- `config/route-connex-laravel.php` in the app, if it was published

Rules:

- one client per App is kept for the life of the PHP process; its token is reused until it is about to expire
- the package works with Guzzle 7.9+ or 8 (`guzzlehttp/guzzle` `^7.9 || ^8.0`)
- `RouteConnex::app()` takes a config App's name or a `RouteConnexAppContract`; never pass a model App's key
- the client secret and Callback token of `RouteConnexApp` are encrypted with the app's `APP_KEY`
- the package does not store Runs or handle Callbacks yet

## Examples

- Check the Credentials work: `RouteConnex::routeConnex()->testAuth()->get()->successful()`
- Upload a file with Meta: see step 3
- Use a second App: `RouteConnex::app('hpe')->hpeContentManager()->records()->get()`
- Use a Tenant's App: `RouteConnex::app($tenantApp)->hpeContentManager()->records()->get()`

## Anti-patterns

- do not build an `ErClient` by hand or call the API with `Http::`; use the facade
- do not catch Guzzle exceptions around facade calls; catch the package's exceptions
- do not read the Credentials with `env()` outside config files
- do not store a model App's Credentials in config or cache a client for it yourself; pass the model to `RouteConnex::app()`
- do not document package internals here; keep the skill focused on adoption in Laravel apps
