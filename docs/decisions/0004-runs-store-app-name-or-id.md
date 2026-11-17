# 0004: Runs store `app_name` or `app_id`, resolved through `models.app`

**Status:** accepted **Date:** 2026-11-17

## Context

A `RouteConnexRun` must find its App again, for example when a Callback arrives and the Callback token has to be checked. Apps are either named in config or models (see [0003](0003-apps-from-config-or-models.md)). A polymorphic reference (`app_type` and `app_id`) would store a class name, which breaks when the App model is renamed or replaced, and `nullableMorphs()` creates a bigint `app_id` that can't hold UUID or MongoDB ObjectId keys.

## Decision

`route_connex_runs` has `app_name` (nullable string) for a config App and `app_id` (nullable string) for a model App. Exactly one is set, and the model throws `RouteConnexConfigurationException` when saving a Run with both or neither. No class name is stored: a model App is always found through `models.app`. See [`RouteConnexRun` tracking](../features/run-tracking.md).

## Consequences

- Replacing or extending the App model, for example to add MongoDB's `DocumentModel`, never breaks stored Runs.
- Keys of any type (integer, UUID, ObjectId) fit in `app_id`.
- The package supports one App model class at a time.
- The exclusivity is enforced by the model, not the database: MySQL only enforces `CHECK` from 8.0.16, Laravel's schema builder has no portable way to declare one and MongoDB has none.
