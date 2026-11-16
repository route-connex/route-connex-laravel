# Vision

## Problem

RouteConnex connects an organisation's apps to business systems such as HPE Content Manager and Microsoft SharePoint, through the RouteConnex API. A PHP app can call that API with `route-connex/route-connex-sdk-php`, but the SDK is framework-agnostic, so every Laravel app has to build the same plumbing around it:

- configuring the API credentials and binding the client into the container
- keeping track, in its own database, of the Runs it starts
- receiving RouteConnex callbacks and turning them into something the app can react to, such as events or jobs, when a Run finishes

This package does that work once, the Laravel way, on top of the SDK.

## Who it's for

Developers of Laravel apps whose organisation is a RouteConnex customer. The package needs RouteConnex API credentials and is no use without them.

- It is for customers' Laravel apps, not for RouteConnex's own services.
- Those apps can be multi-tenant: their own credentials per tenant (often more than one set, one per System), data on database connections chosen at runtime, tables that don't follow Laravel's naming conventions, and their own integration framework that drives the work. The package must fit into them, not the other way round.
- The package is public on Packagist so customers can install it with `composer require`, like the SDK. The README says a RouteConnex account is required.

## Goals

- **A Laravel-flavoured API over the SDK.** A facade, a container binding and config, with a fluent Laravel-style layer on top of `route-connex/route-connex-sdk-php`, not the raw SDK endpoints. When the SDK gains new API endpoints, this package follows.
- **Credentials from `.env` by default, from anywhere when needed.** A single-tenant app sets its credentials in `.env`, one pair or several named ones (one per App). A multi-tenant app can supply them at runtime, for example per tenant from its own settings storage.
- **Runs tracked in the app's database.** The package stores the Runs it starts as `RouteConnexRun` records and keeps their status up to date, on every supported database (MySQL, PostgreSQL, MongoDB and SQLite).
- **Sync calls for quick services.** For services the API can answer straight away, the app can ask for a synchronous response and get the data back in the same call. If the API can't answer synchronously, the call goes on as a normal Run.
- **Credential checks.** The app can check that a set of credentials works against the RouteConnex API, for example when a tenant saves them.
- **Callbacks become Laravel events.** RouteConnex callbacks are received and turned into events (or jobs) the app can listen to. The callback route can live in the app's own route group, such as a tenant's routes, instead of the package's.
- **Callback setup from Artisan.** An Artisan command takes an App and prints the JSON to add to that App's callback configuration in RouteConnex: the callback URL, the HTTP method and the extra fields, including the Callback token. App names can repeat across Tenants, so when a Tenancy preset is on, the command also takes the Tenant and doesn't guess.
- **Everything is overridable.** The models are set in config, so an app can extend them to add fields and change the table name or connection. Every other class the package uses can be replaced with the app's own subclass.
- **Ready for `stancl/tenancy`.** A `tenancy` section in the config sets the package up for apps that use [`stancl/tenancy`](https://tenancyforlaravel.com) with little or no extra code. Version 3 with a database per tenant comes first. Apps with their own tenancy setup plug it in through the overridable classes instead.
- **A building block, not a framework.** An app with its own integration structure can call the package from it without handing over the flow.

## Non-goals

- **No admin UI.** The package ships no screens (Filament, Blade or Livewire) for managing credentials or browsing Runs. Apps build their own.
- **No RouteConnex account management.** Customers, routes, connections and agents are set up in RouteConnex, not through this package.
- **No direct API calls.** All API access goes through the SDK. If the API offers something the SDK doesn't yet, it's added to the SDK first.
- **No support outside the matrix.** Apps on PHP below 8.3 or Laravel below 12 aren't supported.

## Guiding principles

- **Laravel-native first.** Use Laravel's own tools (config, the container, facades, events, routes, migrations, publish tags) before adding abstractions of our own.
- **Defaults that work, everything overridable.** A single-tenant app works with `.env` credentials and the defaults. A complex app changes behavior through config and subclasses, never by forking the package.
- **No global assumptions.** Nothing assumes one tenant, one set of credentials, one database connection or Laravel's table naming conventions.
- **Every supported database.** Anything that touches the database works on MySQL 8.0+, PostgreSQL 14+, MongoDB 7.0+ and SQLite, not only the latest versions.
- **Thin over the SDK.** The SDK talks to the API; this package adds only the Laravel integration on top.
- **Small and focused.** Add only what a real consumer needs.
- **Prefixed names.** Every model, table, event and config key the package adds starts with `RouteConnex` (`route_connex_` for tables and keys), so it doesn't clash with the app's own names, such as an existing `Run` model.

## Where it fits

RouteConnex connects an organisation's apps to business systems through the RouteConnex API. For a Laravel app, the pieces are:

- **The RouteConnex API:** does the work. It authenticates with an OAuth2 client ID and secret. A request starts a Run, which RouteConnex completes and reports back through a callback. Some services can also answer synchronously.
- **`route-connex/route-connex-sdk-php`:** the framework-agnostic PHP client for the API.
- **`route-connex/route-connex-laravel`** (this package): the Laravel integration on top of the SDK.

The flow is: app → this package → SDK → RouteConnex API. When a Run finishes, RouteConnex calls back → this package → events in the app.

The SDK is still being developed alongside the API. When it changes, this package is updated to match.

## Open questions

- **Other goals for 1.0.** Not decided yet. Candidates raised so far: queued Runs, polling as a fallback when no callback arrives, and more Artisan commands.
- **Other `stancl/tenancy` modes.** Whether single-database tenancy (scoping by `tenant_id`) and `stancl/tenancy` version 4 are supported, and when.
