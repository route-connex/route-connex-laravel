# Guzzle 8 support

**Status:** in review **Branch:** `feature/guzzle-8` → **Target:** `release/0.x`
**Planned on:** `gandalf`
**Planning session:**

```shell
claude --ide --resume 55d2a5fd-90b3-42a4-b1fd-513e990658b1
```

## Implementation

- [x] **1. SDK 1.2, Guzzle 8 and version-agnostic error answers**
  - **Model:** `sonnet`[RouteConnexExceptionsTest.php](tests/Feature/RouteConnexExceptionsTest.php)
  - **Effort:** `medium`
  - **Session:** `new`
  - **Resume:** `claude --ide --resume e622c416-3f6b-4df1-aca5-5de4527446a9` (gandalf)
  - **Why:** Small, fully decided code change with tests, but it has to be checked on two Guzzle majors. The planning session is long and mostly about the previous task, so a fresh one with this plan is enough.
  - **Prompt:** Implement Step 1 of PLAN.md: SDK 1.2, Guzzle 8 and version-agnostic error answers. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md; the reasons are in PLAN.md → Context and Decisions. The Guzzle 8 exception hierarchy is in Guzzle's `UPGRADING.md` (8.x), and the SDK's 1.2 changes are in `../route-connex-sdk-php` (`CHANGELOG.md`, `src/Concerns/RunsHttpRequests.php`). Use the `package-testing` and `package-compatibility` skills. `composer.lock` is git-ignored, so switching Guzzle versions locally leaves no trace in git; leave the local install on Guzzle 8 when done. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Add the step's CHANGELOG items as the step says. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 2.
- [x] **2. Docs: Guzzle 8 and the connection exception**
  - **Model:** `haiku`
  - **Effort:** `low`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume e622c416-3f6b-4df1-aca5-5de4527446a9` (gandalf)
  - **Why:** Mechanical wording changes in three docs, building directly on Step 1's session.
  - **Prompt:** Implement Step 2 of PLAN.md: Docs: Guzzle 8 and the connection exception. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Step 1 bumped the SDK to `^1.2`, required `guzzlehttp/guzzle: ^7.9 || ^8.0` directly and made only 4xx/5xx answers throw `RouteConnexRequestException`. Edit the Boost skill directly, checking it against the `package-generate-skill` guidance. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message.
- [x] **3. Align the connection exception docs (review fixes)**
  - **Model:** `haiku`
  - **Effort:** `low`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume d41da745-3521-4823-af72-0908d01ca8a3` (gandalf)
  - **Why:** Two wording fixes found by the review, done in the review session that has the context.
  - **Prompt:** Implement Step 3 of PLAN.md: Align the connection exception docs (review fixes). Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md; the reasons are in PLAN.md → Review, findings 1 and 2. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message.

## Context

`route-connex/route-connex-sdk-php` 1.2.0 allows `guzzlehttp/guzzle: ^7.9 || ^8.0` (SDK issue #2, PR #3). It also uppercases HTTP methods, because Guzzle 8 sends them as given. This package still requires the SDK at `^1.1`, and its README, `docs/features/sdk-layer.md` and Boost skill say that a Laravel 13 app that needs Guzzle 8 can't install it.

Raising the SDK requirement isn't enough. Guzzle 8 moved `getResponse()` from `RequestException` to a new `ResponseException` (between `RequestException` and `BadResponseException`). `RouteConnexClient::wrap()` (`src/RouteConnexClient.php:143`) calls `$e->getResponse()` on any `RequestException`. Under Guzzle 8, a `RequestException` without a response, such as a failure reading the request body, then crashes with "Call to undefined method".

Checked during planning, with SDK 1.2.0 and Guzzle 8.6.0 installed on the local Laravel 13 (then put back):

- PHPStan reports exactly that call: `Call to an undefined method GuzzleHttp\Exception\RequestException::getResponse()` (`method.notFound`), line 143.
- Pest passes, 137 tests, because the tests only throw `ClientException`/`ServerException` (which still have `getResponse()`) and `ConnectException` (not a `RequestException`). The crashing path is untested.

The outcome: the package installs and works on Guzzle 7.9+ and Guzzle 8, and the docs say so.

## Decisions

- **Task branch:** `feature/guzzle-8`, from and into `release/0.x`. Nothing is released yet, so the close folds it into `# v0.1.0-rc`.
- **Which failures carry an API error answer:** only `GuzzleHttp\Exception\BadResponseException` (`ClientException`/`ServerException`, 4xx/5xx). The class exists in Guzzle 7 and 8, and `getResponse()` on it returns a response in both, so one `instanceof` check passes PHPStan on both majors. Every other `GuzzleException` (apart from Guzzle's `InvalidArgumentException`, which `send()` rethrows as it is) becomes `RouteConnexConnectionException`, including:
  - a plain `RequestException` (no response),
  - `TooManyRedirectsException` (it carries a 3xx response, and becomes a `RouteConnexRequestException` today),
  - Guzzle 8's `ResponseTransferException`/`ResponseTimeoutException`: the transfer failed after the headers, which can be a 200's.
  
  This matches what the README and the Boost skill already promise ("the API answered a call with 4xx or 5xx"). The same applies to the token request: only a 4xx/5xx token answer is a `RouteConnexAuthenticationException::rejected()`.
- **Guzzle requirement:** `composer.json` requires `"guzzlehttp/guzzle": "^7.9 || ^8.0"` directly, as `src/` uses Guzzle's classes, and `"route-connex/route-connex-sdk-php": "^1.2"`. The floor matches the SDK's.
- **CI:** no workflow change. Laravel 13 `prefer-stable` resolves to Guzzle 8, while Laravel 12 (`guzzlehttp/guzzle: ^7.8.2`) and `prefer-lowest` stay on Guzzle 7, and every job runs PHPStan and Pest. The review checks this once in the PR's job logs (the `composer update` output names the Guzzle version).
- **No version checks in code:** no `class_exists(ResponseException::class)` or `method_exists()`. The `BadResponseException` check works the same on both majors.

## Steps

### Step 1: SDK 1.2, Guzzle 8 and version-agnostic error answers

**Status:** done

- [x] `composer.json` `require`: `"guzzlehttp/guzzle": "^7.9 || ^8.0"` and `"route-connex/route-connex-sdk-php": "^1.2"` (8-space indent, like the rest of the file)
- [x] `src/RouteConnexClient.php` `wrap()`: take the response only from a `GuzzleHttp\Exception\BadResponseException` (`$e instanceof BadResponseException ? $e->getResponse() : null`); swap the `RequestException` import for `BadResponseException`; update the docblocks of `wrap()` and, if needed, `send()`/`authenticate()` to say that only a 4xx/5xx answer carries a response and every other Guzzle failure is a connection failure
- [x] Tests in `tests/Feature/RouteConnexExceptionsTest.php`, written to run unchanged on Guzzle 7 and 8 (`new RequestException($message, $request)` and `new TooManyRedirectsException($message, $request, $response)` have compatible constructors in both):
  - calling a Service: a plain `RequestException` without a response throws `RouteConnexConnectionException`, keeping it as `previous`
  - calling a Service: a `TooManyRedirectsException` carrying a 3xx response throws `RouteConnexConnectionException`, not `RouteConnexRequestException`
  - getting the token: a plain `RequestException` throws `RouteConnexConnectionException`, not `RouteConnexAuthenticationException`
  - the existing 4xx/5xx tests still pass unchanged
- [x] `CHANGELOG.md`: create `# feature/guzzle-8` above `---` and `# v0.1.0-rc`, with the item "Guzzle 8 support, with `route-connex/route-connex-sdk-php` ^1.2." The exceptions aren't released yet, so reword the existing `v0.1.0-rc` items instead of adding new ones: `RouteConnexRequestException` is for "a 4xx or 5xx answer from the API", and `RouteConnexConnectionException` is for when "the API can't be reached or a request fails without an error answer" (within the item length rules)

**Notes:** Validated on Guzzle 8.6.0 and 7.15.2 (the `--prefer-lowest` update resolved to 7.15.2, the lowest Guzzle 7 the other constraints allow): 140 tests and PHPStan pass on both; local install left on 8.6.0. The first `validate --strict` fails until `composer update` regenerates the git-ignored lock.

**Validate:**

- `make composer ARGS="validate --strict"`
- Guzzle 8: `make composer ARGS="update -W"`, check `make composer ARGS="show guzzlehttp/guzzle"` reports 8.x, then `make test`
- Guzzle 7: `make composer ARGS="update 'guzzlehttp/*' -W --prefer-lowest"`, check `show guzzlehttp/guzzle` reports 7.x, then `make test`
- Back to Guzzle 8: `make composer ARGS="update -W"`

**Commit:** `Support Guzzle 8 and treat only 4xx/5xx as API error answers`

### Step 2: Docs: Guzzle 8 and the connection exception

**Status:** done

- [x] `README.md` Requirements: replace the Guzzle 7 line with Guzzle 7.9+ or 8 (`guzzlehttp/guzzle` `^7.9 || ^8.0`)
- [x] `docs/features/sdk-layer.md` compatibility notes: the package and the SDK (`^1.2`) allow `^7.9 || ^8.0`; Laravel 12 stays on Guzzle 7 and Laravel 13 can use either; CI covers both majors through the Laravel and stability lanes. Drop "Until the SDK allows `^8.0`…"
- [x] `resources/boost/skills/route-connex-laravel-development/SKILL.md`: replace the "the SDK requires Guzzle 7" rule with Guzzle 7.9+ or 8
- [x] `RouteConnexConnectionException` rows in the README and Boost skill exception tables, and the bullet in `docs/features/sdk-layer.md` Scope: "the API couldn't be reached, or the request failed without a 4xx/5xx answer". Check that the `RouteConnexRequestException` rows and bullet still say 4xx/5xx
- [x] No new CHANGELOG items; this is the same change as Step 1's

**Notes:** Kept the README Requirements wording as "Guzzle 7.9+ or 8" and the Boost skill's rule as "the package works with Guzzle 7.9+ or 8". The `grep` for stale constraints shows only that README line, which is the new wording. Also reworded the `RouteConnexConnectionException` rows in the README and the Boost skill table. The `RouteConnexRequestException` rows already say 4xx/5xx.

**Validate:** `make test`; `grep -rn "Guzzle 7\|\^7\.9\b\|\^1\.1" README.md docs resources/boost` shows no stale constraint
**Commit:** `Document Guzzle 8 support and the connection exception`

### Step 3: Align the connection exception docs (review fixes)

**Status:** done

- [x] `src/Exceptions/RouteConnexConnectionException.php` docblock: the API couldn't be reached, or a request failed without a 4xx or 5xx answer (Review finding 1)
- [x] `docs/features/sdk-layer.md` Scope: `RouteConnexRequestException` is a 4xx or 5xx answer to a Service call (Review finding 2)
- [x] No new CHANGELOG items; the rc items already say this

**Notes:** Also added `guzzlehttp/guzzle` to the "New Composer requirements" line in `sdk-layer.md` Scope, which Step 1 made a direct requirement and the line still left out. The exception's message ("Could not reach the RouteConnex API: …") is unchanged: it's runtime behavior and the previous exception's message says what happened.

**Validate:** `make test`
**Commit:** `Align the connection exception docs with Guzzle 8 handling`

## Acceptance Criteria

- [ ] `composer.json` requires `route-connex/route-connex-sdk-php` `^1.2` and `guzzlehttp/guzzle` `^7.9 || ^8.0`.
- [ ] `make test` passes locally on Guzzle 8 and on Guzzle 7 (`--prefer-lowest`), including PHPStan.
- [ ] A Guzzle failure without a 4xx/5xx answer throws `RouteConnexConnectionException` on both majors, for Service calls and the token request, and never a PHP `Error`.
- [ ] 4xx/5xx answers still throw `RouteConnexRequestException` (Service) or `RouteConnexAuthenticationException` (token), with the response.
- [ ] The README, `sdk-layer.md` and the Boost skill say the package works with Guzzle 7.9+ and 8, and describe the connection exception as the code does.
- [ ] CI passes on PHP 8.3–8.5, Laravel 12/13 and both stability lanes, with the Laravel 13 `prefer-stable` jobs on Guzzle 8.

## Review

Findings from Phase 3 (filled in by the reviewing agent).

**Validation (2026-11-25):** `make test` passes on Guzzle 8.6.0 / SDK 1.2.0 (PHPStan, Pint, 100% type coverage, 140 tests) and on Guzzle 7.15.2 (`update 'guzzlehttp/*' -W --prefer-lowest`); local install put back on 8.6.0. `make test-all`: 140 passed on SQLite, MySQL, PostgreSQL and MongoDB. CI (last criterion) can only be checked on the PR.

**Checked and fine:** `composer.json` constraints; `wrap()` takes a response only from `BadResponseException`, with no version checks; the three new tests cover a plain `RequestException` (Service and token) and `TooManyRedirectsException`, and run unchanged on both majors; README, Boost skill and `sdk-layer.md` Guzzle lines; CHANGELOG has `# feature/guzzle-8` with one item and the reworded rc items; `version` is `feature/guzzle-8`; no stale Guzzle 7-only or `^1.1` wording left.

**Findings (docs only, no behavior change):**

1. `src/Exceptions/RouteConnexConnectionException.php` docblock still says the request "failed before the API answered". Since Step 1, a `TooManyRedirectsException` (the API answered 3xx) and Guzzle 8's `ResponseTransferException` (headers received) also become this exception. It should match the README row: the API couldn't be reached, or a request failed without a 4xx or 5xx answer.
2. `docs/features/sdk-layer.md:90` (Scope) describes `RouteConnexRequestException` as "the API answered a Service call with an error". Step 2 asked to check it says 4xx/5xx, as the README, Boost skill and class docblock do; it doesn't.
Findings 1 and 2 fixed in Step 3, with the user's agreement.

3. (No action: `PLAN.md` is blanked on close.) Step 1's commit left a stray `[RouteConnexExceptionsTest.php](…)` link on the Step 1 **Model** line.
