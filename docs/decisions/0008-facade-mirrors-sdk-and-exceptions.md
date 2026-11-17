# 0008: The facade mirrors the SDK, and errors are wrapped

**Status:** accepted **Date:** 2026-11-17

## Context

The package is a thin Laravel layer over `route-connex/route-connex-sdk-php`, which returns raw PSR-7 responses and throws Guzzle and its own exceptions. See [Laravel layer over the SDK](../features/sdk-layer.md).

## Decision

- The `RouteConnex` facade mirrors the SDK's endpoint tree, each endpoint mapped explicitly. Services that create a Run return a `RouteConnexRun`; the others return a `RouteConnexResponse` that extends Laravel's HTTP client `Response`. A `meta:` argument carries Meta. `RouteConnex::sdk()` gives the configured SDK client for anything not mapped yet.
- Named convenience methods per Service (such as `sharepoint()->uploadFile()`) are left for later.
- Every exception the package throws extends `RouteConnexException`, with `RouteConnexRequestException`, `RouteConnexConnectionException`, `RouteConnexAuthenticationException` and `RouteConnexConfigurationException`. They wrap the SDK's and Guzzle's exceptions.

## Consequences

- The package stays thin, and typed return values make the Boost skill and the docs precise.
- A new SDK endpoint needs a small change here before the facade offers it; `sdk()` covers the gap.
- Consumer apps don't depend on Guzzle, and can handle or silence every package exception at once with `dontReport(RouteConnexException::class)`.
- Caching OAuth tokens per App needs an SDK change (accepting a token from outside).
