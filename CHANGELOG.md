# feature/guzzle-8

- Guzzle 8 support, with `route-connex/route-connex-sdk-php` ^1.2.

---

# v0.1.0-rc

- Docker toolchain for PHP 8.4, driven by `make` targets, so no local PHP is needed.
- Package skeleton with the `RouteConnex` facade, publish-only migrations, a Boost skill and `route-connex-laravel-*` publish tags.
- Support for Laravel 12 and 13 on PHP 8.3+.
- Tests on SQLite, MySQL 8.0+, PostgreSQL 14+ and MongoDB 7.0+, with configurable database versions.
- CI across PHP 8.3–8.5, Laravel 12/13, both stability lanes and every database; docs-only changes skip it.
- Tests for the facade and every publish tag.
- Dev tooling stays out of the Composer dist archive.
- Contributor and agent docs for the Docker workflow and the task plan workflow.
- Branching model documented, with a `version` file and branch-named CHANGELOG sections.
- Pest test for the `version` file format.
- CI runs on `release/*.x` pushes, and a tag push must match `version` and the CHANGELOG.
- Tag pushes are rejected unless they are a plain `vX.Y.Z`.
- Release candidates: unreleased work collects under `# vX.Y.Z-rc`, and tasks close into it.
- PHPStan capped below 2.3.6 in `require-dev` until the type coverage plugin supports it.
- Project docs in `docs/`: vision, concepts, ideas, roadmap, feature designs and decision records.
- `RouteConnexTenancy` designed, so apps with their own tenancy get per-Tenant commands and scheduled work.
- Callback design for custom tenancy: overridable handler steps, named routes and per-Tenant callback URLs.
- Shorter CHANGELOG items, with rules for their length in AGENTS.md and the release skill.
- Package config with the RouteConnex base URL, a default App and named Apps from `.env`.
- `RouteConnex::app()` and `RouteConnex::sdk()` give a config App's client and its SDK client.
- `RouteConnexClientFactory` builds each App's SDK client and can be replaced in the container.
- `RouteConnexConfigurationException` for a missing default App, an unknown App or empty Credentials.
- The facade mirrors the SDK's endpoints: `routeConnex()`, `run()`, `microsoftSharepoint()` and `hpeContentManager()`.
- Calls return a `RouteConnexResponse`, with Laravel's HTTP client helpers such as `json()` and `status()`.
- Calls that create a Run take a `meta:` argument, sent where the API expects it.
- `RouteConnexRequestException` for a 4xx or 5xx answer from the API, carrying the `RouteConnexResponse`.
- `RouteConnexAuthenticationException` when the token request is rejected or issues no usable token.
- `RouteConnexConnectionException` when the API can't be reached or a request fails without an error answer.
- Every package exception extends `RouteConnexException`; `RouteConnex::sdk()` still throws the SDK's own.
- Boost skill and README document the config, the facade, Meta, responses and exceptions.
- Project docs mark the SDK layer `in progress` and note what Apps, run tracking and overrides build on.
