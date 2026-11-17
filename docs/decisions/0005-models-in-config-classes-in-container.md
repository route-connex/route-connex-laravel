# 0005: Models in config, other classes bound in the container

**Status:** accepted **Date:** 2026-11-17

## Context

The vision promises that everything is overridable, so apps can change tables, connections, fields and behavior without forking the package. Eloquent relations and queries need class names, while other classes can be resolved from the container. See [Overridable models and classes](../features/overrides.md).

## Decision

- The models are set in config (`models.app`, `models.run`), and the package always uses the configured class.
- The other replaceable classes are resolved from the container, and a consumer app binds its own subclass in a service provider. Before 1.0 the public extension points are `RouteConnexClientFactory`, `RouteConnexRunRecorder`, `RouteConnexCallbackHandler` and `RouteConnexCallbackUrlGenerator`; every other class is internal and can change between minor versions.
- Each replaceable class has small `protected` methods per step, so a subclass overrides one step.
- There is no `classes` map in config.

## Consequences

- Both mechanisms are Laravel-native; apps use what they already know.
- The set of supported extension points is small and explicit, and the internals stay free to change before 1.0.
- A class that isn't on the list can't be replaced without a package change.
- The `stancl/tenancy` preset can make the same bindings for the app.
