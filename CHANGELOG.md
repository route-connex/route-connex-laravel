# docs/project

- Project documentation scaffold in `docs/` (vision, concepts, roadmap, ideas, feature and decision templates), kept out of the Composer dist archive and linked from the README and AGENTS.md.

---

# v0.1.0-rc

- Docker toolchain for PHP 8.4 with Composer, driven by `make` targets (`make help` lists them): tests, lint, static analysis, the workbench server, Testbench and per-database test runs. No local PHP is needed.
- Laravel package skeleton: config, routes, views, translations, an Artisan command, publish-only migrations, the `RouteConnex` facade, assets and the Laravel Boost skill. Publish tags: `route-connex-laravel` plus `-config`, `-views`, `-lang`, `-assets` and `-migrations`.
- Support for Laravel 12 and 13 on PHP 8.3+.
- Tests on SQLite, MySQL 8.0+, PostgreSQL 14+ and MongoDB 7.0+ (`mongodb/laravel-mongodb` is a suggestion, not a requirement), with the database image versions configurable through `MYSQL_VERSION`, `POSTGRES_VERSION` and `MONGO_VERSION`. The test connections start from Laravel's own database defaults.
- CI on GitHub Actions: Laravel 12/13 × `prefer-lowest`/`prefer-stable` × PHP 8.3–8.5 on Linux and Windows with SQLite, plus a `databases` job that runs the minimum and latest version of each database with the same Laravel and stability axes. Documentation-only changes skip CI.
- Tests for the facade and every publish tag.
- Dev tooling (Docker files, `Makefile`, `PLAN.md`) stays out of the Composer dist archive.
- Contributor and agent documentation for the Docker workflow, the minimum supported versions and the task plan workflow; the README credits link to the author's GitHub profile.
- Branching model documented (`release/<major>.x` branches, `main` as the latest stable release, `feature/`, `hotfix/` and `docs/` work branches, `v<major>.<minor>.<patch>` tags), with branch-named CHANGELOG sections for work in progress and a `version` file at the repo root naming the tag or branch the code is on.
- Pest test for the `version` file: it must hold one line that is a `vX.Y.Z` tag, a `vX.Y.Z-rc` release candidate or a `feature|hotfix|docs` work branch name.
- CI runs on pushes to `release/*.x` branches (the old `*.x` filter missed them), and a tag push fails unless `version` equals the tag and the first CHANGELOG heading is `# <tag> (`.
- The tag workflow also rejects any tag that isn't a plain `vX.Y.Z`, such as `vX.Y.Z-rc`.
- Release candidates: unreleased work on a release branch is collected under `# vX.Y.Z-rc` in `CHANGELOG.md` (replacing `# Unreleased`), and `version` holds the same `vX.Y.Z-rc` instead of the release branch name. Only plain `vX.Y.Z` tags exist. A task's close now requires the target branch to be merged into the task branch first, adds its items to the existing rc or asks for the version to target, and the maintainer closes contributors' pull requests. `main` is fast-forwarded to the tag, not to the release branch.
