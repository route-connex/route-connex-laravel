# Roadmap

Themes are ordered by priority, not by date. Status labels are described in the [docs index](README.md#status-labels). Each theme links to its `features/*.md` once one exists, and to the idea it came from in [ideas.md](ideas.md) when it started there. The other themes come from the goals in [vision.md](vision.md).

Version ranges are a guide, not a promise: the package is at `0.x` until 1.0.

Every theme also updates the bundled [Laravel Boost](https://github.com/laravel/boost) skill, so coding agents in consumer apps know how to use what it adds.

## Now

Target: `0.1` to `0.x`. The core of the package.

- **Laravel layer over the SDK**: the facade, the container binding and config, with credentials from `.env`, and package exceptions. Status: `planned`. Target: 0.x. Feature: [sdk-layer.md](features/sdk-layer.md).
- **Multiple Apps**: named Apps in config, like database connections, and App models (the shipped `RouteConnexApp` or the consumer app's own) for multi-tenant apps. Status: `planned`. Target: 0.x. Feature: [apps.md](features/apps.md).
- **`RouteConnexRun` tracking**: Runs stored and kept up to date on MySQL, PostgreSQL, MongoDB and SQLite, with payloads of old successful Runs purged. Status: `planned`. Target: 0.x. Feature: [run-tracking.md](features/run-tracking.md).
- **Callbacks as events**: the Callback route, the Callback token check and the events the consumer app listens to. Status: `planned`. Target: 0.x. Feature: [callbacks.md](features/callbacks.md).
- **Overridable models and classes**: models set in config, and the package's replaceable classes bound in the container, including `RouteConnexTenancy` for apps with their own tenancy setup. Status: `planned`. Target: 0.x. Feature: [overrides.md](features/overrides.md).

## Next

Target: `0.x` up to 1.0.

- **Sync calls**: ask the RouteConnex API to answer straight away and get the data back in the same call. Status: `planned`. Target: 0.x to 1.0.
- **Credential checks**: check that an App's credentials work, for example when a Tenant saves them. Status: `planned`. Target: 0.x to 1.0.
- **`stancl/tenancy` v3 preset**: the `tenancy` config section and a shipped `RouteConnexTenancy` subclass for a database per Tenant. Status: `planned`. Target: 0.x to 1.0.
- **Artisan commands**: the callback configuration command, `route-connex:test-auth`, `route-connex:apps`, `route-connex:callback-token`, `route-connex:runs`, `route-connex:prune` and `route-connex:purge`. Every command works with Tenants through `RouteConnexTenancy`, with a preset or the app's own tenancy. Status: `planned`. Target: 0.x to 1.0. Idea: [More Artisan commands](ideas.md#2026-11-17-more-artisan-commands).
- **Testing helpers**: `RouteConnex::fake()` and simulated Callbacks for consumer apps' tests. Status: `planned`. Target: 0.x to 1.0. Idea: [Testing helpers](ideas.md#2026-11-17-testing-helpers).

## Later

Target: not scheduled, up to 1.0 or after.

- **Dry-run mode**: a `dry_run` setting in config, `.env` and each App that sends every request as a dry run. Blocked on RouteConnex: the API can't take it yet. Status: `planned`. Idea: [Dry-run mode](ideas.md#2026-11-17-dry-run-mode).
- **Link Runs to models**: tie a `RouteConnexRun` to a consumer app model through Meta. Status: `planned`. Idea: [Link Runs to models](ideas.md#2026-11-17-link-runs-to-models).
- **Polling fallback**: re-check Runs whose Callback never arrived, using the SDK's call to get a Run. Status: `planned`. Idea: [Polling as a fallback for missed Callbacks](ideas.md#2026-11-17-polling-as-a-fallback-for-missed-callbacks).
- **Other tenancy modes and Apps tied to a customer**: single-database tenancy, `stancl/tenancy` v4, and checking that Apps work when RouteConnex ties them to a customer. Status: `planned`. Ideas: [Other tenancy modes](ideas.md#2026-11-17-other-tenancy-modes), [Apps tied to a customer](ideas.md#2026-11-17-apps-tied-to-a-customer).
