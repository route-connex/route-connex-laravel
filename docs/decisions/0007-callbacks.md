# 0007: Callbacks: token check, route outside `web`, events on change

**Status:** accepted **Date:** 2026-11-17

## Context

RouteConnex reports a finished Run with a Callback, and Callback authentication in RouteConnex is still being completed. A multi-tenant consumer app needs the route in the Tenant's own routes. See [Callbacks as events](../features/callbacks.md).

## Decision

- **Route:** `POST {prefix}/callbacks/{app}`, registered by the package unless `callbacks.register_routes` is false, in which case the consumer app calls `RouteConnex::callbackRoutes()` in its own route group. It doesn't use the `web` middleware group.
- **Checks, in order:** find the App (404), compare `route_connex_callback_token` in constant time (403, with a TODO where RouteConnex's own authentication will go), find the Run by `run_id` and App (404), update it, fire events and answer 200.
- **Events:** only when the status changed, so a repeated Callback fires nothing, and a finished Run never goes back to an unfinished status. `RouteConnexRunCreated`, `RouteConnexRunStatusChanged`, `RouteConnexRunFinished`, `RouteConnexRunSucceeded` and `RouteConnexRunFailed` (`failed` and `errored`).
- **Jobs:** consumer apps use queued listeners; there is no separate job mechanism.

## Consequences

- Callbacks can live in a Tenant's route group, and apps without tenancy get a working route by default.
- Events are idempotent, so retries from RouteConnex are safe.
- The token is a simple shared secret until RouteConnex authenticates Callbacks; the body format is the one the package is built for and may change with RouteConnex.
- An app that puts the route in a group with the `web` middleware must exclude it from CSRF.
