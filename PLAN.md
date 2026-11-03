# Task 1: Basic package setup (Docker + skeleton + multi-DB tests)

**Status:** in progress (step 7 added from review; waiting for the user to approve and commit the plan)
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
- [ ] **Review (Phase 3)**
  - **Model:** `opus`
  - **Effort:** `high`
  - **Session:** `new`
  - **Resume:** –
  - **Why:** A full review across Docker, the skeleton, the multi-database tests and CI needs broad judgement. Reviews are always a fresh session that didn't build Task 1.
  - **Prompt:** Do the Phase 3 review of Task 1 in PLAN.md. Follow AGENTS.md (Task Plan Workflow → Phase 3). Before starting, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Check every step against the actual code, not the ticked boxes. Look for gaps and errors in the Docker setup, Makefile, composer.json, the service provider and skeleton wiring, tests/TestCase.php and the database tests, the CI workflow, and whether the docs match the code. Run `make test` and `make test-all`, plus the minimum database versions (`MYSQL_VERSION=8.0 make test-mysql`, `POSTGRES_VERSION=14 make test-pgsql`, `MONGO_VERSION=7.0 make test-mongodb`). Write the findings in a **Review** section of PLAN.md, propose fixes as new steps (each with an Implementation entry) without implementing them, set this entry's Done to [x], then stop and give a suggested commit message.

> **Progress notes**
> - The host's Docker networking was fixed outside the repo: NetworkManager no longer manages `docker0`/`br-*`/`veth*` (`/etc/NetworkManager/conf.d/99-docker-unmanaged.conf`). No compose overrides are needed.
> - The skeleton was taken from commit `064b57f` with `git clone`; the GitHub tarball leaves out tests/, workbench/ and dotfiles (`export-ignore`). It already ships `^12.0||^13.0`, Testbench `^10||^11`, and a CI matrix for L12/L13 × prefer-lowest/stable.
> - `configure.php` builds the namespace as `<vendor-namespace>\<class-name>`, and the vendor part can't contain `\`. It was run with `RouteConnex`/`RouteConnex`, and the namespace was then renamed to `RouteConnex\Laravel` by hand.
> - CI: the existing matrix (OS × PHP × L12/13 × lowest/stable) stays on SQLite, with `mongodb` added to its extensions. A separate `databases` job runs L12/L13 × mysql/pgsql/mongodb on PHP 8.4 with service containers. That's 6 extra jobs instead of multiplying the matrix by 4.
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

- [x] **Image**: add `pdo_mysql`, `pdo_pgsql` (plus the `libpq-dev` build dependency) and `pecl install mongodb`.
- [x] **docker-compose services**, each with a healthcheck and a tmpfs data dir so test runs stay fast and disposable:
  - `mysql:8.4`
  - `postgres:17`
  - `mongo:8`
  - `app` gets `depends_on` for them with `condition: service_healthy`, under a compose profile so `make test` on SQLite doesn't start them.
- [x] **Composer**:
  - add `mongodb/laravel-mongodb` as `require-dev` and as a `suggest` entry, not a hard `require`, so SQL-only users don't need ext-mongodb
  - confirm which version supports both Laravel 12 and 13 when installing
- [x] **Tests**:
  - `tests/TestCase.php` → `defineEnvironment()` reads `DB_CONNECTION` (default `sqlite`) and configures a connection for it: `sqlite` in-memory, `mysql`, `pgsql`, or `mongodb`, using the compose service hostnames.
  - Register `MongoDB\Laravel\MongoDBServiceProvider` only when the connection is `mongodb`.
  - Load package migrations with `defineDatabaseMigrations()` / `RefreshDatabase`.
  - Add one smoke test that runs the placeholder migration and does a simple insert/select, so each driver is checked end to end.
- [x] **Makefile**:
  - `make test` runs on SQLite
  - `make test-mysql`, `make test-pgsql` and `make test-mongodb` run with `DB_CONNECTION=...`
  - `make test-all` runs all four
- [x] **CI**: add a `db` matrix axis (`sqlite`, `mysql`, `pgsql`, `mongodb`) with GitHub Actions service containers. Crossed with the Laravel 12/13 axis.
- [ ] **Deferred to the feature work**:
  - how package migrations target MongoDB. The laravel-mongodb `Blueprint` supports collections and indexes only, so migrations will probably need a `if ($connection->getDriverName() === 'mongodb')` branch or separate migration files.
  - which connection the package uses: probably a `connection` key in the package config.

#### 5. Housekeeping
- [x] `.gitignore`: the skeleton ignores `vendor/`, `composer.lock`, `.phpunit.cache`, and `workbench` build artifacts; check those and add any missing entries.
- [x] Add a short "Development with Docker" section to `README.md` listing the `make` targets.
- [ ] Review the whole diff. Don't commit until you've looked at it.

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

### Expected key files afterwards
- `composer.json` (name `route-connex/route-connex-laravel`, PSR-4 `RouteConnex\Laravel\` → `src/`, provider `RouteConnex\Laravel\RouteConnexServiceProvider`, alias `RouteConnex`)
- `src/RouteConnexServiceProvider.php`, `src/RouteConnex.php`, `src/Facades/RouteConnex.php`, `src/Console/Commands/RouteConnexCommand.php`
- `config/route-connex-laravel.php` (or whatever slug the configurator picks), `routes/`, `resources/views`, `lang/en`, `database/migrations`, `public/`
- `tests/` (Pest + Testbench), `workbench/`, `testbench.yaml`, `phpunit.xml.dist`, `phpstan.neon.dist`, `pint.json`
- `docker/php/Dockerfile`, `docker-compose.yml`, `Makefile`, `.dockerignore`
- `PLAN.md` (shared task plan, referenced from `AGENTS.md`)

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
