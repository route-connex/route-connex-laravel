# 0002: RouteConnex prefix on everything the package adds

**Status:** accepted **Date:** 2026-11-17

## Context

The package is installed in consumer apps that already have their own models, tables, events and config. A Laravel app may well have a `Run` model, a `runs` table or an `apps` table of its own. Names without a prefix would clash with them.

## Decision

Everything the package adds starts with `RouteConnex`: models (`RouteConnexRun`, `RouteConnexApp`), contracts, events, exceptions and classes. Tables and config keys use `route_connex_` (`route_connex_runs`, `route_connex_apps`) or the package's config file name, `route-connex-laravel`. Glossary terms stay plain (Run, App) in the docs; see [concepts.md](../concepts.md).

## Consequences

- Package names never clash with the consumer app's own `Run` or `App` classes or tables.
- Class names are longer, and short imports need aliases.
- The rule applies to everything new; a feature doc that adds a name without the prefix is wrong.
