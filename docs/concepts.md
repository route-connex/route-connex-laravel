# Concepts

The shared vocabulary of the package. Terms from the RouteConnex API keep the API's meaning; terms that only exist in this package say so.

Terms are written plainly here. Everything the package adds for them (classes, tables, config keys, events) carries the `RouteConnex` prefix (`route_connex_` for tables and keys), for example `RouteConnexRun`, `RouteConnexApp` or `route_connex_runs`, so it doesn't clash with the consumer app's own names.

## Glossary

### App

A RouteConnex API client: one pair of credentials (client ID and secret) issued by RouteConnex. Today an App works with one System, so an app that uses several Systems needs one App per System, and a multi-tenant app may need several per tenant. An App's status in RouteConnex is `active`, `inactive`, `expired` or `blocked`.

In this package, the consumer app names the Apps it uses, much like Laravel's database connections: a default App from `.env`, more Apps by name, or Apps supplied at runtime (for example per Tenant).

**Related:** Credentials, System, Tenant

### Callback

The request RouteConnex sends to the consumer app when a Run finishes. Its URL, HTTP method and any extra fields are configured for the App in RouteConnex, not per request. The body holds those extra fields plus `data`, which describes the Run.

The package turns a Callback into Laravel events. It only acts on a Callback that carries the right Callback token and whose Run ID matches a RouteConnexRun it created; it ignores the rest.

**Related:** Callback token, Run, RouteConnexRun

### Callback token

A random secret for an App. The package generates it for model Apps when they are created; for a config App, the admin sets it in `.env` until the Artisan commands can generate it. The consumer app's admin adds it to the App's callback configuration in RouteConnex, together with the callback URL and HTTP method; an Artisan command prints that configuration as JSON for a given App. RouteConnex sends it back in every Callback, and the package compares it in constant time before doing anything else. It is a simple check until RouteConnex authenticates Callbacks itself; the code keeps a TODO where that check will go.

**Related:** Callback, App

### Consumer app

A customer's Laravel app that installs this package.

**Related:** Tenant, App

### Credentials

The client ID and secret of an App, used by the SDK to get an OAuth2 access token from the RouteConnex API.

**Related:** App

### Meta

The `_meta_` JSON object a consumer app can attach to a request. RouteConnex stores it with the Run and returns it in the Run's data, including in Callbacks, so the consumer app can link the Run to its own records.

**Related:** Run, Callback

### RouteConnex API

The API that does the work. Reached through the PHP SDK (`route-connex/route-connex-sdk-php`), never called directly by this package.

**Related:** Service, App

### RouteConnexRun

This package's model for a Run it started: the local record in the consumer app's database, with the Run's ID, status and data. The consumer app can extend it (extra fields, another table name or connection) and set its own class in the config.

**Related:** Run, Run status, Callback

### Run

One execution of a Service on a System. It has an ID, a Run type, a Run status, the request data (including Meta) and, once finished, the response data. A Run finishes in the background and is reported through a Callback, unless it was a Sync call that RouteConnex could answer straight away.

**Related:** Run type, Run status, RouteConnexRun, Sync call

### Run status

Where a Run is:

- `created`, `queued`, `running`: not finished yet
- `succeeded`: finished without errors
- `dry-run`: finished without touching the target System. Dry runs are for testing the API: a RouteConnex client gets real data back from RouteConnex with no risk of sending anything to the target System.
- `failed`: failed inside RouteConnex, usually a RouteConnex or configuration issue
- `errored`: failed while working with the target System, for example a connectivity issue with the System or an error while the request was being sent to it

**Related:** Run

### Run type

The kind of Run a Service creates, such as uploading a file to Microsoft SharePoint. It's shown in the Run's data as `type`, with its name and System.

**Related:** Service, Run, System

### Service

Any call the RouteConnex API offers. Some don't involve a System and answer straight away (`about`, `test-auth`, used for credential checks). The others work with a System and create a Run of a Run type (for example list files, upload a file or list drive columns on Microsoft SharePoint, or list, read, create and delete records on HPE Content Manager). The SDK calls them endpoints.

**Related:** Run type, Run, System

### Sync call

A Service call made with the `sync` option, asking RouteConnex to answer straight away with the data. If it can't, the Run goes on in the background as usual. Only some Services support it.

**Related:** Service, Run

### System

A business system RouteConnex connects to, such as Microsoft SharePoint or HPE Content Manager. RouteConnex adds more Systems over time.

**Related:** App, Run type, Service

### Tenant

A tenant of the consumer app, in multi-tenant apps. A Tenant can have its own Apps, its own database connection and its own Callback routes. (RouteConnex has its own internal tenants; this package doesn't use them.)

**Related:** Consumer app, App, Tenancy preset

### Tenancy preset

The `tenancy` section of the package config, which sets the package up for a known tenancy setup in one place. A preset is a shipped subclass of `RouteConnexTenancy`, the replaceable class that tells the package whether tenancy is enabled, how to find a Tenant and how to run code in a Tenant's context. The first preset is [`stancl/tenancy`](https://tenancyforlaravel.com) version 3 with a database per tenant. An app with another setup binds its own `RouteConnexTenancy` subclass instead.

**Related:** Tenant

## How the terms relate

A consumer app (or each of its Tenants) has one or more Apps, each with its own Credentials. With an App, the consumer app calls Services of the RouteConnex API through this package and the SDK.

A Service that works with a System creates a Run of a Run type. The package records it as a RouteConnexRun, with any Meta the consumer app attached. When the Run finishes, RouteConnex sends a Callback; the package checks its Callback token, finds the matching RouteConnexRun, updates its Run status and fires events the consumer app listens to. A Sync call can skip the wait when RouteConnex answers straight away.

## Open questions

- **App scope.** Today an App is tied to one System. RouteConnex may change this so an App belongs to a customer and serves all of its Systems. The package must work either way, and RouteConnex's existing production Apps must keep working.
- **Callback authentication.** How RouteConnex will authenticate or sign Callbacks, replacing the Callback token check.
- **Dry runs.** The API doesn't let clients ask for a dry run yet. How they will, and how the package exposes it, is open.
