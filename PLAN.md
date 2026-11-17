# Project documentation: vision, concepts, roadmap, ideas and features

**Status:** in progress
**Branch:** `docs/project` → **Target:** `release/0.x`
**Planned on:** `gandalf`
**Planning session:**

```shell
claude --ide --resume 88963194-682b-472c-9d64-33580c4b6038
```

## Implementation

- [x] **1. Scaffold `docs/`**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume 88963194-682b-472c-9d64-33580c4b6038` (gandalf)
  - **Why:** Creating files from a structure already agreed in the planning session, plus a one-line `.gitattributes` change. The planning session has the structure loaded.
  - **Prompt:** Implement Step 1 of PLAN.md: Scaffold `docs/`. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. The structure, file purposes, status labels and feature template fields are in PLAN.md → Decisions. Create headings and short placeholder comments only: no vision or feature content, which later steps write with the user. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 2.
- [x] **2. Write `docs/vision.md` with the user**
  - **Model:** `opus`
  - **Effort:** `high`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume 6ecefc09-0ad9-49f7-bff9-76d54376d8e3` (gandalf)
  - **Why:** The vision comes from the user's head, not the code: it needs a careful interview and good judgement on goals and non-goals. Same session keeps the planning context about the ecosystem.
  - **Prompt:** Implement Step 2 of PLAN.md: Write `docs/vision.md` with the user. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Step 1 created `docs/vision.md` with its section headings. Interview the user section by section, draft each section from their answers, and revise until they approve it. Don't invent goals, features or facts: anything the user hasn't decided goes under Open questions. You may read the sibling repos `../route-connex-sdk-php` and RouteConnex's private repos (read-only) for context, but confirm with the user before writing anything taken from them. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 3.
- [x] **3. Write `docs/concepts.md` with the user**
  - **Model:** `opus`
  - **Effort:** `high`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume 6ecefc09-0ad9-49f7-bff9-76d54376d8e3` (gandalf)
  - **Why:** Defining the domain vocabulary needs judgement and builds directly on the vision just written in the same session.
  - **Prompt:** Implement Step 3 of PLAN.md: Write `docs/concepts.md` with the user. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Step 2 wrote `docs/vision.md`; reuse its terms. Interview the user for each term and how the terms relate, draft, and revise until they approve. You may read the sibling repos `../route-connex-sdk-php` and RouteConnex's private repos (read-only) to keep the terms consistent with them, but confirm with the user before using anything from them. Don't invent definitions: unclear terms go under Open questions. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 4.
- [x] **4. Write `docs/ideas.md` and `docs/roadmap.md` with the user**
  - **Model:** `sonnet`
  - **Effort:** `high`
  - **Session:** `new`
  - **Resume:** `claude --ide --resume 38eff6af-7cf1-4628-a206-708e786e894b` (gandalf)
  - **Why:** Mostly collecting and ordering the user's ideas. The vision and concepts are on disk by now, and the earlier session will be long.
  - **Prompt:** Implement Step 4 of PLAN.md: Write `docs/ideas.md` and `docs/roadmap.md` with the user. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Read `docs/README.md`, `docs/vision.md` and `docs/concepts.md` first (Steps 1–3); use their terms and the status labels in `docs/README.md`. Collect the user's ideas into `docs/ideas.md`, then ask which ones become roadmap themes and in which version group (Now / Next / Later). Don't add ideas of your own unless the user asks for suggestions, and mark any you suggest as such until the user accepts them. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 5.
- [x] **5. Write the first feature docs with the user**
  - **Model:** `opus`
  - **Effort:** `high`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume 38eff6af-7cf1-4628-a206-708e786e894b` (gandalf)
  - **Why:** Turning roadmap themes into concrete developer-facing APIs needs design judgement, Laravel package idioms and multi-database awareness. Builds directly on the roadmap from Step 4.
  - **Prompt:** Implement Step 5 of PLAN.md: Write the first feature docs with the user. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Step 4 wrote `docs/roadmap.md`; write one `docs/features/<name>.md` from `docs/features/_template.md` for each roadmap theme in the Now group (ask the user if Next themes should get one too). Interview the user per feature; you may propose the developer-experience examples (config, facade, Artisan) in Laravel-native style, marked as proposals until the user approves. Fill the database & compatibility notes against every supported database and Laravel 12/13 (use the `package-compatibility` skill). Link each feature from `docs/roadmap.md` and the idea it came from in `docs/ideas.md`. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 6.
- [x] **6. Record decisions and cross-link the docs**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume 38eff6af-7cf1-4628-a206-708e786e894b` (gandalf)
  - **Why:** Writing ADRs for decisions already taken in Steps 2–5 and checking links: the Step 5 session knows which decisions were made.
  - **Prompt:** Implement Step 6 of PLAN.md: Record decisions and cross-link the docs. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Write `docs/decisions/0001-project-documentation-structure.md` from `docs/decisions/_template.md` using PLAN.md → Decisions, then list the decisions taken while writing Steps 2–5 and ask the user which ones get an ADR. Check that every relative link in `docs/` and the README's docs link resolves, and that `docs/README.md` lists every file. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start the review.
- [x] **7. Callbacks and custom tenancy**
  - **Model:** `opus`
  - **Effort:** `high`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume 301035d8-61c2-4388-b85e-f83b2e1c0f59` (gandalf)
  - **Why:** It designs a new public extension point (`RouteConnexTenancy`) that affects several docs and ADRs, and it needs judgement about tenancy, queues and routing. The review session already has the findings and the user's answers.
  - **Prompt:** Implement Step 7 of PLAN.md: Callbacks and custom tenancy. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. The findings are R1–R5 and R9 in PLAN.md → Review, and the user's answers are in Decisions → "Review answers". Read `docs/features/callbacks.md`, `docs/features/overrides.md`, `docs/features/run-tracking.md`, `docs/vision.md`, `docs/concepts.md`, `docs/ideas.md`, `docs/roadmap.md` and ADRs 0005 and 0007 first. Propose the `RouteConnexTenancy` methods to the user, mark them as a proposal and get approval before writing them into the docs. Ask the user about the open point in R9 (a finished Run receiving a different finished status). Don't name consumer apps or private repos in the docs. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 8.
- [x] **8. Consistency fixes from the review**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume 301035d8-61c2-4388-b85e-f83b2e1c0f59` (gandalf)
  - **Why:** Small, already-decided wording fixes across the docs, AGENTS.md and PLAN.md. The Step 7 session knows what changed and can keep the CHANGELOG and the plan in line with it.
  - **Prompt:** Implement Step 8 of PLAN.md: Consistency fixes from the review. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. The findings are R6–R8 and R10–R13 in PLAN.md → Review, and the user's answers are in Decisions → "Review answers". Step 7 has added `RouteConnexTenancy` and changed the callback docs: keep the wording consistent with that. Don't name consumer apps or private repos anywhere. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start another review.

## Context

The package is still a bare skeleton (service provider, publish tags, Docker toolchain) with no RouteConnex behavior. Before building features, the user wants to write down why the package exists, the vocabulary it uses, where it's heading and which features are planned, so that later `PLAN.md` tasks can start from written requirements instead of from scratch.

The package is one part of the RouteConnex ecosystem, next to RouteConnex's private repos and `route-connex-sdk-php`, so the docs also say where it fits.

The outcome is a `docs/` folder for maintainers and contributors (not shipped in the Composer archive), with a clear path from a raw idea to a shipped feature.

## Decisions

- **Location:** `docs/` at the repo root.
- **Scope:** the full set, decisions folder (ADRs) included:

  ```
  docs/
  ├── README.md              # index, status labels, idea → feature lifecycle
  ├── vision.md
  ├── concepts.md
  ├── roadmap.md
  ├── ideas.md
  ├── features/
  │   └── _template.md
  └── decisions/
      └── _template.md
  ```

- **`docs/README.md`:** what each file is for, the status labels and the lifecycle below.
- **`vision.md` sections:** Problem, Who it's for, Goals, Non-goals, Guiding principles, Where it fits (the ecosystem: web, agent, PHP SDK), Open questions.
- **`concepts.md`:** a glossary (one heading per term: definition, related terms) and a short section on how the terms relate, plus Open questions.
- **`roadmap.md`:** themes grouped as Now / Next / Later, each mapped to a target version range (0.x → 1.0), with no dates. Each theme links to its `features/*.md` once one exists.
- **`ideas.md`:** a parking lot. One entry per idea: a dated heading and one or two lines. An idea that matures moves into `features/` and its entry links there.
- **Feature template fields:** Status, Target version, Summary, Motivation, Developer experience (example config, facade calls, Artisan commands), Scope, Non-goals, Database & compatibility notes (MySQL, PostgreSQL, MongoDB, SQLite; Laravel 12/13), Open questions, Related.
- **Status labels** (features and roadmap themes): `idea` → `proposed` → `planned` → `in progress` → `shipped (vX.Y.Z)`, plus `dropped`.
- **Lifecycle:** `ideas.md` → `features/<name>.md` (`proposed`) → a `PLAN.md` task (`in progress`) → `shipped (vX.Y.Z)`. User-facing usage goes in the README and release notes in `CHANGELOG.md`; the feature doc stays as the design record.
- **ADR template:** Status (proposed, accepted, superseded by NNNN), Date, Context, Decision, Consequences. Files are numbered `NNNN-<kebab-title>.md`. The first ADR records this documentation structure.
- **Dist archive:** `docs/` is export-ignored in `.gitattributes`; these docs are for maintainers and contributors, not for apps that install the package.
- **CI:** no change. `**.md` in `paths-ignore` already skips docs-only changes; Step 1's `.gitattributes` change will trigger CI, which is fine.
- **Agents:** AGENTS.md → Phase 1 step 2 gets a line telling planning agents to read `docs/vision.md`, `docs/concepts.md` and the relevant `docs/features/*.md`.
- **README:** a short "Project docs" line linking to `docs/README.md`, since `docs/` isn't shipped and GitHub is where contributors find it.
- **Writing the content:** Steps 2–5 are written with the user, interview-style. Agents don't invent vision, terms, ideas or features; anything undecided goes under Open questions.
- **Steps:** Step 1 scaffolds every file, then one step per document (or pair of documents), then the ADRs and the link check.
- **Branch:** `docs/project`, from and into `release/0.x`.
- **Review answers** (Phase 3, findings in Review):
  - R1: add a replaceable tenancy class now, `RouteConnexTenancy`, as the fifth public extension point, with its own ADR. The `stancl/tenancy` preset becomes one implementation of it, and an app with its own tenancy binds its own subclass.
  - R8: `RouteConnexConfigurationException` also covers a Run with both `app_name` and `app_id`, or neither. Widen its definition; no new exception.
  - R9: a `dry-run` Run fires `RouteConnexRunSucceeded`, as well as `RouteConnexRunFinished`.
  - R9 (Step 7): a finished Run's status is final, as in RouteConnex. A Callback with a different finished status is ignored, logged as a warning, and answered with `200`.
  - R1 (Step 7): `RouteConnexTenancy` has `enabled()`, `current()`, `findTenant()`, `run()` and `forEachTenant()`. With tenancy enabled, commands on one App or Run require `--tenant`; `route-connex:purge` and `route-connex:prune` run for every Tenant unless `--tenant` is given.
  - R4 (Step 7): `callbackRoutes()` takes an optional route name.
  - R13: reword this file so it no longer names RouteConnex's private repos or client plugin.

## Steps

### Step 1: Scaffold `docs/`
**Status:** done
- [x] `docs/README.md`: index of every file with a one-line purpose, the status labels and the lifecycle (see Decisions)
- [x] `docs/vision.md`, `docs/concepts.md`, `docs/roadmap.md`, `docs/ideas.md`: title, section headings from Decisions and a short placeholder comment per section
- [x] `docs/features/_template.md` with the feature template fields
- [x] `docs/decisions/_template.md` with the ADR fields
- [x] `.gitattributes`: add `/docs export-ignore`
- [x] `AGENTS.md` → Phase 1 step 2: tell planning agents to read the project docs
- [x] `README.md`: link to `docs/README.md`
- [x] `CHANGELOG.md`: add `# docs/project` with this step's items, above a `---` and `# v0.1.0-rc`

**Validate:** `git check-attr export-ignore docs` prints `export-ignore: set`; `make test`
**Commit:** `Scaffold the project documentation in docs/`

**Note:** the validate command checks `docs`, not `docs/README.md`: `git check-attr` doesn't apply a directory pattern like `/docs` to the files inside it, though `git archive` does exclude them.

### Step 2: Write `docs/vision.md` with the user
**Status:** done
- [x] Problem, Who it's for, Goals, Non-goals, Guiding principles, Where it fits, all approved by the user
- [x] Undecided points listed under Open questions
- [x] `CHANGELOG.md`: item under `# docs/project`

**Validate:** the user approves the document; `git status` shows only `docs/vision.md`, `CHANGELOG.md` and `PLAN.md` changed
**Commit:** `Write the project vision`

**Notes:**
- Run in a new session (`6ecefc09-…`), not the planning session, so the Resume command differs from Step 1's.
- Besides the sibling repos, the user had me read two consumer apps (read-only) to shape the goals: a multi-tenant app with its own tenancy and ExternalIntegrations framework, and one on `stancl/tenancy` v3 with a database per tenant. At the user's request, the docs don't name them, nor RouteConnex's private repos (web, agent); they only mention the RouteConnex API and the public SDK.
- Naming decided with the user: everything the package adds is prefixed `RouteConnex` (`route_connex_` for tables and keys); the Run model is `RouteConnexRun`. Step 3 should use these names.

### Step 3: Write `docs/concepts.md` with the user
**Status:** done
- [x] Glossary entries and how the terms relate, approved by the user
- [x] `docs/vision.md` updated if it uses a term differently (tell the user)
- [x] `CHANGELOG.md`: item under `# docs/project`

**Validate:** the user approves the document; `git status` shows only files in `docs/`, `CHANGELOG.md` and `PLAN.md` changed
**Commit:** `Write the RouteConnex concepts and glossary`

**Notes:**
- `docs/vision.md`: two lines changed to allow several credential pairs (Apps) per tenant or app, as decided here.
- Terms checked against a RouteConnex private repo and a RouteConnex client plugin's callback handling (read-only), with each fact confirmed by the user before it was written.
- Decided: "Service" for any API call, "Run type" for the kind of Run a System Service creates; each credential pair is an App, named in config like database connections; Callbacks are checked with a per-App Callback token (like the client plugin's) plus a Run ID match, with a TODO for RouteConnex-side authentication.
- Added at the user's request: a 1.0 goal in `docs/vision.md` for an Artisan command that takes an App and prints the JSON for its callback configuration in RouteConnex (URL, method, extra fields with the Callback token); the Callback token entry in `docs/concepts.md` mentions it.
- For Step 4 (`ideas.md`): RouteConnex could tie an App to a customer instead of a single System (needs care: RouteConnex is in production); a way for API clients to request dry runs.

### Step 4: Write `docs/ideas.md` and `docs/roadmap.md` with the user
**Status:** done
- [x] `docs/ideas.md`: the user's ideas, one dated entry each
- [x] `docs/roadmap.md`: themes in Now / Next / Later with target version ranges and status labels
- [x] `CHANGELOG.md`: item under `# docs/project`

**Validate:** the user approves both documents; `git status` shows only files in `docs/`, `CHANGELOG.md` and `PLAN.md` changed
**Commit:** `Write the ideas list and the roadmap`

**Notes:**
- Run in a new session (`38eff6af-…`), so the Resume command differs from Steps 2–3.
- Ideas recorded (all dated 2026-11-17): dry-run mode, testing helpers, link Runs to models, more Artisan commands, polling fallback, Apps tied to a customer, other tenancy modes. Testing helpers, link Runs to models and the five Artisan commands were suggested by the agent from the docs and accepted by the user.
- Dropped at the user's request: queued Runs (RouteConnex already queues Runs; an app can queue the call in its own job).
- The roadmap holds the `vision.md` goals as themes as well as the ideas. Version ranges, as agreed: Now = `0.1`–`0.x`, Next = `0.x`–1.0, Later = not scheduled, up to 1.0 or after.
- Statuses: Now and Next themes are `planned`; the Later themes are still only ideas, so they are `idea`. Change them to `planned` if the user wants them agreed.
- `docs/vision.md`: the callback configuration Artisan goal now says the command takes the Tenant when a Tenancy preset is on (requested by the user). Its Open questions still list queued Runs, polling and more Artisan commands as undecided; awaiting the user's answer on whether to update them.

### Step 5: Write the first feature docs with the user
**Status:** done
- [x] One `docs/features/<name>.md` per Now theme (and Next themes if the user asks), from the template: `sdk-layer.md`, `apps.md`, `run-tracking.md`, `callbacks.md`, `overrides.md` (Now only, as the user chose)
- [x] Database & compatibility notes cover MySQL, PostgreSQL, MongoDB, SQLite and Laravel 12/13
- [x] Links from `docs/roadmap.md` and `docs/ideas.md` to each feature
- [x] `CHANGELOG.md`: item under `# docs/project`

**Validate:** the user approves each feature doc; `git status` shows only files in `docs/`, `CHANGELOG.md` and `PLAN.md` changed
**Commit:** `Write the first feature docs`

**Notes:**
- Same session as Step 4 (`38eff6af-…`).
- No Now theme came from an `ideas.md` entry (they all come from the vision goals), so `ideas.md` links to a feature only where an idea refers to one (`route-connex:purge` → `run-tracking.md`). The features link back to related ideas under Related.
- Read the public SDK (v1.1.0, read-only) for the developer-experience proposals; the user confirmed relying on it. It already has a get-Run call, so the "depends on the SDK" wording was removed from the polling entries.
- Step 4 leftovers answered here: the Later roadmap themes are now `planned`; `docs/vision.md` lost its "Other goals for 1.0" open question and gained a "No queued Runs before 1.0" non-goal.
- Added at the user's request: a "Ready for AI agents" goal in `docs/vision.md` (a bundled Laravel Boost skill that every feature keeps up to date), a line on it in `docs/roadmap.md`, and a Boost skill item in each feature's Scope.
- Decisions for Step 6 (candidate ADRs): the facade mirrors the SDK with explicit mapping; one base `RouteConnexException`; Apps from config by name or as models implementing `RouteConnexAppContract` (no resolver), with a shipped `RouteConnexApp`; Runs store `app_name` or `app_id` (exclusive, no morph type), resolved through `models.app`; Run connection rule (own connection, else the App model's, else default); MongoDB through the consumer app's subclass with `DocumentModel`, laravel-mongodb staying a suggestion; payload purging of old successful Runs; Callback route `POST {prefix}/callbacks/{app}` with 404/403/404/200 responses; replaceable classes through container bindings, not config.
- SDK change needed: accept an access token from outside so the package can cache it per App (listed in `sdk-layer.md`).
- Consumer apps aren't named in the docs (see the Step 2 notes).

### Step 6: Record decisions and cross-link the docs

**Status:** done

- [x] `docs/decisions/0001-project-documentation-structure.md`
- [x] ADRs for the decisions from Steps 2–5 that the user picks: 0002–0009 (the first eight of nine candidates; queued Runs got no ADR)
- [x] Every relative link in `docs/` and the README's docs link resolves; `docs/README.md` lists every file
- [x] `CHANGELOG.md`: item under `# docs/project`

**Validate:** `grep -rnoE '\]\([^)#:]+\.md' docs README.md` lists only paths that exist; `git status` shows only files in `docs/`, `CHANGELOG.md` and `PLAN.md` changed
**Commit:** `Record the documentation decisions and cross-link the docs`

**Notes:**

- Same session as Steps 4–5 (`38eff6af-…`).
- ADRs: 0002 `RouteConnex` prefix, 0003 Apps from config or models, 0004 Runs store `app_name` or `app_id`, 0005 models in config and classes in the container, 0006 MongoDB through the app's subclass (`mongodb/laravel-mongodb` is a `suggest` and a `require-dev`), 0007 Callbacks, 0008 facade mirrors the SDK and one exception base, 0009 purging payloads.
- Each feature doc's Related section now links to its ADRs (not in the original plan, part of cross-linking).
- `docs/README.md` lists the `decisions/` and `features/` folders, not each file in them, so it needed no change.

### Step 7: Callbacks and custom tenancy

**Status:** done

- [x] R1: `RouteConnexTenancy` (`RouteConnex\Laravel\Tenancy`), a replaceable class bound in the container, documented in `docs/features/overrides.md` as the fifth extension point, with an example subclass for an app's own tenancy. Methods, approved by the user: `enabled()` (`false` by default), `current()` (the Tenant in the current context, or `null`), `findTenant(string $id)` (the `--tenant` option; an unknown ID throws `RouteConnexConfigurationException`), `run($tenant, Closure $callback)` (runs code in that Tenant's context and restores the previous one) and `forEachTenant(Closure $callback)` (once per Tenant, or once with no Tenant when tenancy is off). The default class does nothing, so single-tenant apps don't change. When `enabled()` is true, a command that acts on one App or Run requires `--tenant` and doesn't guess, and `route-connex:purge` and `route-connex:prune` run for every Tenant unless `--tenant` is given.
- [x] R1: "when a Tenancy preset is on" and "plug in through the overridable classes" now say "when tenancy is enabled (a preset or the app's own `RouteConnexTenancy`)" in `docs/vision.md` (callback setup and `stancl/tenancy` goals), `docs/concepts.md` (Tenancy preset: a shipped `RouteConnexTenancy` subclass; no new glossary entry), `docs/ideas.md` (More Artisan commands, Other tenancy modes) and `docs/roadmap.md` (Overridable models and classes, the `stancl/tenancy` preset, Artisan commands). `docs/features/run-tracking.md` schedules the purge through `forEachTenant()`.
- [x] R1: `docs/decisions/0010-replaceable-tenancy-class.md`, plus ADR 0005's list of extension points updated to five
- [x] R2: `docs/features/callbacks.md` names the handler's `protected` steps (`findApp()`, `checkToken()`, `runInAppContext()`, `findRun()`, `updateRun()`, `fireEvents()`) and says the Run lookup uses the same connection rule as storing a Run. The example is a subclass that overrides `runInAppContext()` to switch to the App's Tenant, for Apps in a central database with Runs on a `tenant` connection.
- [x] R3: `docs/features/callbacks.md` → Tenancy: the queue worker must restore the same Tenant's context, not only a connection with the same name, before a queued listener uses `$event->run` or `$run->app`
- [x] R4: `docs/features/callbacks.md`: `RouteConnex::callbackRoutes(?string $name = null)`, each registration gets its own route name, and `register_routes` is turned off when calling `callbackRoutes()`, because duplicate route names break `php artisan route:cache`
- [x] R5 and R9: ADR 0007 and `docs/features/callbacks.md` record any tenancy setup, extra route parameters ignored, `callbackRoutes(name:)`, the named steps, `RouteConnexCallbackUrlGenerator`, `dry-run` firing `RouteConnexRunSucceeded`, and that a finished Run's status is final (a different finished status is ignored, logged as a warning, and still answered with `200`)
- [x] `CHANGELOG.md`: items under `# docs/project`

**Validate:** the user approves the changes; the link check from Step 6 lists only paths that exist; `git status` shows only files in `docs/`, `CHANGELOG.md` and `PLAN.md` changed **Commit:** `Add a replaceable tenancy class and complete the callback docs for custom tenancy`

**Notes:**

- Same session as the review (`301035d8-…`).
- Differs from the plan's starting proposal: `forEachTenant(Closure)` replaces `tenants()`, so the caller doesn't loop and `run()` always wraps each Tenant; `current()` was added for the URL generator. Approved by the user.
- R2 needed one hook the review didn't name: `runInAppContext()` wraps finding the Run, updating it and firing the events. Switching the Tenant in `findRun()` alone would leave the update and the events outside the Tenant's context.
- R9: checked read-only in RouteConnex that a finished Run can't change status there (it throws when a response arrives for a Run that already succeeded or errored); the user confirmed "finished is final" for the package.
- R4: the user chose `callbackRoutes(name:)` over a docs-only note.
- `docs/features/callbacks.md`: for Apps in a central database, the `RouteConnexRun` subclass sets its own connection, or the connection rule would store and look for Runs on the App's central connection.
- `docs/features/overrides.md` → Database & compatibility notes: `run()` must restore the previous context even when the callback throws (long-running workers).
- ADR 0003 is unchanged: facade calls still take the App passed in, and `RouteConnexTenancy` is only used where nothing else sets the Tenant (ADR 0010 says so).

### Step 8: Consistency fixes from the review

**Status:** done

- [x] R6: `CHANGELOG.md`: an item for the Step 6 callback changes (Callbacks work with any tenancy setup, `RouteConnexCallbackUrlGenerator` as an extension point, the vision's callback URL line). The Step 6 notes in this file record that these changes weren't planned.
- [x] R7: `docs/features/sdk-layer.md`: `$run->status` shows `RouteConnexRunStatus::Created`
- [x] R8: `docs/features/sdk-layer.md`: `RouteConnexConfigurationException` also covers a Run with both `app_name` and `app_id`, or neither
- [x] R10: `docs/features/apps.md` says how a config App gets its Callback token until `route-connex:callback-token` exists (a random value set in `ROUTE_CONNEX_CALLBACK_TOKEN`), and `docs/concepts.md` → Callback token says the package generates it for model Apps
- [x] R11: `docs/README.md` lifecycle: a theme can be `planned` before it has a feature doc, and the doc is written before the theme's `PLAN.md` task starts
- [x] R12: `AGENTS.md` → Phase 1 step 2 also lists `docs/roadmap.md` and the relevant `docs/decisions/*.md`
- [x] R13: this file: Context and the Step 2 and 3 prompts mention "RouteConnex's private repos" and the public SDK only; the Step 3 notes say "a RouteConnex client plugin" instead of naming it
- [x] `CHANGELOG.md`: items under `# docs/project`

**Validate:** the link check from Step 6 lists only paths that exist; `grep -rniE 'route-connex-(we[b]|agen[t])|wordpres[s]' docs README.md CHANGELOG.md PLAN.md AGENTS.md` prints nothing (the brackets keep the pattern from matching this line); `make test`
**Commit:** `Fix documentation inconsistencies found in the review`

**Notes:**

- Same session as Step 7 (`301035d8-…`), on `sonnet`/`medium` as planned.
- R6: besides the CHANGELOG item for the Step 6 callback changes, Step 7's CHANGELOG item already covered the tenancy additions.
- R13: the private repos are now "RouteConnex's private repos" and the plugin "a RouteConnex client plugin"; the old names stay in git history.

## Acceptance Criteria

- [x] `docs/` holds every file listed in Decisions, and `docs/README.md` indexes them with the status labels and lifecycle
- [x] `vision.md`, `concepts.md`, `ideas.md`, `roadmap.md` and the feature docs hold content the user approved, with no invented content
- [x] Every feature doc follows the template, including the database & compatibility notes
- [x] `docs/` is export-ignored (`git archive HEAD | tar -t | grep '^docs/'` prints nothing)
- [x] AGENTS.md points planning agents to the project docs, and the README links to `docs/README.md`
- [x] All relative links in `docs/` resolve
- [x] `CHANGELOG.md` lists each step's items under `# docs/project`, and `version` is `docs/project`

## Review

Reviewed in a new session against the files on `docs/project` at `9c7b23d`, not against the ticked boxes.

**Validation:**

- `make test`: passes (65 tests, lint, static analysis, type coverage).
- `make test-all`: passes on SQLite, MySQL, PostgreSQL and MongoDB (65 tests each). The first run stopped at MySQL because a 13-day-old container pointed at a Docker network that no longer exists; `make db-down` fixed it. This was a problem with the local Docker setup, not with the package.
- `git archive HEAD | tar -t | grep '^docs/'` prints nothing.
- Every relative link and `#anchor` in `docs/` and the README's docs link resolves (checked with a script that also checks heading anchors). The only README link that doesn't resolve locally is `../../contributors`, which is an existing GitHub link.
- The SDK names in the docs match `route-connex-sdk-php` v1.1.0 (`ErClient`, `routeConnex()`, `run()->_id_()`, `microsoftSharepoint()->files()`, `hpeContentManager()->records()`, PHP `^8.1`, Guzzle `^7.9`). The package names match the code: `config/route-connex-laravel.php`, the `route-connex-laravel-*` publish tags, `RouteConnex\Laravel\Facades\RouteConnex`, the Boost skill path, and `mongodb/laravel-mongodb` as both `suggest` and `require-dev`.
- No consumer app or private repo is named in `docs/`, `README.md` or `CHANGELOG.md`.

**Callbacks with custom tenancy** (the question raised at the end of Step 6):

The HTTP side is covered. The route goes into the app's own group with `RouteConnex::callbackRoutes()`. The app's middleware finds the Tenant. The controller ignores extra route parameters. `RouteConnexCallbackHandler` and `RouteConnexCallbackUrlGenerator` can be replaced through the container. The gaps:

- **R1. No custom-tenancy hook outside a request.** The callback configuration command takes a Tenant only "when a Tenancy preset is on" (vision, ideas). An app with its own tenancy has no documented way to say "run this for Tenant X". That affects the callback configuration command, which needs the Tenant's App, Callback token and URL, the other Artisan commands, and `purgePayloads()` run once per Tenant. The URL generator alone can't solve it, because it runs only after the Tenant's App has been loaded. Needs a decision: a replaceable tenancy class that the `stancl/tenancy` preset implements and a custom app binds (a fifth extension point), or an Open question for the Artisan commands theme.
- **R2. The handler's steps aren't named.** `overrides.md` promises small `protected` methods per step, but `callbacks.md` doesn't name them. A custom app whose Apps live in a central database and whose Runs live in the Tenant's database must switch Tenants between "find the App" and "find the Run", so those step names are the extension contract. `callbacks.md` also doesn't say that the Run lookup follows the same connection rule as storing a Run (the Run model's own connection, else the App's, else the default). It only says the Run is "updated on its own connection".
- **R3. Queue workers and Tenants.** `callbacks.md` says the Run's connection "must exist in the queue worker". With custom tenancy, a shared connection name such as `tenant` exists in every worker but points at whichever Tenant was set last. A queued listener could then load the wrong Tenant's Run, or the wrong App through `$run->app`. The note should say that the worker must restore the same Tenant's context.
- **R4. Duplicate route names.** Calling `callbackRoutes()` in more than one group, or leaving `register_routes` on while also calling it, registers `route-connex.callbacks` twice. `php artisan route:cache` refuses duplicate route names. The docs should say to register the route once, or `callbackRoutes()` should take a name.
- **R5. ADR 0007 is missing the Step 6 additions:** any tenancy setup, extra route parameters ignored, and the `RouteConnexCallbackUrlGenerator`. ADR 0005 lists the generator; ADR 0007 doesn't.

**Consistency:**

- **R6.** The `CHANGELOG.md` item for Step 6 covers only the decision records. The callback changes made in that step are missing: Callbacks work with any tenancy setup, `RouteConnexCallbackUrlGenerator` is a fourth extension point, and the vision's callback URL line. The Step 6 notes above don't mention them either, although they weren't in the plan.
- **R7.** `sdk-layer.md` shows `$run->status; // 'created'`, but `run-tracking.md` casts `status` to the `RouteConnexRunStatus` enum (`RouteConnexRunStatus::Created`).
- **R8.** `run-tracking.md` and ADR 0004 throw `RouteConnexConfigurationException` when a Run has both `app_name` and `app_id`, or neither. `sdk-layer.md` defines that exception as covering only missing Credentials, an unknown App, or no default App.
- **R9.** It isn't stated whether `RouteConnexRunSucceeded` fires for `dry-run`. `RouteConnexRunFinished` includes `dry-run`, and the purge counts `dry-run` as successful. It also isn't stated what happens when a finished Run receives a different finished status (`succeeded` → `failed`).
- **R10.** `concepts.md` says the package generates the Callback token. A config App reads it from `ROUTE_CONNEX_CALLBACK_TOKEN`, and nothing generates that value until `route-connex:callback-token` (a Next theme). `apps.md` doesn't say how to create one in the meantime.
- **R11.** `docs/README.md` says a theme becomes `planned` only after its feature doc (`proposed`). Every Next and Later theme is `planned` with no feature doc.
- **R12.** AGENTS.md → Phase 1 step 2 lists `vision.md`, `concepts.md` and the features, but not `docs/decisions/`, which now holds the reasons behind the designs.
- **R13.** This file names RouteConnex's private repos (Context, and the Step 2 and 3 prompts) and a RouteConnex client plugin (Step 3 notes). It's committed and pushed. Blanking it at close doesn't remove it from history, so a rewording now only keeps it out of future commits.

**Acceptance criteria:** all met except the CHANGELOG one (R6).

**Fixes:** with the user's answers (Decisions → Review answers), R1–R5 and R9 go into Step 7, and R6–R8 and R10–R13 go into Step 8.
