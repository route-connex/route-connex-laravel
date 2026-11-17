# 0003: Apps come from config or from models, without a resolver

**Status:** accepted **Date:** 2026-11-17

## Context

A consumer app can have several Apps (credential pairs), and a multi-tenant app needs its own per Tenant, kept in the Tenant's database. A global resolver (`RouteConnex::resolveAppsUsing(...)`) would have to know about Tenants, and cached clients would need Tenant-aware keys. See [Multiple Apps](../features/apps.md).

## Decision

`RouteConnex::app()` takes either a string, the name of an App in config, or a model that implements `RouteConnexAppContract`. Calls without `app()` use the default config App and throw `RouteConnexConfigurationException` if there isn't one. The package ships a `RouteConnexApp` model and an `InteractsWithRouteConnexApp` trait for consumer apps' own models, and the model class is set in `models.app`. There is no resolver: the consumer app loads the model, already from the right Tenant's database, and passes it in.

## Consequences

- The package needs no knowledge of Tenants; a model loaded from the Tenant's connection already carries the right Credentials.
- Single-tenant apps keep working with `.env` only.
- The consumer app is responsible for loading the right App; the package can't pick one.
- The contract is an interface with a trait, not an abstract class, so apps' existing models can implement it.
