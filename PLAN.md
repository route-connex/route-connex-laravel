# Laravel layer over the SDK

**Status:** in progress **Branch:** `feature/sdk-layer` → **Target:** `release/0.x`
**Planned on:** `gandalf`
**Planning session:**

```shell
claude --ide --resume 03d9dabd-20f9-4527-8635-11f1ccfd9d5d
```

## Implementation

- [x] **1. Dependencies, config, Apps by name and the client factory**
  - **Model:** `opus`
  - **Effort:** `medium`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume 03d9dabd-20f9-4527-8635-11f1ccfd9d5d` (gandalf)
  - **Why:** Sets the shape every later step and feature builds on (manager, App-bound client, factory seam, exceptions base). The planning session already holds the design and the SDK's internals.
  - **Prompt:** Implement Step 1 of PLAN.md: Dependencies, config, Apps by name and the client factory. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md; the design is in PLAN.md → Decisions. Read the SDK in `../route-connex-sdk-php` (`ErClient`, `Concerns/AuthenticatesApp.php`) and use the `package-scaffold`, `package-testing` and `package-compatibility` skills. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Add the step's items to `CHANGELOG.md` under `# feature/sdk-layer`. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 2.
- [x] **2. The mirrored endpoint tree and `RouteConnexResponse`**
  - **Model:** `sonnet`
  - **Effort:** `high`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume 03d9dabd-20f9-4527-8635-11f1ccfd9d5d` (gandalf)
  - **Why:** Typical feature work with tests, but many small classes and a test harness (Guzzle `MockHandler` through the factory) that have to be right. It builds directly on Step 1's classes.
  - **Prompt:** Implement Step 2 of PLAN.md: The mirrored endpoint tree and `RouteConnexResponse`. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md; the design is in PLAN.md → Decisions. Step 1 added the `RouteConnex` manager with `app()` and `sdk()`, the App-bound `RouteConnexClient`, `RouteConnexConfigApp` and `RouteConnexClientFactory`. Mirror the SDK endpoints in `../route-connex-sdk-php/src/Endpoints`; use the `package-testing` skill. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Add the step's items to `CHANGELOG.md` under `# feature/sdk-layer`. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 3.
- [x] **3. Package exceptions wrapping the SDK's and Guzzle's**
  - **Model:** `sonnet`
  - **Effort:** `high`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume 03d9dabd-20f9-4527-8635-11f1ccfd9d5d` (gandalf)
  - **Why:** A precise mapping table with one test per row; the error cases of the SDK's `authenticate()` need care. Same area and test harness as Step 2.
  - **Prompt:** Implement Step 3 of PLAN.md: Package exceptions wrapping the SDK's and Guzzle's. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md; the mapping is in PLAN.md → Decisions. Step 1 added `RouteConnexException` and `RouteConnexConfigurationException`; Step 2 added the endpoint tree, the single place where requests are sent, and the `MockHandler` test harness. Use the `package-testing` skill. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Add the step's items to `CHANGELOG.md` under `# feature/sdk-layer`. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 4.
- [x] **4. Boost skill, README and project docs**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `new`
  - **Resume:** `claude --ide --resume de36c929-6f91-4387-811f-3148d7c1ef3b` (gandalf)
  - **Why:** Documentation that must match the code exactly; a fresh session reads the code as it is instead of remembering what was intended.
  - **Prompt:** Implement Step 4 of PLAN.md: Boost skill, README and project docs. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Steps 1–3 added the config, the `RouteConnex` facade with `app()`, the mirrored endpoint tree, `RouteConnexResponse`, `RouteConnexClientFactory` and the package exceptions; read them in `src/` and the tests before writing anything, and document what the code does. Use the `package-generate-skill` skill for the Boost skill. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Add the step's items to `CHANGELOG.md` under `# feature/sdk-layer`. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start the review.
- [x] **5. Review fix: Pint and `composer.json` formatting**
  - **Model:** `haiku`
  - **Effort:** `low`
  - **Session:** `new`
  - **Resume:** `claude --ide --resume 1c96612e-9291-4ea4-a231-ec12b2005b9d` (gandalf)
  - **Why:** Mechanical: run the formatter and fix three lines of indentation. The review session is long and about other things.
  - **Prompt:** Implement Step 5 of PLAN.md: Review fix: Pint and `composer.json` formatting. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md; the background is Review findings 1 and 2. Run `make lint` and check that it changes only formatting; don't edit code by hand. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 6.
- [x] **6. Review fix: token margin, request-data errors, token error message, `result()` test**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `new`
  - **Resume:** `claude --ide --resume 927e5213-903c-4b2f-9a4d-9e22b343582e` (gandalf)
  - **Why:** Small, well-specified changes to `RouteConnexClient` and the endpoints, each with a test, using the existing `FakeApi` harness. Independent of Step 5's context.
  - **Prompt:** Implement Step 6 of PLAN.md: Review fix: token margin, request-data errors, token error message, `result()` test. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md; the background is Review findings 3–6 and the "Review fixes" entries in Decisions. The code is in `src/RouteConnexClient.php`, `src/Endpoints/RouteConnexSystemEndpoint.php` and `src/Exceptions/RouteConnexAuthenticationException.php`; the SDK is in `vendor/route-connex/route-connex-sdk-php/src` (`Concerns/AuthenticatesApp.php`, `Concerns/RunsHttpRequests.php`); tests use `fakeRouteConnexApi()` and `tests/Fixtures/FakeApi.php`. Use the `package-testing` skill. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run `make lint` before validating. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 7.
- [x] **7. Review fix: docs**
  - **Model:** `haiku`
  - **Effort:** `low`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume 927e5213-903c-4b2f-9a4d-9e22b343582e` (gandalf)
  - **Why:** Short doc edits whose wording is given in the step. The Step 6 session knows the new `\InvalidArgumentException` behaviour the docs must describe; switch with `/model`.
  - **Prompt:** Implement Step 7 of PLAN.md: Review fix: docs. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md; the background is Review findings 7–10. Step 6 made request data that can't be encoded as JSON throw `\InvalidArgumentException`, and a token close to expiry renewed before a call; read `src/RouteConnexClient.php` and `src/Endpoints/RouteConnexSystemEndpoint.php` before writing. Use the `package-generate-skill` skill for the Boost skill. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start the review.

## Context

GitHub issue #5: the `RouteConnex` facade and container binding over `route-connex/route-connex-sdk-php`, with config (`base_url` and the default App from `.env`), a fluent API mirroring the SDK's endpoints, and package exceptions wrapping the SDK's and Guzzle's. Design: [`docs/features/sdk-layer.md`](docs/features/sdk-layer.md) and [decision 0008](docs/decisions/0008-facade-mirrors-sdk-and-exceptions.md). It's the base for Multiple Apps, `RouteConnexRun` tracking, Callbacks and Overrides, so it's built with room for them.

State found during planning:

- The package is still the skeleton: `RouteConnex` is an empty singleton behind the facade, and the config holds only a `placeholder` key.
- The SDK is `v1.1.0` on Packagist (PHP `^8.1`, `guzzlehttp/guzzle` `^7.9`). Endpoints: `routeConnex()->about()` (GET, no auth), `routeConnex()->testAuth()` (GET), `run()->_id_($id)` (GET), `microsoftSharepoint()->files()` (GET, POST), `microsoftSharepoint()->driveColumns()` (GET), `hpeContentManager()->records()` (GET, POST). Each returns a raw PSR-7 response. Its exceptions are `NotAuthenticatedException`, `InvalidEndpointException` and `InvalidHttpMethodException` (all `\DomainException`); Guzzle throws on 4xx/5xx and connection errors.
- **Issue #4 is not done:** `ErClient::authenticate()` always requests a new token, and nothing sets the token from outside. Token caching in Laravel's cache is out of scope. One `ErClient` per App is kept in the singleton, so the token is reused within a request or worker, and the SDK requests a new one when it expires.
- The SDK builds its own Guzzle client (`new Client` in the constructor, `protected Client $client`), so there's no way to pass in a handler, and `Http::fake()` doesn't see its requests. Tests go through `RouteConnexClientFactory` (see Decisions).

## Decisions

- **Branch:** `feature/sdk-layer` from `release/0.x`, merged back into `release/0.x`.
- **Scope follows the issue, built toward the end result.** System endpoints return a `RouteConnexResponse` in this task. They all go through one System-endpoint base class whose single protected step turns the API response into the call's result, so the `RouteConnexRun` tracking task only changes that step to store and return a `RouteConnexRun`. Nothing is released before that task lands.
- **`RouteConnex::app(string $name)` ships now, for config Apps.** The Multiple Apps task widens it to `string|RouteConnexAppContract` and adds the contract and the models. Config Apps are held in a small readonly value object, `RouteConnexConfigApp` (`RouteConnex\Laravel\Apps`): name, client ID, client secret, base URL. Multiple Apps makes it implement the contract.
- **Manager and App-bound client**, like Laravel's `DatabaseManager` and `Connection`:
  - `RouteConnex` (the facade root, container singleton) reads config lazily, resolves Apps by name and caches one `RouteConnexClient` per App name. Its explicit methods `routeConnex()`, `run()`, `microsoftSharepoint()`, `hpeContentManager()` and `sdk()` go to the default App's client; `app($name)` returns another App's client. No `__call` magic, so the types are known.
  - `RouteConnexClient` (`RouteConnex\Laravel`) wraps one App and its SDK `ErClient`, and exposes the endpoint tree and `sdk()`.
- **`RouteConnexClientFactory`** (`RouteConnex\Laravel`, from [overrides.md](docs/features/overrides.md)) builds the SDK `ErClient` for an App. It's resolved from the container each time a new App's client is built, so a binding replaces it. It has small protected steps (build the `ErClient`, then configure it: base URL). Tests bind a subclass that returns a test-only `ErClient` subclass whose protected Guzzle client uses a `MockHandler` with a history middleware. No SDK change.
- **Config** (`config/route-connex-laravel.php`, replacing the placeholder):
  - `base_url`: `env('ROUTE_CONNEX_BASE_URL', 'https://app.routeconnex.com/api')`
  - `default`: `env('ROUTE_CONNEX_APP', 'default')`
  - `apps.default`: `client_id` (`ROUTE_CONNEX_CLIENT_ID`), `client_secret` (`ROUTE_CONNEX_CLIENT_SECRET`)
  - an App may set its own `base_url`. The App's `base_url` wins if not empty, then the global one, then the SDK's default.
  - `callback_token` and `models` are not added here; Multiple Apps and run-tracking add them.
- **Config is checked when an App is first used, not at boot**, so an app without Credentials still boots (`package:discover`, CI). `RouteConnexConfigurationException` when: the default App is empty, the name isn't in `apps`, or `client_id` or `client_secret` is empty.
- **Mirrored tree, exact SDK names**, including `run()->_id_($id)`. Only the HTTP methods the SDK allows on each endpoint are offered.
- **Full SDK signatures, plus `meta:` on System endpoints:**
  - System endpoints (`microsoftSharepoint()->files()`, `->driveColumns()`, `hpeContentManager()->records()`): `get(array $query = [], array $body = [], array $headers = [], array $meta = [])` and `post(array $body = [], array $query = [], array $headers = [], array $meta = [])`.
  - Other endpoints (`routeConnex()->about()`, `->testAuth()`, `run()->_id_($id)`): `get(array $query = [], array $body = [], array $headers = [])`.
  - `meta:` goes where the API expects it: `_meta_` as JSON in the query for `GET`, as an array in the body otherwise. A non-empty `meta:` replaces any `_meta_` already in the data; an empty one leaves the data as it is.
- **`RouteConnexResponse`** (`RouteConnex\Laravel\Http`) extends `Illuminate\Http\Client\Response` around the SDK's PSR-7 response (`json()`, `status()`, `successful()`, …). `throw()` is not overridden: Guzzle already throws on 4xx/5xx, so a returned response is always successful.
- **Exceptions** (`RouteConnex\Laravel\Exceptions`), all extending `RouteConnexException` (a `\RuntimeException`), each keeping the original as `previous`:

  | Where             | What happened                                                                                                                       | Package exception                                                        |
  |-------------------|-------------------------------------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------|
  | Config            | missing default, unknown App, empty Credentials                                                                                     | `RouteConnexConfigurationException`                                      |
  | Getting the token | Guzzle `RequestException` with a response (e.g. 401 from `auth/token`)                                                              | `RouteConnexAuthenticationException`, carrying the `RouteConnexResponse` |
  | Getting the token | SDK `NotAuthenticatedException`, or a token response without a token (`ErrorException`/`TypeError` from the SDK's `authenticate()`) | `RouteConnexAuthenticationException`                                     |
  | Anywhere          | Guzzle `ConnectException`, or another `GuzzleException` without a response                                                          | `RouteConnexConnectionException`                                         |
  | Service call      | Guzzle `RequestException` with a response, including 401/403 after a token was issued                                               | `RouteConnexRequestException`, carrying the `RouteConnexResponse`        |

  To tell token failures from Service failures without inspecting URLs, the client calls the SDK's `authenticate()` itself (in its own `try`) before a request whose SDK endpoint `requiresAuth()` for that method, then sends the request. All requests go through one place in `RouteConnexClient` (or the endpoint base) that does this.
- **`RouteConnex::sdk()`** returns the configured `ErClient` as it is: calls made through it throw the SDK's and Guzzle's own exceptions. The docs say so.
- **Class names follow decision 0002** (every class starts with `RouteConnex`). The mirrored tree mirrors the SDK's names under `RouteConnex\Laravel\Endpoints`: abstract `RouteConnexEndpoint` and `RouteConnexSystemEndpoint`; groups `RouteConnexRouteConnexEndpoint`, `RouteConnexRunEndpoint`, `RouteConnexMicrosoftSharepointEndpoint`, `RouteConnexHpeContentManagerEndpoint`; leaves `RouteConnex\RouteConnexAboutEndpoint`, `RouteConnex\RouteConnexTestAuthEndpoint`, `Run\RouteConnexRunIdEndpoint`, `MicrosoftSharepoint\RouteConnexFilesEndpoint`, `MicrosoftSharepoint\RouteConnexDriveColumnsEndpoint`, `HpeContentManager\RouteConnexRecordsEndpoint`.
- **Composer:** require `route-connex/route-connex-sdk-php: ^1.1` and `illuminate/http: ^12.0||^13.0`. Guzzle comes through the SDK (`^7.9`), which keeps Laravel 12's `prefer-lowest` lane on 7.9+.
- **Only the config placeholder is removed** (the `placeholder` key and its test). The placeholder command, view, translation, route file and migration stay for the tasks that replace them.
- **Status:** `docs/features/sdk-layer.md` and its roadmap theme move to `in progress`. They become `shipped (vX.Y.Z)` at release.
- **Review fixes** (from the Phase 3 review; the user agreed to Steps 5–7):
  - **Token margin:** before a call that needs a token, `RouteConnexClient::authenticate()` requests a new token when the current one expires within 30 seconds (a protected `$tokenRefreshMargin = 30`, so a subclass can change it), so the SDK's own check inside the request can't find it expired.
  - **Request data that can't be encoded as JSON** throws `\InvalidArgumentException`, not a package exception: it's a programming error in the calling code, and `dontReport(RouteConnexException::class)` must not hide it. Meta on `GET` rethrows the `JsonException` as `\InvalidArgumentException` (keeping it as `previous`); Guzzle's own `InvalidArgumentException` (already an `\InvalidArgumentException`) is rethrown as it is, not wrapped as a `RouteConnexConnectionException`.
  - **Token error message:** "The RouteConnex API answered the token request with status {status}." instead of "rejected", since a 5xx is an outage, not bad Credentials. The class and the `rejected()` factory keep their names.
  - **Fixes to unreleased behaviour add no CHANGELOG items**; an existing item is reworded only if it became wrong.
  - **Guzzle 8:** the user opens an SDK issue to allow `guzzlehttp/guzzle: ^8.0` (agents don't post to GitHub); the docs say that until then the package needs Guzzle 7.

## Steps

### Step 1: Dependencies, config, Apps by name and the client factory

**Status:** done

- [x] `composer.json`: require `route-connex/route-connex-sdk-php: ^1.1` and `illuminate/http: ^12.0||^13.0`; `make composer ARGS="update"`
- [x] `config/route-connex-laravel.php`: `base_url`, `default`, `apps.default` as in Decisions; remove `placeholder`
- [x] `src/Exceptions/RouteConnexException.php` and `src/Exceptions/RouteConnexConfigurationException.php`
- [x] `src/Apps/RouteConnexConfigApp.php`: readonly value object built from an App's config entry
- [x] `src/RouteConnexClientFactory.php`: builds and configures the SDK `ErClient` for a `RouteConnexConfigApp`, in small protected steps
- [x] `src/RouteConnexClient.php`: holds the App and its `ErClient`; `getApp()` and `sdk()` for now
- [x] `src/RouteConnex.php`: lazy config, `app(string $name)`, default App resolution, one `RouteConnexClient` per App name, `sdk()`; `RouteConnexConfigurationException` cases from Decisions
- [x] `src/RouteConnexServiceProvider.php`: no change needed: the existing `singleton(RouteConnex::class)` autowires the container and config, and the factory resolves on its own
- [x] `src/Facades/RouteConnex.php`: `@method` docblocks for `app()` and `sdk()`
- [x] Tests (`tests/Feature/RouteConnexAppsTest.php`, update `tests/Feature/ExampleTest.php`): config defaults merged; `sdk()` uses the default App's Credentials and base URL; `app('name')` for a named App; per-App `base_url` overrides the global one and falls back to it; the same `ErClient` comes back for the same App across calls; a bound factory subclass is used; each `RouteConnexConfigurationException` case. Credentials are read from the built `ErClient` through reflection, not by sending requests.
- [x] `CHANGELOG.md`: create `# feature/sdk-layer` above `---` and `# v0.1.0-rc`, with this step's items
- [x] `phpstan.neon.dist`: `configDirectories` set to the package's `config/` folder

**Notes:**

- Larastan reported `env()` in `config/route-connex-laravel.php` as a call outside the config directory, because it only knows the app's `config_path()`. `configDirectories` (available since Larastan 3.9.0, the lowest allowed) fixes it without an ignore rule.
- The SDK requires `guzzlehttp/guzzle` `^7.9`, so installing it downgraded the locked Guzzle 8.3 to 7.16 on Laravel 13. Consumer apps on Laravel 13 that need Guzzle 8 can't install the package until the SDK allows `^8.0`; worth an SDK issue.
- Merged config is checked in `tests/Feature/ExampleTest.php` (replacing the placeholder test); App resolution in `tests/Feature/RouteConnexAppsTest.php`, which reads the SDK client's Credentials through reflection since the SDK has no getters for them.
- `prefer-lowest` resolved Laravel 12.71, Guzzle 7.15.2 and Larastan 3.9.0: 83 tests pass.

**Validate:** `make test`; `make composer ARGS="update --prefer-lowest"` then `make test-unit`, then `make composer ARGS="update"` again (Laravel 12's lowest lane runs in CI)
  **Commit:** `Add the SDK dependency, config, Apps by name and the client factory`

### Step 2: The mirrored endpoint tree and `RouteConnexResponse`

**Status:** done

- [x] `src/Http/RouteConnexResponse.php`: extends `Illuminate\Http\Client\Response`
- [x] `src/Endpoints/RouteConnexEndpoint.php`: abstract base for the leaf endpoints; holds the `RouteConnexClient`, builds its SDK endpoint, sends the request through `RouteConnexClient::send()` (the single sending method), returns a `RouteConnexResponse`
- [x] `src/Endpoints/RouteConnexSystemEndpoint.php`: abstract; adds `meta:` placement (`_meta_` JSON in the query for `GET`, array in the body otherwise) and the single protected step that turns the response into the call's result (returns the `RouteConnexResponse` for now; the run-tracking task changes it). The step receives what run-tracking will need: the App, the Service (`<METHOD> <path>`), the request data, the Meta and the response.
- [x] Group and leaf classes as in Decisions (groups are plain classes that return the leaves; only leaves extend the base), each offering only the SDK's allowed methods with the signatures from Decisions
- [x] `RouteConnexClient` and `RouteConnex`: `routeConnex()`, `run()`, `microsoftSharepoint()`, `hpeContentManager()`; facade `@method` docblocks for them
- [x] Test harness in `tests/Fixtures/`: `FakeApi` (a queue of answers through Guzzle's `MockHandler`, plus a history of the requests), `FakeErClient` (an `ErClient` whose Guzzle client uses it) and `FakeClientFactory`; the `fakeRouteConnexApi()` helper in `tests/Pest.php` binds them
- [x] Tests (`tests/Feature/RouteConnexEndpointsTest.php`): each endpoint and method sends the right HTTP method, URI (base URL, `v1`, path, Run ID), query, JSON body and `Authorization` header; `about()` sends no token request; the token is requested once per App across several calls; `meta:` placement for `GET` and `POST`, and replacing an existing `_meta_`; empty `meta:` leaves the data alone; headers are passed through; `app('name')` uses that App's Credentials and base URL; `RouteConnexResponse` exposes `json()` and `status()`
- [x] `CHANGELOG.md`: this step's items under `# feature/sdk-layer`

**Notes:**

- `RouteConnexClient::send()` is where Step 3 adds the authenticate-then-send split and the exception mapping.
- `RouteConnexSystemEndpoint::result()` receives the App, the Service (`POST microsoft-sharepoint/files`), the request (`query` and `body` after the Meta was placed), the Meta and the response.
- The IDE had reformatted four Step 1 files (braces on their own line); `make lint` restored the Pint style, so they show up in this step's diff.

**Validate:** `make test`
  **Commit:** `Mirror the SDK endpoint tree in the facade, returning RouteConnexResponse`

### Step 3: Package exceptions wrapping the SDK's and Guzzle's

**Status:** done

- [x] `src/Exceptions/RouteConnexRequestException.php` (`response(): RouteConnexResponse`), `RouteConnexConnectionException.php`, `RouteConnexAuthenticationException.php` (`response(): ?RouteConnexResponse`)
- [x] The single sending method, `RouteConnexClient::send()`: authenticate first (own `try`, in `authenticate()`) when the SDK endpoint requires auth for the method, then send; every row of the table in Decisions is mapped in `wrap()` and `authenticate()`, keeping the original as `previous`
- [x] Tests (`tests/Feature/RouteConnexExceptionsTest.php`), one per table row: 401 from `auth/token`; a token response without a token; connection failure while getting the token and while calling a Service; 4xx and 5xx from a Service, with the API's error body readable from the exception's response; 401/403 from a Service after a token was issued is a `RouteConnexRequestException`; every package exception is a `RouteConnexException`
- [x] `CHANGELOG.md`: this step's items under `# feature/sdk-layer`

**Notes:**

- A token that is already expired (`expires_in` of 0) makes the SDK's `authenticate()` return without a valid token, and its own request would then throw `NotAuthenticatedException`; `authenticate()` checks `isAuthenticated()` afterwards and throws `RouteConnexAuthenticationException` instead.
- Any error answer to the token request is an authentication exception, including 5xx (the table says so); the tests cover 400, 401 and 500.
- A failed token request is not cached: the next call asks again. A failed Service call keeps the token.
- The SDK's `InvalidEndpointException` and `InvalidHttpMethodException` are not wrapped: the facade only offers valid endpoints and methods, so they can't happen.
- `make lint` again reformatted files the IDE had put in another style (braces, `phpdoc_align`, spacing), in Step 1 and 2 files too, so they appear in this step's diff.

**Validate:** `make test`
  **Commit:** `Wrap the SDK's and Guzzle's exceptions in package exceptions`

### Step 4: Boost skill, README and project docs

**Status:** done

- [x] `resources/boost/skills/route-connex-laravel-development/SKILL.md`: config and `.env` keys, the facade and `app()`, the mirrored endpoints with their signatures and `meta:`, `RouteConnexResponse`, `sdk()` and its raw exceptions, the exception classes, binding a `RouteConnexClientFactory` subclass (with the `package-generate-skill` skill)
- [x] `README.md`: a Configuration section (`.env` keys, publishing the config) and a Usage section (calls, `app()`, Meta, responses, exceptions, `sdk()`); keep the "under development" banner
- [x] `docs/features/sdk-layer.md`: status `in progress`; align the Developer experience and Scope with what was built (System endpoints return `RouteConnexResponse` until run-tracking; `app('name')` for config Apps; full SDK signatures plus `meta:`; `meta:` replaces `_meta_`; the 401/403 mapping; `sdk()` throws raw exceptions; `RouteConnexClientFactory` ships here)
- [x] `docs/roadmap.md`: the "Laravel layer over the SDK" theme to `in progress`
- [x] `docs/features/apps.md`, `docs/features/run-tracking.md`, `docs/features/overrides.md`: short notes on what already exists and what each task changes (widen `app()` to the contract and make `RouteConnexConfigApp` implement it; change the System endpoints' result step to return a `RouteConnexRun`; `RouteConnexClientFactory` already shipped)
- [x] `CHANGELOG.md`: this step's items under `# feature/sdk-layer`
  **Validate:** `make test`; read the Boost skill and README examples against `src/`
  **Commit:** `Document the SDK layer in the Boost skill, README and project docs`

### Step 5: Review fix: Pint and `composer.json` formatting

**Status:** done

- [x] `make lint`, then check with `git diff` that only formatting changed (Review finding 1)
- [x] `composer.json`: indent the `illuminate/http`, `illuminate/support` and `route-connex/route-connex-sdk-php` lines in `require` with 8 spaces, like the rest of the file (Review finding 2)
- [x] No CHANGELOG item (formatting only)

Note: Pint changed 20 files (19 style issues, mostly docblock alignment, `@param` spacing and brace placement). Lint reports PASS on 50 files after the fix.

**Validate:** `make test` passes end to end (PHPStan, Pint, type coverage, Pest); `make composer ARGS="validate --strict"`
**Commit:** `Format the SDK layer with Pint and fix composer.json indentation`

### Step 6: Review fix: token margin, request-data errors, token error message, `result()` test

**Status:** done

- [x] `src/RouteConnexClient.php`: `protected int $tokenRefreshMargin = 30`; `authenticate()` calls the SDK's `authenticate(forceNewToken: true)` when the client has a token whose `getTokenExpiresIn()` is below the margin, and `authenticate()` as now otherwise (Review finding 3)
- [x] `src/RouteConnexClient.php`: in `send()` (and `wrap()`), rethrow Guzzle's `GuzzleHttp\Exception\InvalidArgumentException` as it is instead of wrapping it as a `RouteConnexConnectionException`; update the docblocks (Review finding 4)
- [x] `src/Endpoints/RouteConnexSystemEndpoint.php`: catch the `JsonException` from encoding `GET` Meta and throw `\InvalidArgumentException` with it as `previous`, before any request is sent (Review finding 4)
- [x] `src/Exceptions/RouteConnexAuthenticationException.php`: `rejected()` message "The RouteConnex API answered the token request with status {status}."; class docblock says the token request failed or issued no usable token (Review finding 5)
- [x] Tests in `tests/Feature/RouteConnexExceptionsTest.php`:
  - a token whose `expires_in` is below the margin is renewed before the next call (two token requests, the second call sends the new token), and a token well within its lifetime is reused (already covered: check it still is)
  - Meta that can't be encoded on `GET` throws `\InvalidArgumentException`, not a `RouteConnexException`, and sends no Service request
  - a `POST` body that can't be encoded (e.g. invalid UTF-8) throws `\InvalidArgumentException`, not a `RouteConnexConnectionException`
  - the authentication exception's message for a 401 and a 500 token answer
- [x] Test in `tests/Feature/RouteConnexEndpointsTest.php`, with a test-only subclass of a leaf System endpoint in `tests/Fixtures/` that records what `result()` receives: the App, the Service (`POST microsoft-sharepoint/files`, `GET hpe-content-manager/records`), the request (`query` and `body` after Meta was placed), the Meta and the response; and that the call returns what `result()` returns (Review finding 6)
- [x] No new CHANGELOG items (fixes to unreleased behaviour); reword an existing item only if it became wrong

Notes:

- `make lint` also reformatted 17 files outside this step (braces on their own line, `concat_space`, `phpdoc_align`, and similar): formatting only, no behaviour change. It looks like the committed code and the Pint config disagree; worth a look before the review.
- The `result()` test uses `tests/Fixtures/RecordingFilesEndpoint.php` (a `files` endpoint) for both `POST` and `GET`, so the Services checked are `POST`/`GET microsoft-sharepoint/files`, not `GET hpe-content-manager/records`. The `result()` step is shared by all System endpoints.
- `wrap()` is unchanged apart from its docblock: `send()` rethrows Guzzle's `InvalidArgumentException` before calling it.

**Validate:** `make lint`, then `make test`
**Commit:** `Renew tokens close to expiry and stop wrapping request-data errors`

### Step 7: Review fix: docs

**Status:** done

- [x] `docs/features/sdk-layer.md` Scope: the `RouteConnexConfigurationException` bullet lists only what the code throws today (no default App, an unknown App, empty Credentials); the `app_name`/`app_id` case stays in `run-tracking.md` and decision 0004 (Review finding 7)
- [x] `docs/features/sdk-layer.md` Scope and the exceptions sections of `README.md` and the Boost skill: request data that can't be encoded as JSON throws `\InvalidArgumentException`, not a package exception
- [x] `docs/features/sdk-layer.md` compatibility notes and the `README.md` Requirements: the SDK requires `guzzlehttp/guzzle` `^7.9`, so a Laravel 13 app that needs Guzzle 8 can't install the package yet (Review finding 8)
- [x] Boost skill Rules: "one client per App is kept for the life of the PHP process; its token is reused until it is about to expire" (Review finding 9)
- [x] Boost skill and `README.md`: a short "Testing" note: `Http::fake()` doesn't see these calls, because the SDK uses its own Guzzle client; mock the facade instead, e.g. `RouteConnex::shouldReceive('microsoftSharepoint->files->post')->andReturn(...)`, until `RouteConnex::fake()` (roadmap: Testing helpers) ships. Check that the example works with a quick test before documenting it, and don't keep the test unless it covers package behaviour (Review finding 10)
- [x] No new CHANGELOG items; reword the Boost skill/README item only if it became wrong

Notes:

- Finding 10: the facade-mock example in the Boost skill and README was checked with a throwaway test before documenting it. `RouteConnex::shouldReceive('microsoftSharepoint->files->post')` works, and the scratch test was deleted. The typed-object variant (`shouldReceive('microsoftSharepoint')->andReturn(mock)`) fails because the facade method's return type is enforced, so it isn't documented.
- The Boost skill was edited directly and checked against the `package-generate-skill` guidance; it wasn't regenerated.
- The Guzzle 8 note is in `sdk-layer.md` and the README Requirements. The SDK issue is still for the user to open.

**Validate:**
**Commit:** `Align the SDK layer docs with the review fixes`

## Acceptance Criteria

- [x] With `ROUTE_CONNEX_CLIENT_ID` and `ROUTE_CONNEX_CLIENT_SECRET` set, `RouteConnex::routeConnex()->testAuth()->get()` authenticates and returns a `RouteConnexResponse`.
- [x] `RouteConnex::app('name')` uses that config App's Credentials and base URL; the App's `base_url` overrides the global one.
- [x] Every SDK endpoint and HTTP method is offered by the facade with the SDK's own names and signatures; System endpoints also take `meta:` and put it where the API expects it.
- [x] Every System endpoint call goes through one protected step that the run-tracking task can change to return a `RouteConnexRun`.
- [x] Missing or unknown Apps and empty Credentials throw `RouteConnexConfigurationException` on first use, not at boot.
- [x] Token, connection and Service failures throw the package exceptions from the table in Decisions, all extending `RouteConnexException`, with the API's response available where there is one.
- [x] `RouteConnex::sdk()` returns the configured `ErClient`.
- [x] Binding a `RouteConnexClientFactory` subclass changes how SDK clients are built.
- [x] The Boost skill, README, `sdk-layer.md` and the roadmap describe what the code does; the feature and theme are `in progress`.
- [ ] `make test` passes, and CI passes on PHP 8.3–8.5, Laravel 12/13 and both stability lanes.

## Review

Reviewed on 2026-11-19 against the code at `5da76b0`, the SDK `v1.1.0` in `vendor/`, `docs/` and the Decisions above.

**Validation run:**

- `make test`: **fails** at `composer lint:check` (Pint: 19 files, 19 style issues), so type coverage and Pest don't run through it.
- Run on their own: PHPStan no errors; type coverage 100.0 %; Pest 129 passed (292 assertions).
- `make test-all`: 129 passed on SQLite, MySQL, PostgreSQL and MongoDB.
- CI has not run on this branch (pushes trigger CI only on `main` and `release/*.x`, and there's no PR yet). The `prefer-lowest` lane from Step 1 was not re-run.

**Verified, matches the plan:** config keys and defaults; `base_url` precedence (App, then global, then the SDK's); config checked on first use with the three `RouteConnexConfigurationException` cases; one client per App name; the factory resolved from the container per new App, with `newClient()`/`configure()`; the mirrored tree with the SDK's names, allowed methods and signatures; `meta:` placement and replacement; one sending method with a separate token step; every row of the exception table, keeping `previous`; `sdk()` throwing raw exceptions; class names per decision 0002; facade `@method` docblocks; Composer requirements; CHANGELOG items under `# feature/sdk-layer` within the length rules; `version` is `feature/sdk-layer`; roadmap and feature status `in progress`; the "Already in the code" notes in `apps.md`, `run-tracking.md` and `overrides.md` match the code.

**Findings** (most severe first):

1. **Code style fails (blocks CI).** 19 committed files under `src/` and `tests/` are not Pint-clean (`braces_position`, `phpdoc_align`, `not_operator_with_successor_space`, `concat_space`, `function_declaration`, `cast_spaces`). The CI Linter step would fail on every job, so the last acceptance criterion is unticked. The Step 2 and 3 notes say `make lint` fixed this, but the committed files are back in the IDE's style, so the IDE (PhpStorm, `.idea` is git-ignored) reformats them after Pint. Fix: `make lint`; set the IDE to Laravel/Pint style or turn off reformat-on-save.
2. **`composer.json` indentation.** The three `require` lines added in Step 1 use 6 spaces instead of 8, repeating what `5bfd083` fixed.
3. **Token can expire between the package's check and the SDK's.** `RouteConnexClient::authenticate()` checks the token, then the SDK's `runHttpRequest()` checks it again inside `send()`'s `try`. The SDK's check works to the second, so a token that expires in between makes the SDK request a new one there. A rejected token request then becomes a `RouteConnexRequestException` carrying the `auth/token` response, and a malformed token answer (`ErrorException`/`TypeError`) or the SDK's `NotAuthenticatedException` escapes unwrapped. A token that is about to expire can also expire while the request is in flight. Suggested fix: in `authenticate()`, request a new token (`authenticate(forceNewToken: true)`) when `getTokenExpiresIn()` is below a small margin (e.g. 30 s), with a test.
4. **Invalid request data is reported inconsistently.** With `meta:` on `GET`, `json_encode(..., JSON_THROW_ON_ERROR)` throws a `JsonException`, which isn't a `RouteConnexException`. A body Guzzle can't encode (e.g. invalid UTF-8 in a `POST` body or Meta) throws Guzzle's `InvalidArgumentException`, which implements `GuzzleException` and has no response. It is then wrapped as `RouteConnexConnectionException` with the message "Could not reach the RouteConnex API: json_encode error…". This follows the table ("another `GuzzleException` without a response"), but the table didn't plan for it. Decision needed: let both surface as `\InvalidArgumentException` (a programming error), or map both to one package exception.
5. **5xx from `auth/token`.** As planned, it's a `RouteConnexAuthenticationException`, but the message ("rejected the token request with status 503") reads like bad Credentials during an outage. Suggest a neutral message ("answered the token request with status 503"); `response()->status()` already tells the cases apart.
6. **Untested hand-off to run-tracking.** No test checks what `RouteConnexSystemEndpoint::result()` receives (the App, `POST microsoft-sharepoint/files`, the request after Meta was placed, the Meta, the response), which is the contract the run-tracking task builds on. Suggest a test with a test-only subclass of a leaf endpoint that records the arguments.
7. **`sdk-layer.md` Scope describes code that isn't there.** The `RouteConnexConfigurationException` bullet still lists "a Run saved with both `app_name` and `app_id`, or neither", which belongs to run-tracking (already in `run-tracking.md` and decision 0004). Mark it as added by run-tracking, or drop it.
8. **Guzzle 8 isn't documented.** Step 1 found that the SDK's `guzzlehttp/guzzle: ^7.9` keeps Laravel 13 apps on Guzzle 7. The compatibility notes in `sdk-layer.md` don't say that a Laravel 13 app that needs Guzzle 8 can't install the package, and neither do the README Requirements. No SDK issue exists for it yet. Suggest a line in both, and an SDK issue to allow `^8.0`.
9. **Boost skill wording.** The rule "one client (and one token) is kept per App for the life of the PHP process" is wrong about the token, which is renewed when it expires. Suggest: "one client per App for the life of the PHP process; its token is reused until it expires".
10. **Testing in consumer apps (optional).** The Boost skill and the README don't say how a consumer app tests code that calls the facade. `Http::fake()` doesn't see the SDK's requests, so an agent is likely to try it and fail. Until `RouteConnex::fake()` (roadmap: Testing helpers), one line would help: mock the facade, e.g. `RouteConnex::shouldReceive('microsoftSharepoint->files->post')`.
11. **Note for Multiple Apps (no change now).** The `RouteConnex` singleton caches clients by name for the life of the process and keeps the container and config it was first resolved with. Config changed after first use is ignored, and there's no `purge()`. That's fine for config Apps. The Multiple Apps task must keep it in mind for long-running workers, as `apps.md` already says.

**Fix steps** (agreed with the user; see Steps 5–7 and "Review fixes" in Decisions):

- Step 5: Pint and `composer.json` formatting (findings 1, 2).
- Step 6: Token margin, request-data exceptions, token error message, and a test for `result()` (findings 3–6).
- Step 7: Docs (findings 7–10).
- Not a step: the user opens an SDK issue to allow `guzzlehttp/guzzle: ^8.0` (finding 8).
