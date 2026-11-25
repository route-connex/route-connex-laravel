# Multiple Apps

**Status:** in progress
**Branch:** `feature/apps` → **Target:** `release/0.x`
**Planned on:** `gandalf`
**Planning session:**

```shell
claude --ide --resume 3230ebb2-8ff3-485a-b267-2eb2a11bacda
```

## Implementation

- [x] **1. `RouteConnexAppContract`, the trait and `RouteConnex::app()` for model Apps**
  - **Model:** `opus`
  - **Effort:** `medium`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume 3230ebb2-8ff3-485a-b267-2eb2a11bacda` (gandalf)
  - **Why:** Sets the contract that run tracking, Callbacks and the commands all build on, and changes the types of a public extension point (`RouteConnexClientFactory`). The client cache has to be right for Tenants. The planning session already holds the design.
  - **Prompt:** Implement Step 1 of PLAN.md: `RouteConnexAppContract`, the trait and `RouteConnex::app()` for model Apps. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md; the design is in PLAN.md → Decisions and `docs/features/apps.md`. Issue #5 added `RouteConnex` (the facade root, with `app(string $name)` and one client per config App), `RouteConnexClient`, `RouteConnexConfigApp`, `RouteConnexClientFactory` and `RouteConnexSystemEndpoint::result()`; read them and their tests first. Use the `package-scaffold` and `package-testing` skills. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Add the step's items to `CHANGELOG.md` under `# feature/apps`. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 2.
- [x] **2. The `RouteConnexApp` model, its migration and Callback token generation**
  - **Model:** `opus`
  - **Effort:** `medium`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume 3230ebb2-8ff3-485a-b267-2eb2a11bacda` (gandalf)
  - **Why:** Database work that must pass on MySQL 8.0, PostgreSQL 14, MongoDB 7.0 and SQLite, including the MongoDB subclass pattern and encrypted casts. It builds directly on Step 1's contract and trait.
  - **Prompt:** Implement Step 2 of PLAN.md: The `RouteConnexApp` model, its migration and Callback token generation. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md; the design is in PLAN.md → Decisions and `docs/features/apps.md` (Database & compatibility notes) and decision 0006. Step 1 added `RouteConnexAppContract`, `InteractsWithRouteConnexApp` and `RouteConnex::app(string|RouteConnexAppContract)`. Use the `package-scaffold`, `package-testing` and `package-compatibility` skills. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Add the step's items to `CHANGELOG.md` under `# feature/apps`. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 3.
- [x] **3. Boost skill, README and project docs**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `new`
  - **Resume:** `claude --ide --resume 452ef0e2-6d03-44bc-9021-01396eeea73b` (gandalf)
  - **Why:** Documentation that must match the code exactly. A fresh session reads the code as it is instead of remembering what was intended.
  - **Prompt:** Implement Step 3 of PLAN.md: Boost skill, README and project docs. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Steps 1–2 added `RouteConnexAppContract`, `InteractsWithRouteConnexApp`, `RouteConnex::app(string|RouteConnexAppContract)` with its client cache, `callback_token` and `models.app` in config, `RouteConnex::generateCallbackToken()`, the `RouteConnexApp` model and the `route_connex_apps` migration. Read them in `src/`, `config/`, `database/` and the tests before writing anything, and document what the code does. Use the `package-generate-skill` skill for the Boost skill. Never name consumer apps or private RouteConnex repos in the docs. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Add the step's items to `CHANGELOG.md` under `# feature/apps`. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start the review.

## Context

GitHub issue #6: named Apps in config, `RouteConnexAppContract`, the `RouteConnexApp` model with its migration and Callback token generation, `RouteConnex::app()` and the `models.app` config key. Design: [`docs/features/apps.md`](docs/features/apps.md), [decision 0003](docs/decisions/0003-apps-from-config-or-models.md), and [0004](docs/decisions/0004-runs-store-app-name-or-id.md) and [0006](docs/decisions/0006-mongodb-through-app-subclass.md) for what the App key and the MongoDB subclass must allow. It depends on #5, which is merged into `release/0.x`. The issue asks for the Boost skill and the feature doc status to be updated in this task.

State found during planning:

- `RouteConnex::app(string $name)` already returns the client of a config App, and `RouteConnex` caches one `RouteConnexClient` per App name in `$clients`. `RouteConnexConfigApp` (`final readonly`) holds the name, client ID, client secret and base URL, with the global `base_url` already applied by `fromConfig()`. Neither has a Callback token.
- `RouteConnexClientFactory::make()`, `newClient()` and `configure()`, `RouteConnexClient::__construct()` and `getApp()`, and `RouteConnexSystemEndpoint::result()` are all typed to `RouteConnexConfigApp`. The test fixture `FakeClientFactory` overrides `newClient(RouteConnexConfigApp)`.
- The config has `base_url`, `default` and `apps.default` (`client_id`, `client_secret`). There is no `callback_token` and no `models` key yet. `ExampleTest` asserts the exact `apps.default` array.
- The package doesn't require `illuminate/database` (only `illuminate/http` and `illuminate/support`).
- `database/migrations` holds only the skeleton's `create_route_connex_laravel_placeholder_table` migration, which `DatabaseTest` uses. The `route-connex-laravel-migrations` publish tag already publishes the whole folder through `publishesMigrations`.
- `mongodb/laravel-mongodb` is already in `require-dev` and `suggest`, and `TestCase` registers its service provider when `DB_CONNECTION=mongodb`.
- `apps.md` says model clients are "keyed by connection, class and key". That isn't safe with a database per Tenant: every Tenant uses the same connection name (e.g. `tenant`) and the same IDs, so two Tenants' Apps would share a client in queue workers and Octane, and a rotated secret would keep the old client. This task caches differently and updates the doc (see Decisions).

## Decisions

- **Branch:** `feature/apps` from `release/0.x`, merged back into `release/0.x`.
- **`RouteConnexAppContract`** (`RouteConnex\Laravel\Contracts`), as in `apps.md`, with prefixed method names so they don't clash with the consumer app's own model methods:
  - `getRouteConnexKey(): string|int|null`: the App's key. It's `null` for an unsaved model. A config App's key is its name. Run tracking tells config Apps from model Apps with `instanceof RouteConnexConfigApp`, not through the contract.
  - `getRouteConnexClientId(): ?string` and `getRouteConnexClientSecret(): ?string`: nullable, so a model with empty Credentials gets the package's `RouteConnexConfigurationException` instead of a `TypeError`. A subclass can narrow them to `string` (as in the doc's example).
  - `getRouteConnexCallbackToken(): ?string`: `null` means the App has no token (Callbacks will answer `403`).
  - `getRouteConnexBaseUrl(): ?string`: `null` means the global `base_url`.
- **`InteractsWithRouteConnexApp`** (`RouteConnex\Laravel\Concerns`): implements the contract from the `client_id`, `client_secret`, `callback_token` and `base_url` attributes and `getKey()`. Each method can be overridden. It generates nothing and encrypts nothing.
- **`RouteConnex::app()` takes any `RouteConnexAppContract`** (user's choice), including unsaved models and non-Eloquent objects, so a consumer app can check Credentials before saving them. It doesn't check the class against `models.app`. Run tracking will refuse to store a Run for an App with no key.
- **Model App clients are cached by class, key and a hash of the Credentials and base URL** (user's choice). The SDK client (which holds the token) is cached in the `RouteConnex` singleton under the class, the key, and a hash (e.g. `sha256`) of the client ID, client secret and the App's own base URL. Each `app($model)` call returns a new `RouteConnexClient` bound to the App passed in, around the cached SDK client, so `getApp()` is always the caller's model. This means:
  - Tenants with the same connection name and the same IDs but different Credentials never share a client
  - a rotated secret takes effect on the next call
  - an unsaved model (key `null`) works
  - there's no static state; the cache lives in the singleton's instance, as config Apps' clients already do

  Config Apps keep the current cache by name. `apps.md` is updated to describe this.
- **Missing Credentials on a model App:** an empty or `null` client ID or secret throws `RouteConnexConfigurationException`, with a message naming the class and the key (or that it's unsaved), the same way config Apps do. Empty strings count as unset, as in `RouteConnexConfigApp`.
- **Base URL:** the App's own base URL when it isn't empty, else the global `base_url`, else the SDK's default, for both kinds of App. `RouteConnexClientFactory` applies the global fallback in a small protected step, because a model doesn't know the config. `RouteConnexConfigApp::fromConfig()` can keep applying it.
- **Types widen from `RouteConnexConfigApp` to `RouteConnexAppContract`** in `RouteConnexClientFactory` (`make()`, `newClient()`, `configure()`), `RouteConnexClient` (constructor, `getApp()`) and `RouteConnexSystemEndpoint::result()`. This breaks the signatures of factory subclasses, but nothing has been released yet (`v0.1.0-rc`).
- **`RouteConnexConfigApp`** implements the contract and gains `?string $callbackToken` (a constructor argument after `baseUrl`, default `null`), read from `callback_token` in its config entry (empty counts as unset).
- **Config:**
  - `apps.default.callback_token` = `env('ROUTE_CONNEX_CALLBACK_TOKEN')` (Step 1)
  - `models.app` = `RouteConnex\Laravel\Models\RouteConnexApp::class` (Step 2). `models.run` is added later by run tracking.
- **Callback token generation** (user's choice): `RouteConnex::generateCallbackToken(): string` on the facade root returns `bin2hex(random_bytes(32))` (64 hex characters, the format the docs already suggest for `.env`). `RouteConnexApp` calls it on `creating` when no token is given. The trait doesn't generate one; consumer models and the future `route-connex:callback-token` command call `RouteConnex::generateCallbackToken()`.
- **`RouteConnexApp`** (`RouteConnex\Laravel\Models`) extends Laravel's `Model` and uses the trait:
  - `$table = 'route_connex_apps'`
  - fillable: `name`, `client_id`, `client_secret`, `callback_token`, `base_url`
  - `client_secret` and `callback_token` use the `encrypted` cast and are `$hidden`, so they don't appear in arrays, JSON or logs
  - `name` isn't unique: it's a label, and Callbacks use the key
- **Migration:** `database/migrations/2026_01_01_000000_create_route_connex_apps_table.php`, as in `apps.md`. It has `id`, `name` (string), `client_id` (string), `client_secret` (text), `callback_token` (text), `base_url` (nullable string) and timestamps, with no database-specific types. It replaces the skeleton's placeholder migration, which is deleted, and `DatabaseTest` moves to the new table. It ships under the existing `route-connex-laravel-migrations` tag; `publishesMigrations` gives it a fresh timestamp on publish.
- **MongoDB:** the package's code never uses `DocumentModel`. The tests prove the documented pattern: a fixture `RouteConnexApp` subclass with `DocumentModel`, set in `models.app` by `TestCase` when `DB_CONNECTION=mongodb`. The model tests always go through `config('route-connex-laravel.models.app')`. The key is then a string ObjectId, which the contract's `string|int|null` allows.
- **Composer:** require `illuminate/database: ^12.0||^13.0`, needed for the model. `encrypted` casts, model events and `publishesMigrations` exist in both.
- **Status:** `docs/features/apps.md` and its roadmap theme move to `in progress`. They become `shipped (vX.Y.Z)` at release.

## Steps

### Step 1: `RouteConnexAppContract`, the trait and `RouteConnex::app()` for model Apps

**Status:** done

- [x] `src/Contracts/RouteConnexAppContract.php`, as in Decisions
- [x] `src/Concerns/InteractsWithRouteConnexApp.php`, as in Decisions
- [x] `src/Apps/RouteConnexConfigApp.php`: implements the contract (key = name), adds `callbackToken` read from `callback_token`
- [x] `config/route-connex-laravel.php`: `apps.default.callback_token` from `ROUTE_CONNEX_CALLBACK_TOKEN`, with its comment
- [x] `src/RouteConnexClientFactory.php`, `src/RouteConnexClient.php`, `src/Endpoints/RouteConnexSystemEndpoint.php`: types widened to the contract. The factory falls back to the global `base_url` in a protected step.
- [x] `src/RouteConnex.php`: `app(string|RouteConnexAppContract $app)`. Model Apps get the Credentials check and the SDK client cache by class, key and Credentials hash, with a new `RouteConnexClient` bound to the App passed in on every call.
- [x] `src/Exceptions/RouteConnexConfigurationException.php`: `missingAppCredentials()` for contract Apps, naming the class and key (`App\Models\Integration #5`) or `(unsaved)`
- [x] `src/Facades/RouteConnex.php`: `@method` for `app(string|\RouteConnex\Laravel\Contracts\RouteConnexAppContract $app)`
- [x] `tests/Fixtures/FakeClientFactory.php` and `tests/Fixtures/RecordingFilesEndpoint.php`: follow the new `newClient()` and `result()` types
- [x] `phpstan.neon.dist`: ignore `trait.unused` for `InteractsWithRouteConnexApp` until Step 2's model uses it
- [x] Tests (a new `tests/Feature/RouteConnexModelAppsTest.php`, plus updates to `RouteConnexAppsTest.php` and `ExampleTest.php`), with unsaved Eloquent fixtures using the trait (one overriding a method to read another column) and a plain object implementing the contract, so no table is needed:
  - `app($model)` builds the SDK client from the model's Credentials
  - the base URL comes from the model, then the global one, then the SDK's default
  - `getApp()` returns the model passed in
  - the same model twice reuses the SDK client
  - two Apps with the same class and key but different Credentials (two Tenants) get different SDK clients
  - changing the secret gives a new SDK client
  - an unsaved model and a non-Eloquent App work
  - empty or `null` Credentials throw `RouteConnexConfigurationException`
  - a bound factory subclass receives the contract
  - config Apps implement the contract (key = name) and read `callback_token`; empty counts as unset
  - `config('route-connex-laravel.apps.default')` includes `callback_token`
- [x] `CHANGELOG.md`: create `# feature/apps` above `---` and `# v0.1.0-rc`, with this step's items

Notes:

- **Unplanned files:** PHPStan won't analyse a trait that nothing in `src/` uses (`trait.unused`). The ignore in `phpstan.neon.dist` is limited to that file and that error. Step 2 must remove it: PHPStan fails on unmatched ignores, so the build breaks as soon as `RouteConnexApp` uses the trait and the ignore is still there. `RecordingFilesEndpoint` (a test fixture overriding `result()`) also needed the new type.
- **Fixtures:** `tests/Fixtures/IntegrationApp.php` (an Eloquent model with the trait, never saved), `SettingsIntegrationApp.php` (overrides `getRouteConnexClientId()` to read a `settings` array) and `PlainApp.php` (not a model).
- **Cache key:** `RouteConnex::appCacheKey()` is `class|key|sha256(serialize([client ID, secret, App base URL]))`. Config Apps passed in as objects use the same cache; by name they keep the cache by name.
- The trait treats empty strings as `null`, like `RouteConnexConfigApp`. Its key is `null` unless `getKey()` is a string or an int.

**Validate:** `make test`: passed (PHPStan, Pint, type coverage 100%, 162 tests).

**Commit:** `Accept App models in RouteConnex::app() through RouteConnexAppContract`

### Step 2: The `RouteConnexApp` model, its migration and Callback token generation

**Status:** done

- [x] `composer.json`: require `illuminate/database: ^12.0||^13.0`; `make composer ARGS="update"`
- [x] `src/RouteConnex.php`: `generateCallbackToken(): string`; `@method` on the facade
- [x] `src/Models/RouteConnexApp.php`, as in Decisions, generating a Callback token on `creating` when none is given
- [x] `phpstan.neon.dist`: remove Step 1's `trait.unused` ignore now that the model uses the trait
- [x] `config/route-connex-laravel.php`: `models.app`, with a comment that a consumer app's own model or a MongoDB subclass goes here
- [x] `database/migrations/2026_01_01_000000_create_route_connex_apps_table.php`; delete the placeholder migration
- [x] `tests/Fixtures/MongoRouteConnexApp.php` (extends `RouteConnexApp`, uses `DocumentModel`); `tests/TestCase.php` sets `models.app` to it when `DB_CONNECTION=mongodb`, and sets a fixed `app.key` for the encrypted casts
- [x] Tests (`tests/Feature/RouteConnexAppModelTest.php`, and `DatabaseTest.php` moved to `route_connex_apps`; `ExampleTest.php` needed no change), always through `config('route-connex-laravel.models.app')`:
  - the migration creates `route_connex_apps`
  - `create()` generates a 64-hex token when none is given and keeps a given one
  - `RouteConnex::generateCallbackToken()` returns a new 64-hex string each time
  - the secret and the token are encrypted at rest (the raw column isn't the plain value) and decrypt on read
  - they're hidden from `toArray()`
  - a model read back from the database works with `RouteConnex::app()`
  - the key is an int on SQL and a string on MongoDB
  - `models.app` defaults to `RouteConnexApp` (read from the package's config file, since `TestCase` overrides it on MongoDB)
  - the migrations publish tag still publishes `database/migrations` (the existing `ServiceProviderTest` dataset already covers it)
- [x] `CHANGELOG.md`: this step's items under `# feature/apps`

Notes:

- **Test app key:** Testbench's app has no `APP_KEY`, and the `encrypted` casts need one, so `TestCase` sets a fixed test key. Consumer apps always have their own.
- **Model:** `$fillable` and `$hidden` as planned. The casts are in `casts()`, which Laravel 12 and 13 both support. Token generation is in `booted()` on `creating`: it runs when `getRouteConnexCallbackToken()` is `null` (missing or empty) and calls `app(RouteConnex::class)->generateCallbackToken()`.
- **Config:** Pint turned the FQCN in `models.app` into a `use` import at the top of `config/route-connex-laravel.php` (`fully_qualified_strict_types`).
- **Raw rows:** the encryption test reads them with `DB::table()`, cast to an array, so it works the same on SQL and MongoDB.

**Validate:**

- `make test`
- `make test-all`
- `MYSQL_VERSION=8.0 make test-mysql`, `POSTGRES_VERSION=14 make test-pgsql` and `MONGO_VERSION=7.0 make test-mongodb`
- `make composer ARGS="update --prefer-lowest"`, then `make test-unit`, then `make composer ARGS="update"` again

Results:

- `make test`: passed (PHPStan, Pint, type coverage 100%, 171 tests)
- `make test-all`: passed on SQLite, MySQL, PostgreSQL and MongoDB (171 tests each)
- MySQL 8.0, PostgreSQL 14 and MongoDB 7.0: passed
- prefer-lowest `make test-unit`: passed. Dependencies are back on stable (Laravel 13.44.1).

**Commit:** `Add the RouteConnexApp model, its migration and Callback token generation`

### Step 3: Boost skill, README and project docs

**Status:** done

- [x] `resources/boost/skills/route-connex-laravel-development/SKILL.md` (with the `package-generate-skill` skill):
  - config Apps with `callback_token`
  - `RouteConnexApp` with the migration publish tag
  - implementing the contract on an existing model, with the trait and overrides
  - `models.app`, the MongoDB subclass and `RouteConnex::generateCallbackToken()`
  - remove the "Apps are config-only" rule
- [x] `README.md`: Configuration (`ROUTE_CONNEX_CALLBACK_TOKEN`, `models.app`), the migrations section (what it creates), and a Multiple Apps usage section covering config Apps, `RouteConnexApp`, the consumer app's own model and MongoDB
- [x] `docs/features/apps.md`:
  - status `in progress`
  - the contract's nullable return types and `null` key
  - `RouteConnex::generateCallbackToken()`
  - the client cache by class, key and Credentials hash, replacing "keyed by its connection, class and key"
  - `RouteConnex::app()` accepts any contract implementation
  - "Already in the code" updated
- [x] `docs/roadmap.md`: Multiple Apps `in progress`. Check `docs/concepts.md` (Callback token), `docs/features/overrides.md`, `docs/features/run-tracking.md` and `docs/features/callbacks.md` for statements this task made untrue, and fix them.
- [x] `CHANGELOG.md`: this step's items under `# feature/apps`

**Validate:** `make test`; the README and skill examples match the code (class names, method names, config keys, env names)

**Commit:** `Document Multiple Apps in the Boost skill, README and project docs`

## Acceptance Criteria

- [ ] `RouteConnex::app('name')` still returns a config App's client, and config Apps read `callback_token` from config (`ROUTE_CONNEX_CALLBACK_TOKEN` for the default App).
- [ ] `RouteConnex::app($app)` accepts any `RouteConnexAppContract` (the shipped model, a consumer model with the trait, an unsaved model, a non-Eloquent object) and calls the API with its Credentials and base URL.
- [ ] Two Apps with the same class and key but different Credentials never share an SDK client, and a changed secret takes effect on the next call.
- [ ] Missing Credentials on a model App throw `RouteConnexConfigurationException`.
- [ ] `RouteConnexApp` stores Apps in `route_connex_apps`, encrypts and hides the secret and the Callback token, and generates a 64-hex Callback token on create when none is given.
- [ ] `RouteConnex::generateCallbackToken()` returns a new 64-hex token.
- [ ] `models.app` defaults to `RouteConnexApp`, and a MongoDB subclass set there works.
- [ ] The migration publishes under `route-connex-laravel-migrations` and runs on MySQL 8.0+, PostgreSQL 14+, MongoDB 7.0+ and SQLite. The tests pass on all four, at their minimum versions, and on Laravel 12's `prefer-lowest` lane.
- [ ] The Boost skill, README and `docs/` describe both kinds of App as implemented. `apps.md` and its roadmap theme are `in progress`.
- [ ] `CHANGELOG.md` lists the task's items under `# feature/apps`, and `version` is `feature/apps`.

## Review

Findings from Phase 3 (filled in by the reviewing agent).
