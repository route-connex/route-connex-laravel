# Ideas

A parking lot for rough ideas that haven't been checked yet. Add one entry per idea: a dated heading (`## YYYY-MM-DD: Title`) and one or two lines. When an idea matures, it gets a `features/<name>.md` and its entry links there.

<!-- Entries go below, newest first. -->

## 2026-11-17: Dry-run mode

A `dry_run` setting in config and `.env` (the default for every App) and one on each App. When it's on, every request is sent as a dry run, so the app gets real data back with no risk of touching the target System. Blocked on RouteConnex: the API doesn't let clients ask for a dry run yet (see Open questions in [concepts.md](concepts.md)).

## 2026-11-17: Testing helpers

`RouteConnex::fake()`, like Laravel's `Http::fake()` and `Event::fake()`, so consumer apps can test their code without calling the RouteConnex API. It should also make it easy to simulate a Callback, so tests can cover what happens when a Run finishes.

## 2026-11-17: Link Runs to models

A trait or relation that uses Meta to tie a `RouteConnexRun` to an Eloquent model in the consumer app, so a model can list its Runs and a finished Run can find its model.

## 2026-11-17: More Artisan commands

Besides the callback configuration command (a 1.0 goal in [vision.md](vision.md)):
- `route-connex:test-auth {app}`: the credential check from the terminal, using the `test-auth` Service.
- `route-connex:apps`: lists the configured Apps, showing the dry-run flag and whether a Callback token is set.
- `route-connex:callback-token {app}`: generates or rotates an App's Callback token.
- `route-connex:runs`: lists recent `RouteConnexRun` records, filterable by App and status.
- `route-connex:prune`: deletes old `RouteConnexRun` records.

Every command must work with Tenants: App names can repeat across Tenants, so a command has to know which Tenant's App and `RouteConnexRun` records it acts on, and must not guess when the Tenancy preset is on.

## 2026-11-17: Polling as a fallback for missed Callbacks

A Callback can get lost, for example when the consumer app is down, and the `RouteConnexRun` would then stay `running` forever. A fallback, such as a scheduled command, would re-check Runs that have been unfinished for too long. Depends on the SDK offering a way to get a Run. Low priority until a lost Callback becomes a real problem.

## 2026-11-17: Apps tied to a customer

RouteConnex may change an App from one System to a whole customer (see Open questions in [concepts.md](concepts.md)). In the package an App is only a named credentials pair, so it should work either way with no change; this entry is to check that when RouteConnex decides.

## 2026-11-17: Other tenancy modes

Single-database tenancy (scoping by `tenant_id`) and `stancl/tenancy` version 4, after version 3 with a database per tenant, which is the first Tenancy preset. Apps with other setups can plug in through the overridable classes.
