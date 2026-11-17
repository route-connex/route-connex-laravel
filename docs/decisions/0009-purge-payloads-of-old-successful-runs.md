# 0009: Purge payloads of old successful Runs instead of deleting Runs

**Status:** accepted **Date:** 2026-11-17

## Context

A `RouteConnexRun` stores the request and response of the Service call. They are useful while a Run is unfinished or after it failed, and for a while after it succeeded, but then they are only dead weight in the consumer app's database, and may hold sensitive data. See [`RouteConnexRun` tracking](../features/run-tracking.md).

## Decision

`RouteConnexRun::purgePayloads()` sets `request` and `response` to null on `succeeded` and `dry-run` Runs that finished more than `runs.purge_after_days` days ago (30 by default, `null` keeps them forever) and sets `purged_at`. `failed`, `errored` and unfinished Runs keep their payloads, and every other column stays. The consumer app calls the method from its own scheduler (once per Tenant in a multi-tenant app); a `route-connex:purge` command is planned with the Artisan commands. `runs.store_request` and `runs.store_response` turn off storing either payload from the start.

## Consequences

- The history of Runs (IDs, status, type, Meta, App) stays, while the large JSON values go.
- Failed Runs stay debuggable.
- A purged Run can't be replayed or inspected afterwards.
- Deleting old Runs is a separate decision, left to `route-connex:prune` in the Artisan commands.
