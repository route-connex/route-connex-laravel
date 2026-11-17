# Multiple Apps

**Status:** planned
**Target version:** 0.x

## Summary

A consumer app can use several Apps. Single-tenant apps name them in config, like database connections. Multi-tenant apps keep them in the database, as `RouteConnexApp` models or as their own models that implement `RouteConnexAppContract`, and pass the model to `RouteConnex::app()`.

## Motivation

One App works with one System, so an app that uses several Systems needs several Apps, and a multi-tenant app needs its own Apps per Tenant. A consumer app with its own integration structure may also let its users add Systems to integrate with, each with its own Credentials stored in the database. Config alone can't hold those, and a global resolver would have to know about Tenants. Passing the App in leaves Tenant resolution to the consumer app: a model loaded from the Tenant's database already carries the right Credentials.

## Developer experience

Config Apps, for single-tenant apps:

```php
// config/route-connex-laravel.php
return [
    'base_url' => env('ROUTE_CONNEX_BASE_URL', 'https://app.routeconnex.com/api'),

    'default' => env('ROUTE_CONNEX_APP', 'default'),

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
            'base_url' => env('ROUTE_CONNEX_HPE_BASE_URL'), // optional, overrides the global one
        ],
    ],

    'models' => [
        'app' => \RouteConnex\Laravel\Models\RouteConnexApp::class,
    ],
];
```

```php
use RouteConnex\Laravel\Facades\RouteConnex;

RouteConnex::microsoftSharepoint()->files()->get([...]);           // the default App
RouteConnex::app('hpe')->hpeContentManager()->records()->get([...]); // a config App by name
```

Model Apps, for multi-tenant apps. With the shipped model, after publishing and running its migration (`php artisan vendor:publish --tag=route-connex-laravel-migrations`):

```php
use RouteConnex\Laravel\Models\RouteConnexApp;

$app = RouteConnexApp::create([
    'name' => 'SharePoint',
    'client_id' => $clientId,
    'client_secret' => $clientSecret, // encrypted; a Callback token is generated on create
]);

RouteConnex::app($app)->microsoftSharepoint()->files()->post([...], meta: [...]);
```

Or with the consumer app's own model:

```php
use RouteConnex\Laravel\Concerns\InteractsWithRouteConnexApp;
use RouteConnex\Laravel\Contracts\RouteConnexAppContract;

class Integration extends Model implements RouteConnexAppContract
{
    use InteractsWithRouteConnexApp;

    // The trait reads client_id, client_secret, callback_token and base_url.
    // Override its methods to map other columns:
    public function getRouteConnexClientId(): string
    {
        return $this->settings['route_connex_client_id'];
    }
}

RouteConnex::app($integration)->hpeContentManager()->records()->post([...]);
```

The consumer app's own model is set in `models.app` too, so the package can find it again when a [Callback](callbacks.md) arrives:

```php
'models' => [
    'app' => \App\Models\Integration::class,
],
```

On MongoDB, the consumer app extends the model and adds laravel-mongodb's `DocumentModel` trait, then sets it in config:

```php
class RouteConnexApp extends \RouteConnex\Laravel\Models\RouteConnexApp
{
    use \MongoDB\Laravel\Eloquent\DocumentModel;
}
```

## Scope

- `RouteConnex::app(string|RouteConnexAppContract $app)`: a string is the name of a config App, a model is used as it is.
- Calls without `app()` use the default App (`default` in config, `ROUTE_CONNEX_APP` in `.env`). If there is no default App, the default names an App that isn't configured, or its Credentials are empty, the call throws `RouteConnexConfigurationException`, so installations with only model Apps fail loudly instead of guessing.
- `RouteConnexAppContract` (`RouteConnex\Laravel\Contracts`): the App's key (used by [`RouteConnexRun` tracking](run-tracking.md) and [Callbacks](callbacks.md) to find the App again), client ID, client secret, Callback token and optional base URL.
- `InteractsWithRouteConnexApp` (`RouteConnex\Laravel\Concerns`): implements the contract from the `client_id`, `client_secret`, `callback_token` and `base_url` attributes; each method can be overridden.
- Config Apps are wrapped in a small value object that implements the same contract, so the rest of the package only works with the contract.
- `RouteConnexApp` (`RouteConnex\Laravel\Models`): the shipped Eloquent model on the `route_connex_apps` table, with `name`, `client_id`, `client_secret`, `callback_token` and `base_url`. The secret and the Callback token use the `encrypted` cast, and a Callback token is generated when an App is created without one. Its class is set in `models.app` in config (see [Overridable models and classes](overrides.md)). A consumer app that uses its own model sets that model there instead, since Callbacks find model Apps through `models.app`.
- A publish-only migration for `route_connex_apps`, under the existing `route-connex-laravel-migrations` tag.
- The bundled Laravel Boost skill shows both kinds of App and how to implement the contract on an existing model.

## Non-goals

- **No resolver or Tenant detection.** The package doesn't find a Tenant's Apps; the consumer app loads the model and passes it in.
- **No screens** for managing Apps (see the vision's non-goals).
- **No App status checks** against RouteConnex here; that's the Credential checks theme.
- **No encryption of the consumer app's own model.** Apps that implement the contract on their own model encrypt their Credentials themselves.

## Database & compatibility notes

- **Migration:** `route_connex_apps` with `id`, `name` (string), `client_id` (string), `client_secret` (text), `callback_token` (text), `base_url` (nullable string) and timestamps. The encrypted columns are `text` because encrypted values are longer than 255 characters. No database-specific column types.
- **MySQL 8.0+, PostgreSQL 14+, SQLite:** the plain migration and model work as they are.
- **MongoDB 7.0+:** the migration creates the collection (laravel-mongodb ignores column definitions). The shipped model extends Laravel's `Model`, so the consumer app extends it with laravel-mongodb's `DocumentModel` trait and sets it in `models.app`. `mongodb/laravel-mongodb` stays a Composer suggestion: only apps that use MongoDB install it, and the package's own code never uses the trait. The key is then a string ObjectId, which the contract must accept: App keys are `string|int`.
- **Tenancy:** the model uses its connection as usual, so on a database-per-Tenant setup it's read from and written to the Tenant's database. The package keeps no static state per App across Tenants: an SDK client cached for a model is keyed by its connection, class and key, so long-running workers (queues, Octane) don't mix Tenants.
- **Laravel 12 and 13:** `encrypted` casts, `publishesMigrations` and model events exist in both.

## Open questions

None at the moment.

## Related

- Roadmap: [Multiple Apps](../roadmap.md#now)
- Concepts: [App](../concepts.md#app), [Credentials](../concepts.md#credentials), [Callback token](../concepts.md#callback-token), [Tenant](../concepts.md#tenant)
- Features: [Laravel layer over the SDK](sdk-layer.md), [`RouteConnexRun` tracking](run-tracking.md), [Callbacks as events](callbacks.md), [Overridable models and classes](overrides.md)
- Decisions: [0002: RouteConnex prefix on everything the package adds](../decisions/0002-routeconnex-prefix.md), [0003: Apps come from config or from models, without a resolver](../decisions/0003-apps-from-config-or-models.md), [0006: MongoDB through the consumer app's model subclass](../decisions/0006-mongodb-through-app-subclass.md)
