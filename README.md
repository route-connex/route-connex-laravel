<div align="center">
    <h1>RouteConnex</h1>
    <h2>This plugin is under development - DO NOT try to use it yet!</h2>
</div>

<p align="center">
    <a href="https://packagist.org/packages/route-connex/route-connex-laravel"><img src="https://img.shields.io/packagist/v/route-connex/route-connex-laravel.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://packagist.org/packages/route-connex/route-connex-laravel"><img src="https://img.shields.io/packagist/php-v/route-connex/route-connex-laravel.svg?style=flat-square" alt="PHP from Packagist"></a>
    <a href="https://packagist.org/packages/route-connex/route-connex-laravel"><img src="https://badge.laravel.cloud/badge/route-connex/route-connex-laravel?style=flat" alt="Laravel versions"></a>
    <a href="https://github.com/route-connex/route-connex-laravel/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/route-connex/route-connex-laravel/tests.yml?branch=main&label=Tests&style=flat-square"></a>
    <a href="https://packagist.org/packages/route-connex/route-connex-laravel"><img src="https://img.shields.io/packagist/dt/route-connex/route-connex-laravel.svg?style=flat-square" alt="Total Downloads"></a>
</p>

Laravel plugin for route-connex

## Requirements

- PHP 8.3+
- Laravel 12 or 13
- One of these databases:
  - MySQL 8.0+
  - PostgreSQL 14+
  - MongoDB 7.0+ (requires [`mongodb/laravel-mongodb`](https://github.com/mongodb/laravel-mongodb) ^5.12 and the `mongodb` PHP extension)
  - SQLite
- Guzzle 7.9+ or 8 (`guzzlehttp/guzzle` `^7.9 || ^8.0`)

## Installation

You can install the package via Composer:

```bash
composer require route-connex/route-connex-laravel
```

You may publish all of the package's resources at once:

```bash
php artisan vendor:publish --tag="route-connex-laravel"
```

Or, you may publish each resource individually:

### Publishing the Configuration File

```bash
php artisan vendor:publish --tag="route-connex-laravel-config"
```

See [Configuration](#configuration) for what the config holds.

### Publishing and Running the Migrations

```bash
php artisan vendor:publish --tag="route-connex-laravel-migrations"
php artisan migrate
```

The migration creates the `route_connex_apps` table (or collection, on MongoDB) for [App models](#app-models). Skip it if all your Apps are in config or in your own table.

### Publishing the Views

```bash
php artisan vendor:publish --tag="route-connex-laravel-views"
```

### Publishing the Translations

```bash
php artisan vendor:publish --tag="route-connex-laravel-lang"
```

### Publishing the Public Assets

```bash
php artisan vendor:publish --tag="route-connex-laravel-assets"
```

## Configuration

Set the App's Credentials in `.env`:

```dotenv
ROUTE_CONNEX_CLIENT_ID=your-client-id
ROUTE_CONNEX_CLIENT_SECRET=your-client-secret
ROUTE_CONNEX_CALLBACK_TOKEN=a-long-random-value

# Optional
ROUTE_CONNEX_BASE_URL=https://app.routeconnex.com/api
ROUTE_CONNEX_APP=default
```

`ROUTE_CONNEX_CALLBACK_TOKEN` is the secret RouteConnex sends back in every Callback: use a long random value, for example the output of `RouteConnex::generateCallbackToken()`. It is only stored for now; nothing reads it until Callbacks ship. `ROUTE_CONNEX_BASE_URL` is the API URL for every App, and `ROUTE_CONNEX_APP` names the default App. To add Apps, [publish the config](#publishing-the-configuration-file) and list them under `apps` in `config/route-connex-laravel.php`. An App can set its own `base_url`, which wins over the global one:

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

`models.app` sets the App model class (default `RouteConnex\Laravel\Models\RouteConnexApp`); see [App models](#app-models).

The config is checked when an App is first used, not at boot, so an app without Credentials still boots.

## Usage

The `RouteConnex` facade mirrors the endpoints of [`route-connex/route-connex-sdk-php`](https://packagist.org/packages/route-connex/route-connex-sdk-php), with the SDK's names. Calls go to the default App:

```php
use RouteConnex\Laravel\Facades\RouteConnex;

RouteConnex::routeConnex()->about()->get();
RouteConnex::routeConnex()->testAuth()->get();
RouteConnex::run()->_id_($runId)->get();
RouteConnex::microsoftSharepoint()->files()->get();
RouteConnex::microsoftSharepoint()->files()->post($body);
RouteConnex::microsoftSharepoint()->driveColumns()->get();
RouteConnex::hpeContentManager()->records()->get();
RouteConnex::hpeContentManager()->records()->post($body);
```

Another config App (or an [App model](#app-models), `RouteConnex::app($model)`):

```php
RouteConnex::app('hpe')->hpeContentManager()->records()->get();
```

Each call takes the SDK's arguments: `get(array $query = [], array $body = [], array $headers = [])` and, where the SDK allows it, `post(array $body = [], array $query = [], array $headers = [])`.

### App models

Multi-tenant apps keep their Apps in the database and pass the model to `RouteConnex::app()`. The package doesn't find Tenants: load the model (for a database per Tenant, from the Tenant's database) and pass it in. [Publish and run the migration](#publishing-and-running-the-migrations), then:

```php
use RouteConnex\Laravel\Models\RouteConnexApp;

$app = RouteConnexApp::create([
    'name' => 'SharePoint',
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
]);

RouteConnex::app($app)->microsoftSharepoint()->files()->get();
```

`RouteConnexApp` encrypts and hides `client_secret` and `callback_token`, so they need your `APP_KEY` and don't appear in arrays or JSON. It generates a 64-character hex Callback token on create when none is given. `name` is a label, not unique.

To use your own model, implement `RouteConnexAppContract` with the `InteractsWithRouteConnexApp` trait. It reads `client_id`, `client_secret`, `callback_token` and `base_url`, and each method can be overridden to read another column. It encrypts and generates nothing, so cast and fill those columns yourself; `RouteConnex::generateCallbackToken()` returns a new random token:

```php
use RouteConnex\Laravel\Concerns\InteractsWithRouteConnexApp;
use RouteConnex\Laravel\Contracts\RouteConnexAppContract;

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

Set your model (or a `RouteConnexApp` subclass) in `models.app`, so the package can find it again later. The contract's `getRouteConnexKey()` is the App's key (`null` while unsaved), and `getRouteConnexBaseUrl()` overrides the global `base_url` when it isn't empty. An empty client ID or secret throws `RouteConnexConfigurationException`, naming the class and key.

SDK clients are shared per class, key and Credentials. Tenants with the same IDs and different Credentials never share a client, and a changed secret takes effect on the next call.

On MongoDB, extend the model with [`mongodb/laravel-mongodb`](https://github.com/mongodb/laravel-mongodb)'s `DocumentModel` trait and set the subclass in `models.app`. The key is then a string ObjectId:

```php
class RouteConnexApp extends \RouteConnex\Laravel\Models\RouteConnexApp
{
    use \MongoDB\Laravel\Eloquent\DocumentModel;
}
```

### Meta

Calls to a System (`microsoftSharepoint()` and `hpeContentManager()`) also take `meta:`. The package sends it where the API expects it: `_meta_` as JSON in the query for `GET`, as an array in the body otherwise. A non-empty `meta:` replaces any `_meta_` already in the data.

```php
RouteConnex::microsoftSharepoint()->files()->post([
    'site_name' => 'Finance',
    'channel' => 'Invoices',
    'file_path' => $path,
    'file_name' => 'invoice.pdf',
], meta: ['invoice_id' => $invoice->id]);
```

### Responses

Calls return a `RouteConnexResponse`, which extends Laravel's HTTP client `Response`:

```php
$response = RouteConnex::routeConnex()->about()->get();

$response->status();
$response->successful();
$response->json('data');
```

### Exceptions

Every exception extends `RouteConnexException` and keeps the original as `getPrevious()`:

| Exception                            | When                                                                                                    |
|--------------------------------------|---------------------------------------------------------------------------------------------------------|
| `RouteConnexConfigurationException`  | no default App, an unknown App, or empty Credentials (config or model App)                                                    |
| `RouteConnexAuthenticationException` | the token request failed or issued no usable token; `response()` holds the API's answer, if any        |
| `RouteConnexConnectionException`     | the API couldn't be reached, or a request failed without a 4xx or 5xx answer                            |
| `RouteConnexRequestException`        | the API answered a call with 4xx or 5xx, including 401 and 403; `response()` holds the API's error body |

```php
use RouteConnex\Laravel\Exceptions\RouteConnexRequestException;

try {
    RouteConnex::microsoftSharepoint()->files()->post($body);
} catch (RouteConnexRequestException $e) {
    $e->response()->json();
}
```

Request data that can't be encoded as JSON (invalid UTF-8 in a body, or Meta on `GET`) throws `\InvalidArgumentException` instead, since it's a bug in the calling code.

To silence the package's exceptions, add `$exceptions->dontReport(RouteConnexException::class);` in the `withExceptions()` callback of `bootstrap/app.php`. This doesn't hide the `\InvalidArgumentException` above.

### The SDK client

`RouteConnex::sdk()` returns the default App's configured SDK `ErClient`, for anything the facade doesn't cover. Calls made through it return raw PSR-7 responses and throw the SDK's and Guzzle's own exceptions, not the package's.

### Testing

`Http::fake()` doesn't see these calls, because the SDK sends them with its own Guzzle client. Mock the facade instead:

```php
RouteConnex::shouldReceive('microsoftSharepoint->files->post')
    ->andReturn(new RouteConnexResponse(new Response(200, [], '{"data":"ok"}')));
```

`RouteConnex::fake()` is planned for a later release.

### Replacing how SDK clients are built

Bind a subclass of `RouteConnex\Laravel\RouteConnexClientFactory` in the container to change how the SDK client is built, for example to add a proxy. Its `newClient()` and `configure()` steps receive a `RouteConnexAppContract`:

```php
$this->app->bind(RouteConnexClientFactory::class, MyClientFactory::class);
```

## Development

No local PHP is needed: everything runs in Docker through `make` (run `make help` for the full list).

```bash
make build      # build the PHP 8.4 image
make install    # composer install
make test       # PHPStan, Pint, type coverage and Pest (SQLite)
make test-unit ARGS="--filter=Foo"   # Pest only (SQLite); ARGS are passed to Pest
make test-all   # Pest against SQLite, MySQL, PostgreSQL and MongoDB
make serve      # workbench app on http://localhost:8000
make testbench ARGS="route:list"   # artisan for the package
make shell      # shell inside the container
```

MySQL, PostgreSQL and MongoDB run as Compose services (the `databases` profile). `make test-mysql`, `make test-pgsql` and `make test-mongodb` start them as needed and accept `ARGS` too (e.g. `make test-mysql ARGS="--filter=DatabaseTest"`), and `make db-down` stops and removes them.

By default they run the latest tested versions (MySQL 8.4, PostgreSQL 18, MongoDB 8). Set `MYSQL_VERSION`, `POSTGRES_VERSION` or `MONGO_VERSION` to test another version, e.g. the minimum supported ones:

```bash
MYSQL_VERSION=8.0 make test-mysql
POSTGRES_VERSION=14 make test-pgsql
MONGO_VERSION=7.0 make test-mongodb
```

CI tests MySQL 8.0 and 8.4, PostgreSQL 14, 17 and 18, and MongoDB 7.0 and 8.2, each on Laravel 12 and 13. (CI uses 8.2 instead of the latest 8.x because current MongoDB 8 builds refuse to start on the GitHub runners' kernel; see the comment in `.github/workflows/tests.yml`.)

## Project docs

The vision, concepts, roadmap and planned features are described in [docs/](docs/README.md).

## Contributing

Thank you for considering contributing to RouteConnex! Please review our [contributing guide](.github/CONTRIBUTING.md) to get started.

## Credits

- [Leo Oberle](https://github.com/leocello)
- [All Contributors](../../contributors)

## License

RouteConnex is open-sourced software licensed under the [MIT license](LICENSE).
