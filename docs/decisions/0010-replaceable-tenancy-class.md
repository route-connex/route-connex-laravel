# 0010: A replaceable tenancy class

**Status:** accepted **Date:** 2026-11-17

## Context

The package gets its Tenant from the consumer app in two places: a call through the facade takes the App the app passes in ([0003](0003-apps-from-config-or-models.md)), and a Callback runs in the app's own route group ([0007](0007-callbacks.md)). Code that runs without a request or a caller has neither: the callback configuration command, the other Artisan commands, and scheduled work such as purging payloads once per Tenant. Before this decision only a Tenancy preset could take a Tenant there, so an app with its own tenancy setup had no way to say "run this for Tenant X". Apps with their own tenancy setups are among the first consumer apps. See [Overridable models and classes](../features/overrides.md).

## Decision

- `RouteConnexTenancy` (`RouteConnex\Laravel\Tenancy`) is the fifth public extension point, resolved from the container like the others ([0005](0005-models-in-config-classes-in-container.md)).
- Its methods: `enabled()`, `current()`, `findTenant(string $id)`, `run(mixed $tenant, Closure $callback)` and `forEachTenant(Closure $callback)`. A Tenant is whatever the consumer app uses for one; the package only passes it back.
- The shipped class means no tenancy: `enabled()` is `false`, `current()` is `null`, and `run()` and `forEachTenant()` just call the callback.
- A Tenancy preset is a shipped subclass, bound by the `tenancy` config section. The `stancl/tenancy` v3 preset is the first. An app with its own tenancy binds its own subclass.
- When tenancy is enabled, a command that acts on one App or Run requires `--tenant` and doesn't guess. Commands that sweep every Run (`route-connex:purge`, `route-connex:prune`) run for every Tenant through `forEachTenant()` unless `--tenant` limits them to one.
- `RouteConnexCallbackHandler::runInAppContext()` lets a Callback switch to the App's Tenant when the route can't find it, and `RouteConnexCallbackUrlGenerator` can build a Tenant's URL from `current()`.

## Consequences

- Presets and custom tenancy setups go through the same class, so every command and scheduled task works the same way with both.
- Single-tenant apps change nothing.
- The package still doesn't find a Tenant's Apps or detect Tenants for facade calls ([0003](0003-apps-from-config-or-models.md) stands). The class is used only where nothing else sets the Tenant.
- `run()` must restore the previous context even when the callback throws, or a long-running worker stays in the wrong Tenant. The docs and the Boost skill say so.
- Queued listeners still rely on the consumer app's tenancy to restore the Tenant in the worker; the class doesn't cover queues.
