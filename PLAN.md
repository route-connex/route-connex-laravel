# Task 1: Basic package setup (Docker + skeleton + multi-DB tests)

**Status:** in progress (Phase 3 review done; its follow-up steps 8–12 are done and committed; steps 13–16, from the review observations, are planned and wait for the plan commit)
**Branch:** main → **Target:** main (initial setup, made before the task plan workflow existed)
**Planned on:** `xps-omarchy`
**Planning session:**

```shell
claude --ide --resume 1e90e112-fc92-4d2c-9824-c49374c32b7f
```

### Implementation

Steps 0–6 were done before this list was part of the workflow, so they have no entries.

- [x] **7. Skip CI for documentation-only changes**
  - **Model:** `sonnet`
  - **Effort:** `low`
  - **Session:** `new`
  - **Resume:** `claude --ide --resume 9a8d2e18-651d-49ba-9bd8-59def7984e6b` (xps-omarchy)
  - **Why:** Small, contained YAML change. It only needs care so that the ignore list never covers files CI depends on. The planning session is very long and about Task 1 setup, and the prompt plus the step section are self-contained.
  - **Prompt:** Implement Step 7 of PLAN.md: Skip CI for documentation-only changes. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Only `.github/workflows/tests.yml` and `AGENTS.md` change: keep the existing `push`/`pull_request` triggers and jobs as they are, and run `actionlint` through Docker (`docker run --rm -v "$PWD":/repo -w /repo rhysd/actionlint:1.7.7 .github/workflows/tests.yml`). Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. When it's validated, set this entry's Done to [x], then stop and give a suggested commit message. Don't start the review.
- [x] **Review (Phase 3)**
  - **Model:** `opus`
  - **Effort:** `high`
  - **Session:** `new`
  - **Resume:** `claude --ide --resume ea9d766f-1d8f-4e27-8421-5af3ecd48611` (xps-omarchy)
  - **Why:** A full review across Docker, the skeleton, the multi-database tests and CI needs broad judgement. Reviews are always a fresh session that didn't build Task 1.
  - **Prompt:** Do the Phase 3 review of Task 1 in PLAN.md. Follow AGENTS.md (Task Plan Workflow → Phase 3). Before starting, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Check every step against the actual code, not the ticked boxes. Look for gaps and errors in the Docker setup, Makefile, composer.json, the service provider and skeleton wiring, tests/TestCase.php and the database tests, the CI workflow, and whether the docs match the code. Run `make test` and `make test-all`, plus the minimum database versions (`MYSQL_VERSION=8.0 make test-mysql`, `POSTGRES_VERSION=14 make test-pgsql`, `MONGO_VERSION=7.0 make test-mongodb`). Write the findings in a **Review** section of PLAN.md, propose fixes as new steps (each with an Implementation entry) without implementing them, set this entry's Done to [x], then stop and give a suggested commit message.
- [x] **8. Keep dev tooling out of the Composer dist archive**
  - **Model:** `haiku`
  - **Effort:** `low`
  - **Session:** `new`
  - **Resume:** `claude --ide --resume 6bbbdbf3-e722-4e67-a582-411d745aa949` (xps-omarchy)
  - **Why:** A few lines in `.gitattributes`, checked with one `git archive` command. The review session is long and about the whole task.
  - **Prompt:** Implement Step 8 of PLAN.md: Keep dev tooling out of the Composer dist archive. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Only `.gitattributes` changes. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 9.
- [x] **9. Test the facade and the publish tags**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `new`
  - **Resume:** `claude --ide --resume 53980e7a-cfec-47b1-9f4e-38bc6de59b88` (xps-omarchy)
  - **Why:** Typical Pest/Testbench test work against the existing service provider. Independent of Step 8.
  - **Prompt:** Implement Step 9 of PLAN.md: Test the facade and the publish tags. Follow AGENTS.md (Task Plan Workflow), the `package-testing` skill and the step's section in PLAN.md. Only tests change; `src/RouteConnexServiceProvider.php` stays as it is. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change (in particular, ask before changing package code if a test exposes a bug). Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 10.
- [x] **10. Make the `make` test targets ignore the host's `DB_CONNECTION` and accept `ARGS`**
  - **Model:** `sonnet`
  - **Effort:** `low`
  - **Session:** `new`
  - **Resume:** `claude --ide --resume ddf73b5f-a0fe-4082-8fa5-b11caa09f5a1` (xps-omarchy)
  - **Why:** Small Makefile/Compose change, but it touches how every test target picks its database, so it needs a careful check of each target. Independent of Step 9's test work.
  - **Prompt:** Implement Step 10 of PLAN.md: Make the `make` test targets ignore the host's `DB_CONNECTION` and accept `ARGS`. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Only `Makefile` and `docker-compose.yml` change (README/AGENTS docs follow in Step 11). Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 11.
- [x] **11. Align CONTRIBUTING, README and the agent skills with the Docker workflow**
  - **Model:** `haiku`
  - **Effort:** `low`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume ddf73b5f-a0fe-4082-8fa5-b11caa09f5a1` (xps-omarchy)
  - **Why:** Docs-only edits. It documents the `ARGS` support from Step 10, so it follows it in the same session.
  - **Prompt:** Implement Step 11 of PLAN.md: Align CONTRIBUTING, README and the agent skills with the Docker workflow. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Step 10 added `ARGS` to `make test-unit` and the `make test-<db>` targets; describe what the Makefile actually does. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 12.
- [x] **12. Bring Task 1's steps in line with what was built**
  - **Model:** `haiku`
  - **Effort:** `low`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume ddf73b5f-a0fe-4082-8fa5-b11caa09f5a1` (xps-omarchy)
  - **Why:** Text edits inside PLAN.md only, each one listed in the step.
  - **Prompt:** Implement Step 12 of PLAN.md: Bring Task 1's steps in line with what was built. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Only `PLAN.md` changes. Check each listed item against the code before editing it. Before changing anything, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Tick the step's boxes as you go, and ask before any unplanned change. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't close the task.
- [x] **13. Build the test database connections on Laravel's defaults**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `new`
  - **Resume:** `claude --ide --resume f46eb0db-f1c1-40e7-af76-a63df28b7edb` (xps-omarchy)
  - **Why:** A small change to `tests/TestCase.php`, but it affects every database run, so all four drivers and the minimum versions must be rechecked. The planning session is long and about the review.
  - **Prompt:** Implement Step 13 of PLAN.md: Build the test database connections on Laravel's defaults. Follow AGENTS.md (Task Plan Workflow), the `package-testing` skill and the step's section in PLAN.md. Only `tests/TestCase.php` changes. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 14.
- [ ] **14. Check the Laravel 12 floor on every database in CI**
  - **Model:** `sonnet`
  - **Effort:** `low`
  - **Session:** `new`
  - **Resume:** –
  - **Why:** A contained YAML change to the `databases` job, checked with `actionlint`; independent of Step 13's code.
  - **Prompt:** Implement Step 14 of PLAN.md: Check the Laravel 12 floor on every database in CI. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Only `.github/workflows/tests.yml` changes: add a `stability` axis to the `databases` job only, keep the `tests` job as it is. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run `actionlint` through Docker (`docker run --rm -v "$PWD":/repo -w /repo rhysd/actionlint:1.7.7 .github/workflows/tests.yml`). Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 15.
- [ ] **15. Link the README credits to the author's GitHub profile**
  - **Model:** `haiku`
  - **Effort:** `low`
  - **Session:** `same`
  - **Resume:** –
  - **Why:** A one-line docs edit; it fits at the end of Step 14's short session.
  - **Prompt:** Implement Step 15 of PLAN.md: Link the README credits to the author's GitHub profile. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Only `README.md` changes. Before changing anything, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Tick the step's boxes as you go, and ask before any unplanned change. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 16.
- [ ] **16. Add CHANGELOG.md and make it part of the workflow**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `new`
  - **Resume:** –
  - **Why:** Docs and workflow rules only, but the Task 1 entry must summarise what was actually built across Steps 0–15 and the rule must fit the existing Phase 4 and release skill wording. A fresh session reads the whole plan and git log without unrelated context.
  - **Prompt:** Implement Step 16 of PLAN.md: Add CHANGELOG.md and make it part of the workflow. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Steps 13–15 must be committed first, since the changelog lists them; read `git log` and the whole plan to write the Task 1 entry. Only `CHANGELOG.md` (new), `AGENTS.md` and `.agents/skills/package-release/SKILL.md` change. Before changing anything, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't close the task.

> **Progress notes**
> - The host's Docker networking was fixed outside the repo: NetworkManager no longer manages `docker0`/`br-*`/`veth*` (`/etc/NetworkManager/conf.d/99-docker-unmanaged.conf`). No compose overrides are needed.
> - The skeleton was taken from commit `064b57f` with `git clone`; the GitHub tarball leaves out tests/, workbench/ and dotfiles (`export-ignore`). It already ships `^12.0||^13.0`, Testbench `^10||^11`, and a CI matrix for L12/L13 × prefer-lowest/stable.
> - `configure.php` builds the namespace as `<vendor-namespace>\<class-name>`, and the vendor part can't contain `\`. It was run with `RouteConnex`/`RouteConnex`, and the namespace was then renamed to `RouteConnex\Laravel` by hand.
> - CI: the existing matrix (OS × PHP × L12/13 × lowest/stable) stays on SQLite, with `mongodb` added to its extensions. A separate `databases` job runs L12/L13 × mysql/pgsql/mongodb on PHP 8.4 with service containers. That's 6 extra jobs instead of multiplying the matrix by 4 (14 after Step 6 added the minimum and newer database versions).
> - Database runs use `vendor/bin/pest` without `--parallel`: parallel processes would share one MySQL/Postgres/Mongo database.
> - The Laravel 12 floor passes on all four databases. `--prefer-lowest` resolves Laravel 12.69 because of the dev dependencies; the package itself still requires `^12.0`.
> - The skeleton's placeholder package route (`routes/route-connex-laravel.php`) is commented out, so there's no package URL to curl yet. The workbench `/` returns 200.
> - Watch out: `configure.php` replaces placeholder words (`skeleton`, `vendor-name`, ...) in **every** text file, including this one. It has already run and deleted itself, so this won't happen again.

### Context
The repo has only `README.md` and `LICENSE` (MIT, RouteConnex). The goal is to scaffold a Laravel package from the official `laravel/package-skeleton`, which is what the Laravel 13.x package docs recommend. The machine has no local PHP, so Composer, the skeleton's configure script, tests, lint, static analysis and the workbench dev server all run in Docker (Docker 29.9 and Compose v5 are installed). This step covers only the basic setup. The plugin's actual features come later.

Decisions made:
- Composer name `route-connex/route-connex-laravel`, namespace `RouteConnex\Laravel`, class name `RouteConnex`.
- Keep all skeleton features: config, routes, views, translations, commands, migrations, facade, assets, and the boost skill.
- Minimal repo tooling: keep CI workflows, drop dependabot, issue template, changelog automation, funding and the security policy.
- **Minimum Laravel version: 12.** Laravel 12 and 13 are both supported.
  - `composer.json` requires `"illuminate/support": "^12.0||^13.0"`; any other `illuminate/*` package we add later uses the same constraint.
  - Dev dependencies need matching ranges: `orchestra/testbench` `^10.0||^11.0` (10 targets Laravel 12, 11 targets Laravel 13), and Larastan and pest-plugin-laravel ranges that cover both.
  - PHP `^8.3`, which is Laravel 13's minimum and also works for Laravel 12 (12 itself only needs 8.2). The Docker image uses 8.4.
  - CI matrix: Laravel 12 and 13, each with `prefer-lowest` and `prefer-stable`.

### What the skeleton gives us
- PHP `^8.3`, `illuminate/support ^12.0||^13.0`, Orchestra Testbench, Pest (with the laravel and type-coverage plugins), Larastan, Pint, and a `workbench/` app.
- Composer scripts: `lint`, `lint:check`, `analyse`, `test:types`, `test:unit` (parallel Pest), `test` (all of them), `build`, `serve` (workbench on :8000).
- `configure.php` runs on `composer install`, or by hand as `php configure.php`. It renames `Skeleton*` and replaces placeholders. In non-interactive mode it takes flags: `--author-name --author-email --package-name --package-name-human --package-description --vendor-namespace --class-name`, plus one flag per feature/tool. It deletes itself when it finishes. Passing no tool flags keeps all tools, so after it runs we remove the unwanted tool files by hand, unless `--help` shows a way to opt out.
- The service provider is auto-discovered through `extra.laravel.providers` and `aliases` in composer.json (per the docs).

### Steps

#### 0. Shared task plan file: `PLAN.md` (repo root)
- [x] Before anything else, create `PLAN.md` and copy this whole plan into it, so other agents can pick up the work.
- [x] Structure:
  - a short header explaining how the file is used: one section per task, each with Status (`todo` / `in progress` / `done`), Context, Steps as `- [ ]` checkboxes, and Verification
  - newest task first; finished tasks stay as history
- [x] First entry: **"Task 1: Basic package setup (Docker + skeleton + multi-DB tests)"**, containing steps 1–5 and the verification list below as checkboxes.
- [x] Tick checkboxes as each step is completed during implementation, and set the status to `done` at the end.
- [x] After the skeleton generates `AGENTS.md` (which `CLAUDE.md` links to), add a line there: "Active and past task plans live in `PLAN.md`; read it before starting work and keep it updated."

#### 1. Docker toolchain (added first, so PHP is available)
- [x] `docker/php/Dockerfile`: `php:8.4-cli` plus `composer:2` (copied from the official image). Extensions: `pdo_sqlite`, `intl`, `zip`, `pcntl` (needed by Pest `--parallel`). Packages: `git`, `unzip`. Build args `UID`/`GID` create a non-root user that matches the host user, so files written into the bind mount belong to the user.
- [x] `docker-compose.yml`: one service `app`:
  - bind mount `.:/app`, working dir `/app`
  - named volume for the Composer cache (`/home/app/.composer`)
  - port `8000:8000` for the workbench
  - default command `composer serve`
- [x] `Makefile` wrappers so day-to-day use needs no PHP:
  - `make build`: build the image
  - `make install`: `composer install`
  - `make composer ARGS=...`: run any Composer command
  - `make test`: `composer test`
  - `make test-unit`: `composer test:unit`
  - `make lint`: `composer lint`
  - `make analyse`: `composer analyse`
  - `make serve`: `composer serve`, workbench on http://localhost:8000
  - `make testbench ARGS=...`: `vendor/bin/testbench ...`, the artisan equivalent for packages
  - `make shell`: open a shell in the container
  - All targets use `docker compose run --rm app ...`.
- [x] ~~`.dockerignore`~~: not needed, because the build context is `docker/php` and doesn't include the repo.

#### 2. Bring in the skeleton
- [x] Shallow-clone the skeleton into the scratchpad (the tarball is missing files). Copy its contents into the repo root, without `.git`.
- [x] Existing files:
  - keep our `LICENSE` and drop the skeleton's `LICENSE.md` (or update composer/README references if any point to it)
  - our `README.md` gets replaced by the skeleton's generated `README_PACKAGE.md`; I'll carry the "Laravel plugin for route-connex" line over as the description

#### 3. Run the configurator in the container (non-interactive)
```
docker compose run --rm app composer install --no-scripts
docker compose run --rm app php configure.php --no-interaction \
  --author-name="Leo Oberle" --author-email="leo@adroitcreations.com" \
  --package-name="route-connex-laravel" --package-name-human="RouteConnex" \
  --vendor-namespace="RouteConnex\\Laravel" --class-name="RouteConnex" \
  --package-description="Laravel plugin for route-connex" \
  --config --routes --views --translations --commands --migrations --facade --assets --boost-skill
```
- [x] First run `php configure.php --help` to confirm the exact flag names, and how the vendor (`route-connex`) and package name are split. Adjust the flags to match.
- [x] Check the JSON output for manual follow-up steps.
- [x] Remove the tool files that aren't wanted: `.github/dependabot.yml`, `.github/ISSUE_TEMPLATE`, `.github/FUNDING.yml`, `.github/SECURITY.md`, `CHANGELOG.md`, `.github/workflows/update-changelog.yml`, `.github/release.yml`.
- [x] Then run `composer update` / `install` so the lock file and autoload match the renamed package.

#### 3b. Lock the Laravel 12 floor
- [x] Check the generated `composer.json`:
  - `illuminate/support` must be `^12.0||^13.0`
  - Testbench, Larastan and pest-plugin-laravel ranges must allow a Laravel 12 install
  - widen any range that doesn't
- [x] Check the skeleton's CI workflow in `.github/workflows/`. Make sure its matrix runs Laravel 12 (Testbench 10) and Laravel 13 (Testbench 11), with `prefer-lowest` and `prefer-stable`.

#### 4. Make the workbench reachable from the host
- [x] Inside the container, `testbench serve` must bind `0.0.0.0`. Adjust the `serve` script in `composer.json` to `@php vendor/bin/testbench serve --host=0.0.0.0 --port=8000`, keeping the skeleton's `@build` prefix. If the skeleton already passes these options, leave it as is.

#### 4b. Multi-database test infrastructure (SQLite, MySQL, PostgreSQL, MongoDB)
The skeleton only covers SQLite: Testbench defaults to in-memory SQLite. This step adds the infrastructure for all four databases. How the migrations themselves handle MongoDB is a design question for later.

- [x] **Image**: install `pdo_mysql`, `pdo_pgsql` and `mongodb` with `mlocati/php-extension-installer`. `pdo_sqlite` comes with the base image; there's no `pecl install` or `libpq-dev`.
- [x] **docker-compose services**, each with a healthcheck and a tmpfs data dir so test runs stay fast and disposable:
  - `mysql:8.4`
  - `postgres:17`
  - `mongo:8`
  - The database services sit under a compose profile (`databases`), so `make test` on SQLite doesn't start them. `app` has no `depends_on`: the Makefile starts each database with `docker compose --profile databases up -d --wait <service>` before its tests run.
- [x] **Composer**:
  - add `mongodb/laravel-mongodb` as `require-dev` and as a `suggest` entry, not a hard `require`, so SQL-only users don't need ext-mongodb
  - confirm which version supports both Laravel 12 and 13 when installing
- [x] **Tests**:
  - `tests/TestCase.php` → `defineEnvironment()` reads `DB_CONNECTION` (default `sqlite`) and configures a connection for it: `sqlite` in-memory, `mysql`, `pgsql`, or `mongodb`, using the compose service hostnames.
  - Register `MongoDB\Laravel\MongoDBServiceProvider` only when the connection is `mongodb`.
  - Load package migrations with `$this->loadMigrationsFrom()` in `tests/Feature/DatabaseTest.php`'s `beforeEach` (Testbench rolls them back after each test).
  - Add one smoke test that runs the placeholder migration and does a simple insert/select, so each driver is checked end to end.
- [x] **Makefile**:
  - `make test` runs on SQLite
  - `make test-mysql`, `make test-pgsql` and `make test-mongodb` run with `DB_CONNECTION=...`
  - `make test-all` runs all four
- [x] **CI**: the existing matrix stays on SQLite, with `mongodb` added to its extensions. A separate `databases` job runs Laravel 12/13 × mysql/pgsql/mongodb on PHP 8.4 with GitHub Actions service containers (see Step 6).
- [ ] **Deferred to the feature work**:
  - ~~how package migrations target MongoDB.~~ Decided after the review (2026-11-03): MongoDB stays supported, but the first stage has no MongoDB migrations. MongoDB doesn't need a schema (collections are created on first write), so the package works there without one. The feature work decides how the SQL migrations skip a `mongodb` connection.
  - which connection the package uses: probably a `connection` key in the package config.

#### 5. Housekeeping
- [x] `.gitignore`: the skeleton ignores `vendor/`, `composer.lock`, `.phpunit.cache`, and `workbench` build artifacts; check those and add any missing entries.
- [x] Add a short "Development with Docker" section to `README.md` listing the `make` targets.
- ~~[ ] Review the whole diff. Don't commit until you've looked at it.~~ Obsolete: the setup was committed in `732637b`.

#### 6. Minimum database versions and CI database fix (from review)
**Status:** done

**Why:**
- The first CI run failed in the `databases` job: `mongo:8` refuses to start on `ubuntu-latest` (Ubuntu 26.04), whose kernel is in the range MongoDB 8.x can't run on (6.19 up to 7.0.13; see [SERVER-121912](https://jira.mongodb.org/browse/SERVER-121912)).
- `fail-fast` then cancelled the other database jobs.
- The package will be installed in elementtime, which runs MongoDB 7.0.40 (`mongo:7.0`) and MySQL 8.0.32 (`mysql/mysql-server:8.0`).
- Another target project runs PostgreSQL 18. The suite was checked locally against `postgres:18` (18.6), and all tests pass.
- The `postgres:18` image moved its data directory and won't start with a mount at `/var/lib/postgresql/data`. Mounting `/var/lib/postgresql` works for all versions (14–17 keep their data in a subfolder).

**Decisions:**
- Minimum supported versions: MySQL 8.0, PostgreSQL 14, MongoDB 7.0.
- Test the minimum and the latest version of each. PostgreSQL also tests 17, so it covers 14, 17 and 18.
- Local default stays on the latest versions.

- [x] `docker-compose.yml`: make the database image tags configurable, with the latest versions as defaults:
  - `mysql:${MYSQL_VERSION:-8.4}`
  - `postgres:${POSTGRES_VERSION:-18}`, with the pgsql tmpfs mount moved from `/var/lib/postgresql/data` to `/var/lib/postgresql`
  - `mongo:${MONGO_VERSION:-8}`

  This allows e.g. `MONGO_VERSION=7.0 make test-mongodb`. Changing a version recreates the container (tmpfs data, so nothing is lost).
- [x] `.github/workflows/tests.yml`, `databases` job:
  - one generic `db` service, with its image, port and health check taken from the matrix, so each job starts only the database it tests
  - matrix: Laravel 12/13 × {mysql 8.0, mysql 8.4, pgsql 14, pgsql 17, pgsql 18, mongodb 7.0, mongodb 8.2}, 14 jobs
  - ~~`runs-on: ubuntu-24.04`~~: didn't help, because the 24.04 runners also have an affected kernel. The job stays on `ubuntu-latest`, and CI tests `mongo:8.2` instead of `mongo:8` (see the notes below)
  - `fail-fast: false`, so one database failing doesn't cancel the others
- [x] `README.md`: add a "Requirements" section:
  - PHP ^8.3
  - Laravel 12 or 13
  - MySQL ≥ 8.0, PostgreSQL ≥ 14, MongoDB ≥ 7.0 (needs `mongodb/laravel-mongodb` ^5.12 and `ext-mongodb`), SQLite
  - mention `MYSQL_VERSION` / `POSTGRES_VERSION` / `MONGO_VERSION` in the Development section
- [x] `AGENTS.md`: note the supported database versions, so that new code isn't checked only against the latest ones.

**Notes from development:**
- PostgreSQL 14–17 images declare a volume at `/var/lib/postgresql/data`. With the tmpfs at `/var/lib/postgresql`, that anonymous volume outlived version switches, so 17 failed on data left behind by 14. Fixed by setting `PGDATA=/var/lib/postgresql/pgdata` (inside the tmpfs) for every version. Switching 14 → 17 → 18 → 14 now works, as do MySQL 8.4 ↔ 8.0 and MongoDB 8 ↔ 7.0.
- `make db-down` now runs `docker compose rm --force --stop --volumes` on the database services. That removes their anonymous volumes too, without touching the `composer-cache` volume (which `down -v` would delete).
- `actionlint` (run via Docker) passes on `.github/workflows/tests.yml`.
- Local results: `make test` passes. All 14 tests pass on mysql 8.0 and 8.4, pgsql 14, 17 and 18, and mongodb 7.0, 8.2 and 8.
- CI run `41566555139`: 36 of 38 jobs passed. Only `mongo:8` failed, with the same kernel guard as before, even on `ubuntu-24.04`.
  - MongoDB 8.0.x and 8.3.x (`mongo:8` = 8.3) refuse to start on kernels 6.19–7.0.13 (SERVER-125742).
  - `mongo:8.2` is reported to start on Ubuntu 26.04 / kernel 7.0 (SERVER-136272).
  - Fix: CI uses `mongo:8.2` on `ubuntu-latest`, with a comment saying to switch back to `mongo:8` once the runner kernel is ≥ 7.0.14.
  - Local defaults stay on `mongo:8`, since the dev machine's kernel (7.2) isn't affected.
  - There is no MongoDB 8.4. The 8.x tags are 8.0, 8.2 and 8.3; 9.0 exists but hasn't been checked with laravel-mongodb 5.12.

**Validate:**
- `make test-all` (latest versions)
- `MYSQL_VERSION=8.0 make test-mysql`, `POSTGRES_VERSION=14 make test-pgsql`, `POSTGRES_VERSION=17 make test-pgsql` and `MONGO_VERSION=7.0 make test-mongodb` (minimum versions)
- after the user pushes, the `databases` CI job is green for all 14 combinations (re-run after the `mongo:8.2` fix). ✅ Run `41567523964` (`270bbc7`): all 38 jobs passed

**Commit:** `Set minimum database versions (MySQL 8.0, PostgreSQL 14, MongoDB 7.0), test PostgreSQL 18, and fix CI database jobs`

#### 7. Skip CI for documentation-only changes (from review)
**Status:** done

**Why:** changes that touch only plan or docs files (e.g. `PLAN.md` status updates, README edits) currently run the full CI, which is 38 jobs.

**Decisions:**
- Use `paths-ignore` on both `push` and `pull_request`. CI is skipped only when **every** changed file matches; mixed commits still run.
- Ignore docs, license and agent files only. Docker/Makefile files aren't ignored for now, even though CI doesn't use them.
- Add `workflow_dispatch` so CI can be started by hand from the Actions tab.
- Nothing on `main` is a required check today. If branch protection adds required checks later, docs-only PRs would wait forever for checks that never start, and an always-passing job would then be needed.

- [x] `.github/workflows/tests.yml`: add the same `paths-ignore` list under `push` and `pull_request`:
  - `'**.md'`: `PLAN.md`, `README.md`, `AGENTS.md`, `.github/CONTRIBUTING.md`, and the `SKILL.md` files under `.agents/` and `resources/boost/`
  - `'LICENSE'`
  - `'CLAUDE.md'` (symlink to `AGENTS.md`)
  - `'.agents/**'`
  - `'.claude'` (symlink to `.agents`)
- [x] `.github/workflows/tests.yml`: add a `workflow_dispatch:` trigger.
- [x] `AGENTS.md`: one line noting that docs-only changes don't run CI, and that new ignore entries must never cover files the tests or build depend on.

**Validate:**
- `actionlint` (via Docker) passes on `.github/workflows/tests.yml`.
- After the user pushes this step: CI **runs**, because the workflow file itself changed.
- Next push that changes only `PLAN.md` (e.g. ticking this step): **no** CI run appears in `gh run list`.
- The "Run workflow" button shows up on the Actions tab (`workflow_dispatch`).

**Commit:** `Skip CI for documentation-only changes`

#### 8. Keep dev tooling out of the Composer dist archive (from Phase 3 review)
**Status:** done

**Why:** Composer installs tagged releases from GitHub's zip archive, which honours `export-ignore` in `.gitattributes`. `git archive HEAD` currently includes `Makefile`, `PLAN.md`, `docker-compose.yml` and `docker/php/Dockerfile`, so every app that installs the package would get them in `vendor/route-connex/route-connex-laravel/`. Review finding R1.

- [x] `.gitattributes`: add `export-ignore` lines for `/docker`, `/docker-compose.yml`, `/Makefile` and `/PLAN.md`, aligned with the existing entries.
- [x] `.gitattributes`: remove the stale `/AGENTS_PACKAGE.md` entry (no such file; it's a skeleton leftover).

Note: `/docker-compose.yml` is 19 characters, so its line has one space before `export-ignore` instead of the two the other entries use. The column is off by one for that line only.

**Validate:**
- `git archive --worktree-attributes HEAD | tar -t | grep -v '/$'` lists only `LICENSE`, `README.md`, `composer.json`, `config/`, `database/`, `lang/`, `public/`, `resources/`, `routes/` and `src/` files.
- `make test` still passes.

**Commit:** `Keep Docker, Makefile and PLAN.md out of the dist archive`

#### 9. Test the facade and the publish tags (from Phase 3 review)
**Status:** done

**Why:** AGENTS.md asks for tests of service provider wiring and published resources. The suite covers the config merge, views, translations, the command and the singleton, but nothing checks the `RouteConnex` facade or any of the six publish tags the README documents. A renamed path or tag would go unnoticed. Review finding R2.

- [x] New `tests/Feature/ServiceProviderTest.php`: the `RouteConnex\Laravel\Facades\RouteConnex` facade resolves to the same `RouteConnex\Laravel\RouteConnex` singleton as the container.
- [x] Publish tags: for each of `route-connex-laravel-config`, `-views`, `-lang`, `-assets` and `-migrations`, assert the source → target mapping (e.g. via `ServiceProvider::pathsToPublish(RouteConnexServiceProvider::class, $tag)`), and that the `route-connex-laravel` tag contains all of them. Use a Pest dataset rather than five copies of the same test.
- [x] One end-to-end check that `vendor:publish --tag=route-connex-laravel-config` writes `config/route-connex-laravel.php` into the Testbench app (clean up the published file afterwards, so tests don't leak files into `vendor/orchestra/testbench-core/laravel`).
- [x] `AGENTS.md` (Task Plan Workflow): suggested commit messages are not hard-wrapped; a line break only where a new line starts. Added at the user's request during this step (outside the original scope).

> Note: the dataset holds closures for the target paths (`config_path()` etc.), because datasets are built before the app boots. The step's tests all passed on the first real run of `make test` (26 tests) and `make test-all`; no package code changed.

**Validate:** `make test` (type coverage stays at 100 %) and `make test-all`.

**Commit:** `Test the facade and the package publish tags`

#### 10. Make the `make` test targets ignore the host's `DB_CONNECTION` and accept `ARGS` (from Phase 3 review)
**Status:** done

**Why:**
- `docker-compose.yml` passes the host's `DB_CONNECTION` into the `app` container (`DB_CONNECTION: ${DB_CONNECTION:-sqlite}`). Checked: `DB_CONNECTION=mysql docker compose run --rm app printenv DB_CONNECTION` prints `mysql`. A Laravel developer with `DB_CONNECTION` exported in their shell (direnv, another project) gets `make test` running Pest `--parallel` against MySQL, which isn't started and would be shared by the parallel workers. Review finding R3.
- `make test-unit` and `make test-<db>` take no arguments, so the `package-testing` skill's `composer test:unit -- --filter ...` advice has no `make` equivalent. Review finding R4.

- [x] `docker-compose.yml`: dropped the `DB_CONNECTION` passthrough from the `app` service (TestCase already defaults to `sqlite`), or pin it to `sqlite`.
- [x] `Makefile`: `test`, `test-unit` and `test-sqlite` always run on SQLite, whatever the host shell has exported; `test-mysql`/`test-pgsql`/`test-mongodb` keep setting it explicitly with `-e`.
- [x] `Makefile`: `test-unit` and the `pest_on` targets pass `$(ARGS)` through, e.g. `make test-unit ARGS="--filter=DatabaseTest"`, `make test-mysql ARGS="--filter=DatabaseTest"`. Update the `help` texts.

**Validate:**
- `DB_CONNECTION=mysql make test` passes on SQLite with the database containers stopped (`make db-down` first).
- `make test-unit ARGS="--filter=DatabaseTest"` runs only the 3 database tests.
- `make test-all` and `MYSQL_VERSION=8.0 make test-mysql ARGS="--filter=DatabaseTest"` pass.

**Commit:** `Pin make test targets to SQLite and pass ARGS through to Pest`

> Note: the passthrough was dropped (TestCase defaults to `sqlite`); `make test` and `make test-unit` also pass `-e DB_CONNECTION=sqlite` explicitly, so they ignore the host variable.

#### 11. Align CONTRIBUTING, README and the agent skills with the Docker workflow (from Phase 3 review)
**Status:** done

**Why:** Review finding R5.
- `.github/CONTRIBUTING.md` tells contributors to run `composer install`, `composer lint` and `composer test`, but the documented setup has no local PHP, and it never mentions the database targets.
- `.agents/skills/package-release/SKILL.md` points to `CHANGELOG.md`, `.github/release.yml` and `.github/workflows/update-changelog.yml`, which Step 3 deleted on purpose.
- `.agents/skills/package-testing/SKILL.md` only mentions `composer` commands.

- [x] `.github/CONTRIBUTING.md`: Setup/Lint/Tests use `make build`, `make install`, `make lint`, `make test`, and `make test-all` (with the minimum-version commands) for database-related changes. Mention that the `composer` scripts work too for contributors with local PHP 8.3+.
- [x] `.agents/skills/package-release/SKILL.md`: drop the references to the deleted changelog files and workflows (or reword them as "if the repo adds them"), and use `make test` / `make test-all` as the release check.
- [x] `.agents/skills/package-testing/SKILL.md`: add the `make` equivalents (`make test-unit ARGS="--filter ..."`, `make test`, `make test-all`) next to the composer commands.
- [x] `README.md` and `AGENTS.md` Quick Commands: mention `ARGS` for `make test-unit` / `make test-<db>` if Step 10 added it.

**Validate:** `make test` (docs-only change, CI will be skipped by `paths-ignore`). Proof-read every command against `Makefile`.

**Commit:** `Document the Docker make workflow in CONTRIBUTING and agent skills`

#### 12. Bring Task 1's steps in line with what was built (from Phase 3 review)
**Status:** done

**Why:** Several Task 1 steps describe something the code doesn't do, without a note. AGENTS.md: "The plan must never describe something the code doesn't do." Review finding R6.

- [x] Step 1 / 4b, image: the Dockerfile installs extensions with `mlocati/php-extension-installer` (`intl zip pcntl bcmath pdo_mysql pdo_pgsql mongodb`); `pdo_sqlite` comes with the base image. There's no `pecl install` or `libpq-dev`.
- [x] Step 4b, compose: `app` has no `depends_on`; the Makefile starts each database with `docker compose --profile databases up -d --wait <service>` instead.
- [x] Step 4b, tests: migrations are loaded with `$this->loadMigrationsFrom()` in `tests/Feature/DatabaseTest.php`'s `beforeEach` (Testbench rolls them back after each test), not `defineDatabaseMigrations()` / `RefreshDatabase`.
- [x] Step 4b, CI: replace the "`db` matrix axis crossed with Laravel 12/13" text with the separate `databases` job (see the progress notes and Step 6).
- [x] Step 5: the "Review the whole diff" box is obsolete (the setup was committed in `732637b`); strike it through with a short note.
- [x] "Expected key files": drop `.dockerignore` (Step 1 decided it isn't needed).

**Validate:** each edited sentence matches the code it describes. `make test` isn't needed (PLAN.md only).

**Commit:** `Align Task 1 plan steps with the implemented setup`

#### 13. Build the test database connections on Laravel's defaults (from Phase 3 review observations)
**Status:** done

**Why:** `tests/TestCase.php` writes the MySQL and PostgreSQL connection arrays by hand, so Laravel's defaults such as `strict`, `engine`, `prefix_indexes` and `sslmode` are missing. It makes no difference today, but it will once real migrations and queries exist, and the tests should run on the same settings a Laravel app uses.

- [x] `tests/TestCase.php` → `defineEnvironment()`: for `sqlite`, `mysql` and `pgsql`, start from Laravel's own connection config (`$app['config']->get("database.connections.{$connection}")`) and override only what the test setup decides: host, port, database, username and password (and `database => ':memory:'` plus `foreign_key_constraints => true` for SQLite). Keep the `DB_*` environment variables and their defaults as they are.
- [x] `mongodb` keeps its hand-written array, since Laravel has no default MongoDB connection.
- [x] No new tests: the existing suite on all four databases is the check.

> Note: the shared lookup is a small `laravelConnection()` helper in `TestCase`; the MySQL/PostgreSQL arrays now override only host, port, database, username and password.

**Validate:** `make test`, `make test-all`, `MYSQL_VERSION=8.0 make test-mysql`, `POSTGRES_VERSION=14 make test-pgsql`, `MONGO_VERSION=7.0 make test-mongodb`.

**Commit:** `Build test database connections on Laravel's default config`

#### 14. Check the Laravel 12 floor on every database in CI (from Phase 3 review observations)
**Status:** todo

**Why:** The `databases` job only runs `--prefer-stable`, so the lowest dependency versions are checked against MySQL, PostgreSQL and MongoDB only locally. Decision: run the full matrix, so the job doubles from 14 to 28 jobs.

- [ ] `.github/workflows/tests.yml`, `databases` job: add `stability: [prefer-lowest, prefer-stable]` to the matrix, use `--${{ matrix.stability }}` in `composer update` (as the `tests` job does), and add the stability to the job name (`DB <image> - L<laravel> - <stability>`).
- [ ] The `tests` job, triggers and `paths-ignore` stay as they are.

**Validate:** `actionlint` through Docker (see the Implementation prompt). After the user pushes, the CI run shows 28 green `databases` jobs; record the run ID here.

**Commit:** `Run the database CI jobs with prefer-lowest and prefer-stable`

#### 15. Link the README credits to the author's GitHub profile (from Phase 3 review observations)
**Status:** todo

**Why:** The skeleton linked "Leo Oberle" to the `route-connex` organisation. Decision: link the personal profile instead.

- [ ] `README.md` Credits: `[Leo Oberle](https://github.com/leocello)`.

**Validate:** the link points to `https://github.com/leocello`; no other file links the author to the organisation (`grep -rn 'github.com/route-connex)' --exclude-dir=vendor .`). `make test` isn't needed (docs only).

**Commit:** `Link the README credits to the author's GitHub profile`

#### 16. Add CHANGELOG.md and make it part of the workflow (from Phase 3 review observations)
**Status:** todo

**Why:** Step 3 deleted the skeleton's `CHANGELOG.md` and its automation. Decision: keep a hand-written changelog instead, filled at each task's close and turned into a release entry when a version is tagged.

**Decisions:**
- One H1 per release, `# vX.Y.Z (YYYY-MM-DD)`, followed by a list of the items done in that release. Releases are separated by a line `---`. Newest first. No file title or intro above the first H1.
- Work that isn't released yet goes under `# Unreleased` at the top. There are no tags yet, so the file starts with `# Unreleased` listing Task 1.
- Each task adds its items under `# Unreleased` in its Phase 4 close commit. At release time, `# Unreleased` is renamed to the version and date, and the tag matches it (`vX.Y.Z`).
- `CHANGELOG.md` ships in the Composer dist archive (no `export-ignore`). CI's `paths-ignore` (`**.md`) already covers it.

- [ ] `CHANGELOG.md` (new): `# Unreleased` with Task 1's items, written for package users and maintainers, one line each: Docker toolchain and `make` targets, package skeleton (config, routes, views, translations, command, migrations, facade, assets, Boost skill), Laravel 12 and 13 support, test setup on SQLite, MySQL 8.0+, PostgreSQL 14+ and MongoDB 7.0+, CI matrix (including Steps 7 and 14), dist archive cleanup, facade and publish tag tests, contributor docs. Check each item against `git log` and the code.
- [ ] `AGENTS.md` → Phase 4: before blanking `PLAN.md`, add the task's items under `# Unreleased` in `CHANGELOG.md` (create that section at the top, followed by `---` when older releases exist), in the same close commit. Describe the format in one short paragraph.
- [ ] `.agents/skills/package-release/SKILL.md`: the release renames `# Unreleased` to `# vX.Y.Z (YYYY-MM-DD)`, the tag is `vX.Y.Z`, and the next task starts a new `# Unreleased` above a `---`. Replace the "if the repo has a changelog" wording with `CHANGELOG.md`, and list it under References.
- [ ] Task 1's own close doesn't add another entry: this step already writes it.

**Validate:** the file follows the format above; every listed item matches the code and `git log`; `.gitattributes` has no `export-ignore` for `CHANGELOG.md`. `make test` isn't needed (docs only).

**Commit:** `Add a hand-written CHANGELOG and fill it at each task's close`

### Expected key files afterwards
- `composer.json` (name `route-connex/route-connex-laravel`, PSR-4 `RouteConnex\Laravel\` → `src/`, provider `RouteConnex\Laravel\RouteConnexServiceProvider`, alias `RouteConnex`)
- `src/RouteConnexServiceProvider.php`, `src/RouteConnex.php`, `src/Facades/RouteConnex.php`, `src/Console/Commands/RouteConnexCommand.php`
- `config/route-connex-laravel.php` (or whatever slug the configurator picks), `routes/`, `resources/views`, `lang/en`, `database/migrations`, `public/`
- `tests/` (Pest + Testbench), `workbench/`, `testbench.yaml`, `phpunit.xml.dist`, `phpstan.neon.dist`, `pint.json`
- `docker/php/Dockerfile`, `docker-compose.yml`, `Makefile`
- `PLAN.md` (shared task plan, referenced from `AGENTS.md`)
- `CHANGELOG.md` (hand-written, one H1 per release; added in Step 16)

### Verification
- [x] 1. `make build` builds the image, and `docker compose run --rm app php -v` shows PHP 8.4.
- [x] 2. `make install` runs with no errors, and `configure.php` is gone.
- [x] 3. `make test` passes: Pint check, PHPStan, type coverage and Pest are all green.
- [x] 4. (workbench only: the placeholder package route is commented out in the skeleton) `make serve`, then `curl http://localhost:8000` returns the workbench welcome page, and a package route from `routes/` answers.
- [x] 5. `make testbench ARGS="package:discover"` lists the provider, and `make testbench ARGS="vendor:publish --tag=route-connex-laravel-config"` (or the tag that `configure.php` generated) publishes the config into the workbench.
- [x] 6. Laravel 12 floor check:
   - `make composer ARGS="require illuminate/support:^12.0 orchestra/testbench:^10.0 --dev -W --no-update"`, then `make composer ARGS="update --prefer-lowest"` and `make test`.
   - Expected: passes on Laravel 12.
   - Afterwards, revert with `git checkout composer.json` and reinstall.
   - The same check also runs in CI through the matrix.
- [x] 7. `make test-all` passes the migration smoke test on sqlite, mysql, pgsql and mongodb.
- [x] 8. `git status` shows file ownership as the host user (no root-owned files).

### Review
Phase 3 review, 2026-11-03, in a session that didn't build Task 1 (`claude --ide --resume ea9d766f-1d8f-4e27-8421-5af3ecd48611`, xps-omarchy). Every step was checked against the files, not the ticked boxes.

**Validation runs (all green):**
- `make test`: PHPStan level 7 no errors, Pint pass (21 files), type coverage 100 %, Pest 14 passed (18 assertions).
- `make test-all`: 14 passed on each of sqlite, mysql 8.4, pgsql 18 and mongodb 8 (`mongo:8`).
- Minimum versions: `MYSQL_VERSION=8.0 make test-mysql`, `POSTGRES_VERSION=14 make test-pgsql` and `MONGO_VERSION=7.0 make test-mongodb`: 14 passed each.
- CI: last full run `41567523964` (`270bbc7`) had all 38 jobs green; Step 7 only changed triggers.

**What checks out:**
- Composer: name, PSR-4 `RouteConnex\Laravel\`, provider and alias auto-discovery, `illuminate/support ^12.0||^13.0`, Testbench `^10||^11`, Larastan/Pest ranges that resolve on both Laravel versions (Pest 5 needs PHP 8.4, so PHP 8.3 CI jobs get Pest 4), `mongodb/laravel-mongodb ^5.12` in `require-dev` + `suggest` (5.12 requires `illuminate/support ^12|^13`). `serve` binds `0.0.0.0:8000` behind `@build`.
- Service provider: config merge, routes, views, translations, publish tags (`route-connex-laravel` plus `-config`, `-views`, `-lang`, `-assets`, `-migrations`) and the command all match the README. Migrations are publish-only (`publishesMigrations`), which matches the README's "publish, then migrate".
- Docker: non-root user from `UID`/`GID`, Composer cache volume, database services with tmpfs + healthchecks under the `databases` profile, version overrides, `PGDATA` fix for 14–18. `make db-down` keeps the Composer cache.
- `tests/TestCase.php`: the connection comes from `DB_CONNECTION` with defaults matching both Compose and CI; the MongoDB provider is only registered for `mongodb`. The database tests run without `--parallel`, as intended.
- CI: L12/L13 × prefer-lowest/stable × PHP 8.3–8.5 × Linux/Windows on SQLite, plus a 14-job `databases` matrix covering the minimum and latest versions. Actions are pinned by SHA, `fail-fast: false` for databases, `paths-ignore` matches AGENTS.md and covers nothing CI depends on.
- README Requirements, the Development section, AGENTS.md supported versions and the CI matrix agree (MongoDB 8.2 vs `mongo:8` locally is documented).

**Findings (each has a proposed step):**
- **R1 — dev tooling ships to users (medium).** `git archive HEAD` includes `Makefile`, `PLAN.md`, `docker-compose.yml` and `docker/php/Dockerfile`; `.gitattributes` has no `export-ignore` for them, and still lists a non-existent `/AGENTS_PACKAGE.md`. → Step 8.
- **R2 — facade and publish tags are untested (medium).** No test resolves the facade or checks any publish tag, though both are documented and AGENTS.md asks for tests of provider wiring and published resources. → Step 9.
- **R3 — `make test` follows the host's `DB_CONNECTION` (low).** Compose passes it through (verified with `printenv`), so an exported `DB_CONNECTION=mysql` makes `make test` run parallel Pest against MySQL. → Step 10.
- **R4 — no way to filter tests through `make` (low).** `make test-unit` / `make test-<db>` take no `ARGS`. → Step 10.
- **R5 — contributor and agent docs describe a different setup (low).** `.github/CONTRIBUTING.md` uses bare `composer` commands; the `package-release` skill references the deleted `CHANGELOG.md`, `.github/release.yml` and `update-changelog.yml`; the `package-testing` skill has no `make` commands. → Step 11.
- **R6 — Task 1's steps don't all match the code (low).** Step 1/4b image details, `depends_on`, the migration-loading approach, the CI `db` axis wording, Step 5's open "review the diff" box and `.dockerignore` in Expected key files. → Step 12.

**Observations, no step proposed at review time.** The user's decisions, 2026-11-03, are noted after each one:
- The `databases` CI job only runs `--prefer-stable`, so the Laravel 12 floor is checked against MySQL/PostgreSQL/MongoDB only locally (progress notes), not in CI. Adding `prefer-lowest` would double it to 28 jobs. → Full matrix: Step 14.
- `TestCase` builds the MySQL/PostgreSQL connection arrays by hand instead of starting from Laravel's defaults, so options such as `strict` and `prefix_indexes` are missing. Today this makes no difference (MySQL 8's default `sql_mode` equals Laravel's strict mode, and there is no prefix), but it may matter once real migrations and queries exist. → Step 13.
- Nothing enforces the documented `mongodb/laravel-mongodb` ^5.12 minimum for users (it's only a `suggest`). A `conflict` with `<5.12` fits better once the package has MongoDB code (already deferred in Step 4b). → Stays deferred: MongoDB is supported, but the first stage has no MongoDB migrations (see Step 4b), so there's no MongoDB code yet.
- README Credits links "Leo Oberle" to `https://github.com/route-connex` (the organisation), as generated by the skeleton. Check whether it should be a personal profile. → `https://github.com/leocello`: Step 15.
- `resources/boost/skills/route-connex-laravel-development/SKILL.md`, the placeholder route, config key, view, translation, command and migration are skeleton placeholders by design; they get replaced by the feature work. → No step.
- This plan predates the current `PLAN.md` format (no Decisions/Acceptance Criteria sections, no Implementation entries for Steps 0–6). Not worth reformatting, since Phase 4 blanks it. → No step.
- Added by the user after the review: keep a hand-written `CHANGELOG.md` (the skeleton's was deleted in Step 3). → Step 16.
