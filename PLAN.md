# `RouteConnexRun` tracking

**Status:** in progress
**Branch:** `feature/run-tracking` → **Target:** `release/0.x`
**Planned on:** `gandalf`
**Planning session:**

```shell
claude --ide --resume b904391f-1524-465a-86b7-0df02ed3d82a
```

## Implementation

- [x] **1. The `route_connex_runs` migration, `RouteConnexRunStatus` and the `RouteConnexRun` model**
  - **Model:** `opus`
  - **Effort:** `medium`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume b904391f-1524-465a-86b7-0df02ed3d82a` (gandalf)
  - **Why:** Database work that must pass on MySQL 8.0, PostgreSQL 14, MongoDB 7.0 and SQLite, including the MongoDB subclass and string `app_id` lookups across key types. The planning session already holds the design.
  - **Prompt:** Implement Step 1 of PLAN.md: The `route_connex_runs` migration, `RouteConnexRunStatus` and the `RouteConnexRun` model. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. The design is in PLAN.md → Decisions, `docs/features/run-tracking.md` (Scope, Database & compatibility notes) and decisions 0004 and 0006. Issue #6 added `RouteConnexAppContract`, `RouteConnexConfigApp`, `RouteConnexApp` with its migration, `models.app` and the `MongoRouteConnexApp` test fixture that `TestCase` sets on MongoDB. Read them and their tests first. Use the `package-scaffold`, `package-testing` and `package-compatibility` skills. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Add the step's items to `CHANGELOG.md` under `# feature/run-tracking`. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 2.
- [x] **2. `RouteConnexRunRecorder`: System calls store and return the Run**
  - **Model:** `opus`
  - **Effort:** `medium`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume b904391f-1524-465a-86b7-0df02ed3d82a` (gandalf)
  - **Why:** Changes the return type of every System endpoint and adds a public extension point. The connection rule (Run model, then the App's connection, then the default) has to be right for Tenants and across databases. It builds directly on Step 1's model.
  - **Prompt:** Implement Step 2 of PLAN.md: `RouteConnexRunRecorder`: System calls store and return the Run. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. The design is in PLAN.md → Decisions and `docs/features/run-tracking.md`. Step 1 added the `route_connex_runs` migration, `RouteConnexRunStatus`, `RouteConnexRun` (with its app check and `app` accessor), `models.run` and the `MongoRouteConnexRun` fixture. Issue #5 added `RouteConnexSystemEndpoint::call()` and `result()`, which this step changes. Read them and `tests/Feature/RouteConnexEndpointsTest.php` first. Use the `package-scaffold` and `package-testing` skills. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Add the step's items to `CHANGELOG.md` under `# feature/run-tracking`. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 3.
- [x] **3. `updateFromRouteConnex()` and `refreshFromRouteConnex()`**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume b904391f-1524-465a-86b7-0df02ed3d82a` (gandalf)
  - **Why:** Typical feature work with tests on top of Steps 1–2. The rules are fully specified in Decisions.
  - **Prompt:** Implement Step 3 of PLAN.md: `updateFromRouteConnex()` and `refreshFromRouteConnex()`. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. The rules are in PLAN.md → Decisions (Updating a Run) and `docs/features/callbacks.md` (step 5, which issue #9 will build on this method). Steps 1–2 added `RouteConnexRun`, `RouteConnexRunStatus`, `RouteConnexRunRecorder` (which already parses `data` from the API's answers) and the `RouteConnexRequestException` named constructors. Use the `package-testing` skill. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Add the step's items to `CHANGELOG.md` under `# feature/run-tracking`. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 4.
- [ ] **4. Boost skill, README and project docs**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `new`
  - **Resume:** –
  - **Why:** Documentation that must match the code exactly. A fresh session reads the code as it is instead of remembering what was intended.
  - **Prompt:** Implement Step 4 of PLAN.md: Boost skill, README and project docs. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Steps 1–3 added the `route_connex_runs` migration, `RouteConnexRunStatus`, the `RouteConnexRun` model (`app`, `updateFromRouteConnex()`, `refreshFromRouteConnex()`), `RouteConnexRunRecorder`, `RouteConnexRunCreated`, `models.run` and `runs.store_request`/`runs.store_response`, and the System endpoints now return the stored Run. Read them in `src/`, `config/`, `database/` and the tests before writing anything, and document what the code does. Use the `package-generate-skill` skill for the Boost skill. Never name consumer apps or private RouteConnex repos in the docs. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Add the step's items to `CHANGELOG.md` under `# feature/run-tracking`. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start the review.

## Context

GitHub issue #7: the `RouteConnexRun` model and its migration on MySQL, PostgreSQL, MongoDB and SQLite, Runs recorded when a call starts, and a way to update them. Runs store `app_name` or `app_id` ([decision 0004](docs/decisions/0004-runs-store-app-name-or-id.md)), and MongoDB works through the consumer app's model subclass ([decision 0006](docs/decisions/0006-mongodb-through-app-subclass.md)). Design: [`docs/features/run-tracking.md`](docs/features/run-tracking.md). It depends on #6, which is merged into `release/0.x`. The issue asks for the Boost skill and the feature doc status to be updated in this task.

Later issues build on this one: #8 (purge payloads), #9 (Callbacks as events, which update Runs and fire the status events), #10 (overridable classes, which says earlier tasks bind their own classes from the start), #11 (Artisan commands) and #13 (Sync calls).

State found during planning:

- Every System call goes through `RouteConnexSystemEndpoint::call()`. It puts the Meta in `_meta_` (JSON in the query for `GET`, an array in the body otherwise), sends the request and hands `result()` the App, the Service (`POST microsoft-sharepoint/files`), the request as sent (`['query' => ..., 'body' => ...]`, `_meta_` included), the Meta and the `RouteConnexResponse`. `result()` returns the response unchanged for now. The test fixture `RecordingFilesEndpoint` overrides `result()`.
- The System endpoints are `microsoftSharepoint()->files()` (`get`, `post`), `microsoftSharepoint()->driveColumns()` (`get`) and `hpeContentManager()->records()` (`get`, `post`). All of them return `RouteConnexResponse`.
- `run()->_id_($id)->get()` (`GET run/{id}`) already exists and returns `RouteConnexResponse`. It isn't a System endpoint, so it creates no Run.
- The API's create answer carries the Run ID in `data.id`. `GET run/{id}` answers with `data.status.id`, `data.type` (`{name, system}`) and `data.response_data` (SDK README and Boost skill).
- Config Apps are `RouteConnexConfigApp` (`final readonly`, key = name). Every other `RouteConnexAppContract` is a model App: its key is `string|int|null` (`null` while unsaved), and it may or may not be an Eloquent model (the `PlainApp` fixture isn't one).
- `RouteConnex::app()` takes a name or any contract. `RouteConnex::configApp()` is protected, so a config App is reached through `RouteConnex::app($name)->getApp()`. That throws `RouteConnexConfigurationException::unknownApp()` for an unknown name.
- `RouteConnexRequestException` takes a `RouteConnexResponse` and an optional previous exception, and its message is always "The RouteConnex API answered with status N.".
- `config/route-connex-laravel.php` has `models.app` only, and no `runs` section. `database/migrations` holds only `2026_01_01_000000_create_route_connex_apps_table.php`, published through `publishesMigrations` under `route-connex-laravel-migrations`.
- `TestCase` sets `models.app` to the `MongoRouteConnexApp` fixture when `DB_CONNECTION=mongodb`. The CI `databases` job runs one database service per job, and `pdo_sqlite` is always installed, so an extra SQLite connection is available on every database job.
- The endpoint tests (`RouteConnexEndpointsTest.php`) call System endpoints with `FakeApi::json()` answers that have no `data.id`, and run without migrations.

## Decisions

- **Branch:** `feature/run-tracking` from `release/0.x`, merged back into `release/0.x`.
- **Scope split** (user's choice):
  - This task ships the **complete** `route_connex_runs` migration as designed, including `purged_at` and the `status` + `finished_at` index, so a published migration never needs a follow-up. It also ships `runs.store_request`, `runs.store_response`, `RouteConnexRunCreated` and `refreshFromRouteConnex()`.
  - **#8** adds `RouteConnexRun::purgePayloads()` and `runs.purge_after_days`. `purged_at` exists and is cast here, but nothing sets it yet.
  - **#9** adds `RouteConnexRunStatusChanged`, `RouteConnexRunFinished`, `RouteConnexRunSucceeded` and `RouteConnexRunFailed`, and fires them from Callbacks and from `refreshFromRouteConnex()`. In this task, `refreshFromRouteConnex()` fires no events.
- **Migration** `database/migrations/2026_01_01_000001_create_route_connex_runs_table.php`, as in `run-tracking.md`: `id`, `run_id` (string, indexed), `app_name` (nullable string), `app_id` (nullable string, indexed), `service` (string), `type` (nullable string), `status` (string), `meta`, `request` and `response` (nullable `json`), `finished_at` and `purged_at` (nullable timestamps), timestamps, and an index on `['status', 'finished_at']`. There are no database enums, no `CHECK` constraints and no unique keys. On MongoDB the same migration creates the collection and its indexes.
- **`RouteConnexRunStatus`** (`RouteConnex\Laravel\Enums`): a string-backed enum with the cases `Created` (`created`), `Queued`, `Running`, `Succeeded`, `DryRun` (`dry-run`), `Failed` and `Errored`. `isFinished()` is true for `succeeded`, `dry-run`, `failed` and `errored`.
- **`RouteConnexRun`** (`RouteConnex\Laravel\Models`) extends Laravel's `Model`:
  - `$table = 'route_connex_runs'`
  - fillable: `run_id`, `app_name`, `app_id`, `service`, `type`, `status`, `meta`, `request`, `response`, `finished_at`
  - casts: `status` → `RouteConnexRunStatus`, `meta`/`request`/`response` → `array`, `finished_at`/`purged_at` → `datetime`
  - On `saving`, exactly one of `app_name` and `app_id` must be set (non-empty), or it throws `RouteConnexConfigurationException` (new named constructor, e.g. `invalidRunApp()`, saying both or neither were set)
  - Its class is set in `models.run` (default `RouteConnexRun::class`). The package always uses the configured class.
- **`$run->app`** (a cached `Attribute` accessor) returns the `RouteConnexAppContract` that started the Run:
  - from `app_name`: the config App, through `RouteConnex::app($name)->getApp()`. An unknown name throws the existing `unknownApp()` exception.
  - from `app_id`: `config('route-connex-laravel.models.app')::query()->find($app_id)`, on the App model's own connection. A missing App throws `RouteConnexConfigurationException` (new named constructor, e.g. `missingRunApp()`, naming the `models.app` class and the `app_id`) (user's choice).
  - The string `app_id` has to find integer keys on PostgreSQL and ObjectId keys on MongoDB. The tests cover both.
- **`RouteConnexRunRecorder`** (`RouteConnex\Laravel\Runs`, next to the future `Callbacks\` and `Tenancy\` namespaces) stores the Run after the API call. It's resolved from the container on every call (`app(RouteConnexRunRecorder::class)`), so binding a subclass replaces it, as with `RouteConnexClientFactory`. It needs no explicit binding. Its public methods:
  - `checkApp(RouteConnexAppContract $app): void`: throws `RouteConnexConfigurationException` (new named constructor, e.g. `appWithoutKey()`, naming the class) when the App isn't a config App and its key is `null` or empty. `RouteConnexSystemEndpoint::call()` calls it **before** sending the request, so no Run is started in RouteConnex that can't be tracked (user's choice). Calls that create no Run (`about`, `test-auth`, `run/{id}`) still work with unsaved Apps.
  - `record(RouteConnexAppContract $app, string $service, array $request, array $meta, RouteConnexResponse $response): RouteConnexRun`, in small `protected` steps that a subclass can override one at a time, for example to fill extra columns: read `data`, the Run ID, the status and the type; build the attributes; pick the connection; save; fire `RouteConnexRunCreated`.
- **What `record()` stores:**
  - `run_id`: `data.id`, as a string. If it's missing or empty, nothing is stored and it throws `RouteConnexRequestException` with the response (user's choice). #13 (Sync calls) will change this path.
  - `status`: `data.status.id`. When the create answer has no `status` at all, it's `created`; a `status` without a known `id` is unknown. A value the enum doesn't know throws `RouteConnexRequestException` and nothing is stored (user's choice). A new RouteConnex status is a package release (a new enum case), with no migration.
  - `type`: `data.type.name`, or `null` (user's choice).
  - `app_name` (the name of a `RouteConnexConfigApp`) or `app_id` (the string key of any other App). They're told apart with `instanceof RouteConnexConfigApp`.
  - `service` and `meta`, as received.
  - `request`: `['query' => ..., 'body' => ...]` exactly as sent, `_meta_` included (user's choice). `null` when `runs.store_request` is `false`.
  - `response`: the answer's `data` object (user's choice). `null` when `runs.store_response` is `false`.
- **`RouteConnexRequestException`** gains an optional message (a `?string $message = null` constructor argument after `$previous`, so existing `new RouteConnexRequestException($response, $e)` calls keep working, and the current message stays the default) and named constructors, e.g. `noRunId(RouteConnexResponse)` and `unknownRunStatus(RouteConnexResponse, mixed $status)`, so these failures carry the response like an error answer does.
- **Connection** (as in `run-tracking.md`):
  - A Run model that sets its own connection (`getConnectionName()` on a new instance isn't `null`) always uses it.
  - Otherwise, a Run of an App that is an Eloquent model goes on that App's connection, so a Tenant's Runs stay in the Tenant's database.
  - Otherwise (a config App, or an App that isn't a model), the default connection.
  - On MongoDB, a Run subclass for a split setup must set `$connection` (as the docs' example does), or the rule would put it on the App's SQL connection.
- **Endpoints:** `RouteConnexSystemEndpoint::call()` and `result()` return `RouteConnexRun`. `result()` hands everything to the recorder. The public `get()`/`post()` of the files, drive columns and records endpoints return `RouteConnexRun` (the base class in the signature; the instance is the `models.run` class). Nothing has been released yet (`v0.1.0-rc`), so the type change is allowed. `run()->_id_()->get()`, `about` and `test-auth` keep returning `RouteConnexResponse`.
- **`RouteConnexRunCreated`** (`RouteConnex\Laravel\Events`): `SerializesModels`, with a public `RouteConnexRun $run`, fired through the `Event` facade once the Run is saved. It doesn't use `Dispatchable`, which only ships with `laravel/framework`; `illuminate/queue` is required for `SerializesModels` (user's choice, during Step 2).
- **Config:** `models.run` (Step 1), and a `runs` section with `store_request` and `store_response`, both `true` (Step 2). `purge_after_days` comes with #8.
- **Updating a Run** (user's choice: the same rule as Callbacks). `RouteConnexRun::updateFromRouteConnex(RouteConnexRunStatus $status, array $data): bool` is public, so #9's `updateRun()` step reuses it:
  - If the Run's current status is finished, the status is final. Nothing is updated and it returns `false`. A **different** finished status (`failed` after `succeeded`) also logs a warning; a late unfinished one (`running` after `succeeded`) is ignored silently.
  - Otherwise it sets `status`, `type` (`data.type.name` when present), `response` (the `data` object, unless `runs.store_response` is `false`), and `finished_at = now()` the first time a finished status arrives (the API sends no finish time). Then it saves and returns whether the status changed.
  - The caller parses the status, so each caller decides how to report an unknown one.
- **`$run->refreshFromRouteConnex(): static`**: `GET run/{run_id}` through `RouteConnex::app($this->app)`, then `updateFromRouteConnex()`. It returns `$this`, like `Model::refresh()`. A missing or unknown `data.status.id` throws `RouteConnexRequestException::unknownRunStatus()` and leaves the Run unchanged (unlike a create answer, where a missing status means `created`). API failures throw the usual package exceptions. It fires no events until #9.
- **MongoDB:** the package's code never uses `DocumentModel`. A test fixture `MongoRouteConnexRun extends RouteConnexRun` uses it, and `TestCase` sets it in `models.run` when `DB_CONNECTION=mongodb`. A second fixture with `$connection = 'mongodb'` covers a Run on MongoDB whose App is on SQLite.
- **Cross-connection tests** use an extra in-memory SQLite connection (e.g. `tenant`), migrated with `migrate --database=tenant --path=... --realpath`, so they run on every database job.
- **Status:** `docs/features/run-tracking.md` and its roadmap theme move to `in progress`. They stay there until #8 ships the purge, and become `shipped (vX.Y.Z)` at release.

## Steps

### Step 1: The `route_connex_runs` migration, `RouteConnexRunStatus` and the `RouteConnexRun` model

**Status:** done

- [x] `database/migrations/2026_01_01_000001_create_route_connex_runs_table.php`, as in Decisions
- [x] `src/Enums/RouteConnexRunStatus.php`, with `isFinished()`
- [x] `src/Models/RouteConnexRun.php`: table, fillable, casts, the `saving` check for `app_name`/`app_id`, and the `app` accessor
- [x] `src/Exceptions/RouteConnexConfigurationException.php`: `invalidRunApp()` and `missingRunApp()`
- [x] `config/route-connex-laravel.php`: `models.run`, and the `models` comment mentions the Run model
- [x] `tests/Fixtures/MongoRouteConnexRun.php`, set in `models.run` by `tests/TestCase.php` on MongoDB
- [x] Cross-connection setup: `tests/TestCase.php` defines an in-memory SQLite `tenant` connection on every database, `migrateTenantConnection()` in `tests/Pest.php` migrates it, and `tests/Fixtures/TenantRouteConnexApp.php` is an App model on it
- [x] `composer.json`: `test:types` runs with `--memory-limit=512M` (added with the user's agreement, see the notes)
- [x] Tests (`tests/Feature/DatabaseTest.php` and a new `tests/Feature/RouteConnexRunModelTest.php`), always through `config('route-connex-laravel.models.run')`:
  - the migration creates `route_connex_runs`, and the published migrations include it (`route-connex-laravel-migrations`)
  - `models.run` defaults to `RouteConnexRun` on the `route_connex_runs` table
  - `status`, `meta`, `request`, `response`, `finished_at` and `purged_at` round-trip with their casts, including nested arrays and `null`
  - `isFinished()` for every case
  - saving with both or neither of `app_name`/`app_id` throws `RouteConnexConfigurationException`
  - two Runs can share a `run_id`
  - `$run->app` returns the config App by `app_name`, and the model App by `app_id` (an integer key on SQL, including PostgreSQL, and an ObjectId on MongoDB)
  - an unknown config App or a missing model App throws `RouteConnexConfigurationException`
  - `$run->app` loads the App from the App model's own connection, including a Run on MongoDB whose App is on SQLite (on the MongoDB job)

**Notes:**

- **Type coverage memory (unplanned, agreed with the user):** after this step, `composer test:types` ran past PHP's default 128M limit in the Docker image. Memory adds up across files, so no single file was at fault; with more memory coverage is 100%. The script now passes the plugin's own `--memory-limit=512M`, which exists since `pest-plugin-type-coverage` v4.0.0, so the `prefer-lowest` lane has it too.
- **MySQL reorders JSON keys:** MySQL's `json` type stores object keys shortest first (`body` before `query`, `_meta_` before `file_name`), so a stored `request`, `response` or `meta` reads back with the same values but possibly another key order. The round-trip test compares them with `toEqual`. Step 4 should mention it where the docs describe the payload columns.
- `$run->app` reads `models.app` with a `class-string<Model&RouteConnexAppContract>` docblock, as the tests do, rather than validating the class at runtime.

**Validate:** `make test`, `make test-all`, then the minimum versions: `MYSQL_VERSION=8.0 make test-mysql`, `POSTGRES_VERSION=14 make test-pgsql`, `MONGO_VERSION=7.0 make test-mongodb`
**Commit:** `Add the RouteConnexRun model, its status enum and the route_connex_runs migration`

### Step 2: `RouteConnexRunRecorder`: System calls store and return the Run

**Status:** done

- [x] `src/Runs/RouteConnexRunRecorder.php`: `checkApp()` and `record()` with protected steps, as in Decisions
- [x] `src/Events/RouteConnexRunCreated.php`, with `SerializesModels`, fired through the `Event` facade
- [x] `composer.json`: require `illuminate/queue: ^12.0||^13.0` (agreed with the user, see the notes)
- [x] `src/Exceptions/RouteConnexRequestException.php`: optional message, `noRunId()` and `unknownRunStatus()`
- [x] `src/Exceptions/RouteConnexConfigurationException.php`: `appWithoutKey()`
- [x] `src/Endpoints/RouteConnexSystemEndpoint.php`: `checkApp()` before sending, and `call()`/`result()` return `RouteConnexRun` through the recorder
- [x] `src/Endpoints/MicrosoftSharepoint/RouteConnexFilesEndpoint.php`, `RouteConnexDriveColumnsEndpoint.php` and `src/Endpoints/HpeContentManager/RouteConnexRecordsEndpoint.php`: return `RouteConnexRun`
- [x] `src/Facades/RouteConnex.php`: check the `@method` docblocks still match (they return the endpoint groups, so nothing changed)
- [x] `config/route-connex-laravel.php`: `runs.store_request` and `runs.store_response`, with their comment
- [x] `tests/Fixtures/RecordingFilesEndpoint.php` and `tests/Feature/RouteConnexEndpointsTest.php`: follow the new return type. The System calls answer with a Run ID and run with the migrations.
- [x] Test helpers: `FakeApi::run()` answers with a Run ID, `runModel()` moved from `RouteConnexRunModelTest.php` to `tests/Pest.php` so every test file can use it, and two Run fixtures with their own connection: `TenantRouteConnexRun` (`tenant`) and `MongoConnectionRouteConnexRun` (`DocumentModel`, `mongodb`)
- [x] Tests (a new `tests/Feature/RouteConnexRunRecorderTest.php`), through the facade and `FakeApi`, on every database:
  - a System call (`GET` and `POST`) stores one Run and returns it: `run_id`, `status` (`created` when the answer has none), `type` from `data.type.name`, `service`, `meta`, `request` as sent with `_meta_`, `response` as the answer's `data`
  - a config App sets `app_name`, a model App (the shipped model and a consumer model with the trait) sets `app_id`, and a non-model App with a key sets `app_id` on the default connection
  - `RouteConnexRunCreated` fires once with the stored Run
  - `runs.store_request` and `runs.store_response` set to `false` store `null`
  - an answer without `data.id`, or with an unknown status, throws `RouteConnexRequestException` with the response, and stores nothing
  - an API error stores nothing
  - an unsaved model App throws `RouteConnexConfigurationException` before any request is sent (`FakeApi` saw no request), while `test-auth` still works with it
  - connection: a Run of a model App on the `tenant` SQLite connection is stored there; a Run model with its own connection wins; a config App's Run uses the default connection
  - a bound `RouteConnexRunRecorder` subclass overriding one step (e.g. filling an extra attribute) is used

**Notes:**

- **`illuminate/queue` (unplanned, agreed with the user):** the plan had `RouteConnexRunCreated` use `Dispatchable` and `SerializesModels`. `Dispatchable` is in `Illuminate\Foundation` (only in `laravel/framework`), and `SerializesModels` is in `illuminate/queue`, which wasn't required. The event now keeps `SerializesModels` (queued listeners restore the Run by key, as `callbacks.md` expects), `illuminate/queue` is required, and the recorder fires the event through the `Event` facade.
- **`RouteConnexRequestException`'s message** is the third constructor argument, after `$previous`, not the second as planned, so `RouteConnexClient`'s `new RouteConnexRequestException($response, $e)` keeps working. The named constructors use `message:`.
- **The recorder's protected steps** are `attributes()`, `data()`, `runId()`, `status()`, `type()`, `appAttributes()`, `newRun()`, `connection()`, `save()` and `dispatchCreated()`. A numeric `data.id` is stored as a string. Step 3 didn't reuse the recorder's status parsing: a refresh has stricter rules (no status is an error, not `created`), so the model parses the answer itself.
- **The MongoDB-only test** (a Run on MongoDB whose App is on SQLite) is skipped on the other databases, so SQL jobs report 1 skipped.
- The connection test for a model App on `tenant` sets `models.run` to the plain `RouteConnexRun`: the MongoDB fixture has no connection of its own, so it would follow the App onto SQLite, which is the documented reason a split MongoDB setup sets `$connection`.

**Validate:** `make test`, `make test-all`, then the minimum versions: `MYSQL_VERSION=8.0 make test-mysql`, `POSTGRES_VERSION=14 make test-pgsql`, `MONGO_VERSION=7.0 make test-mongodb`
**Commit:** `Store a RouteConnexRun for every System call and return it`

### Step 3: `updateFromRouteConnex()` and `refreshFromRouteConnex()`

**Status:** done

- [x] `src/Models/RouteConnexRun.php`: `updateFromRouteConnex()` and `refreshFromRouteConnex()`, as in Decisions
- [x] Test helpers: `createRun()`, `freshRun()` and `createModelApp()` moved from `RouteConnexRunModelTest.php` to `tests/Pest.php`, so the new test file can use them
- [x] Tests (a new `tests/Feature/RouteConnexRunRefreshTest.php`), on every database:
  - `refreshFromRouteConnex()` sends `GET run/{run_id}` with the Run's own App (config and model App), then updates `status`, `type`, `response` and `finished_at`, and returns the Run
  - `finished_at` is set once, when a finished status first arrives, and isn't set for unfinished ones
  - a finished Run keeps its status, type, response and `finished_at` on a later unfinished status (no warning) or a different finished status (a warning is logged)
  - `updateFromRouteConnex()` returns whether the status changed
  - `runs.store_response` set to `false` keeps `response` `null`
  - a missing or unknown status throws `RouteConnexRequestException` and leaves the stored Run unchanged
  - a Run on the `tenant` connection is updated there

**Notes:**

- The model parses the `GET run/{id}` answer itself (`data.status.id`, or a bare `data.status` string) instead of calling the recorder, because the recorder defaults a missing status to `created` and a refresh must throw. `updateFromRouteConnex()` takes the parsed `RouteConnexRunStatus` and the `data` array, so #9's Callback handler parses its own body and reuses it.
- `updateFromRouteConnex()` on an unfinished Run saves even when the status didn't change, since the type and response may have: it returns whether the *status* changed, as planned.
- A repeat of the same finished status (a duplicate Callback, say) returns `false` without a warning; only a *different* finished status warns.
- Pint's `fully_qualified_strict_types` rule imports `GuzzleHttp\Psr7\Response` in the new test file; `make lint` fixed it.

**Validate:** `make test`, `make test-all`, then the minimum versions: `MYSQL_VERSION=8.0 make test-mysql`, `POSTGRES_VERSION=14 make test-pgsql`, `MONGO_VERSION=7.0 make test-mongodb`
**Commit:** `Update Runs from RouteConnex with refreshFromRouteConnex()`

### Step 4: Boost skill, README and project docs

**Status:** todo

- [ ] `resources/boost/skills/route-connex-laravel-development/SKILL.md` (with the `package-generate-skill` skill): System calls return a `RouteConnexRun`, reading it (`run_id`, `status`, `meta`, `app`), `refreshFromRouteConnex()`, `RouteConnexRunCreated`, publishing the migration, `models.run`, `runs.store_*`, the MongoDB subclass, the connection rule, and binding a `RouteConnexRunRecorder` subclass
- [ ] `README.md`: a Runs section (migration, reading and refreshing Runs, the event, config, MongoDB), and the Meta and Responses sections updated for System calls returning a Run
- [ ] `docs/features/run-tracking.md`: Status `in progress`, plus the details settled in Decisions (what `response` and `type` hold, `checkApp()` before the call, the no-Run-ID and unknown-status exceptions, `updateFromRouteConnex()` and its rule, no status events or purge until #9 and #8, the recorder's namespace), and an "Already in the code" section that says what ships here
- [ ] `docs/roadmap.md`: the `RouteConnexRun` tracking theme is `in progress`
- [ ] `docs/features/callbacks.md`: `updateRun()` uses `RouteConnexRun::updateFromRouteConnex()`. `docs/features/overrides.md`: the recorder's namespace. `docs/features/sdk-layer.md`: `result()` now stores the Run. `docs/concepts.md`, only if a term changed.

**Validate:** `make test`
**Commit:** `Document RouteConnexRun tracking in the Boost skill, README and project docs`

## Acceptance Criteria

- [ ] `php artisan vendor:publish --tag=route-connex-laravel-migrations` publishes the `route_connex_runs` migration, which runs on MySQL 8.0+, PostgreSQL 14+, MongoDB 7.0+ and SQLite.
- [ ] Every System call (`files()->get/post`, `driveColumns()->get`, `records()->get/post`) stores a `RouteConnexRun` (the `models.run` class) once the API returns a Run ID, and returns it.
- [ ] The Run holds `run_id`, exactly one of `app_name`/`app_id`, `service`, `type`, `status` (as `RouteConnexRunStatus`), `meta`, `request` and `response`. `runs.store_request`/`runs.store_response` turn the payloads off.
- [ ] `RouteConnexRunCreated` fires once per stored Run.
- [ ] A Run is stored on the Run model's own connection, else on the model App's connection, else on the default one.
- [ ] `$run->app` returns the config App or the model App (integer, string or ObjectId key) and throws when it's gone.
- [ ] No Run ID, an unknown status or an App without a key fail with package exceptions, and store nothing.
- [ ] `$run->refreshFromRouteConnex()` updates the Run from RouteConnex, a finished status stays final, and `finished_at` is set when the Run finishes.
- [ ] A `RouteConnexRun` subclass with `DocumentModel` set in `models.run` works on MongoDB, including with its App on an SQL connection.
- [ ] A bound `RouteConnexRunRecorder` subclass replaces the shipped one.
- [ ] `make test` and `make test-all` pass, and the tests pass at the minimum database versions.
- [ ] The Boost skill, README and `docs/` describe Run tracking as implemented. `run-tracking.md` and its roadmap theme are `in progress`.
- [ ] `CHANGELOG.md` lists the task's items under `# feature/run-tracking`, and `version` is `feature/run-tracking`.

## Review

Findings from Phase 3 (filled in by the reviewing agent).
