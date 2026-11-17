# 0007: Callbacks: token check, route outside `web`, events on change

**Status:** accepted **Date:** 2026-11-17

## Context

RouteConnex reports a finished Run with a Callback, and Callback authentication in RouteConnex is still being completed. A multi-tenant consumer app needs the route in the Tenant's own routes, and consumer apps find their Tenants in different ways (a subdomain, a path, a header, a tenancy package, their own code). See [Callbacks as events](../features/callbacks.md).

## Decision

- **Route:** `POST {prefix}/callbacks/{app}`, named `route-connex.callbacks`, registered by the package unless `callbacks.register_routes` is false, in which case the consumer app calls `RouteConnex::callbackRoutes()` in its own route group. `callbackRoutes()` takes an optional route name, so the route can be registered in several groups without breaking `route:cache`. It doesn't use the `web` middleware group.
- **Any tenancy setup:** the package assumes no tenancy library. The consumer app's group and middleware find the Tenant, and the controller reads only the `app` route parameter and ignores any other (such as a `{tenant}` domain parameter).
- **Checks, in order:** each a `protected` step of `RouteConnexCallbackHandler`. Find the App (404), compare `route_connex_callback_token` in constant time (403, with a TODO where RouteConnex's own authentication will go), then, inside `runInAppContext()` (a no-op by default, where a subclass can switch to the App's Tenant), find the Run by `run_id` and App on the same connection a Run is stored on (404), update it and fire events. Answer 200.
- **Finished is final:** as in RouteConnex, a finished Run's status never changes. A late unfinished status is ignored. A different finished status is ignored with a logged warning. Both still get 200.
- **Events:** only when the status changed, so a repeated Callback fires nothing. `RouteConnexRunCreated`, `RouteConnexRunStatusChanged`, `RouteConnexRunFinished`, `RouteConnexRunSucceeded` (`succeeded` and `dry-run`) and `RouteConnexRunFailed` (`failed` and `errored`).
- **Callback URL:** built by the replaceable `RouteConnexCallbackUrlGenerator`, so an app whose Callbacks live on each Tenant's own domain returns that Tenant's URL. The callback configuration command runs it in the Tenant's context through `RouteConnexTenancy` (see [0010](0010-replaceable-tenancy-class.md)).
- **Jobs:** consumer apps use queued listeners; there is no separate job mechanism.

## Consequences

- Callbacks can live in a Tenant's route group, or switch to the App's Tenant in the handler, and apps without tenancy get a working route by default.
- Events are idempotent, so retries from RouteConnex are safe.
- The token is a simple shared secret until RouteConnex authenticates Callbacks; the body format is the one the package is built for and may change with RouteConnex.
- An app that puts the route in a group with the `web` middleware must exclude it from CSRF.
- Queued listeners need the consumer app's tenancy to restore the same Tenant in the worker; a connection with the same name isn't enough.
