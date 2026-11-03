# RouteConnex

This repository is a Laravel package. Keep the package focused, idiomatic, and easy for Laravel developers to install, test, and maintain.

## Package Conventions

- Use Laravel-native package APIs and the existing service provider shape before adding abstractions.
- Keep package names, namespaces, Composer metadata, publish tags, documentation, and examples aligned with `route-connex/route-connex-laravel`.
- Add only the files and dependencies needed for the package behavior being implemented.
- Prefer explicit Laravel package code over helper abstractions unless the extension point is real.
- Keep tests focused on observable package behavior through public APIs, service provider wiring, commands, routes, published resources, and documentation promises.
- Supported versions: PHP 8.3+, Laravel 12 and 13, MySQL 8.0+, PostgreSQL 14+, MongoDB 7.0+ and SQLite. Database code (especially migrations) must work on every supported database, not only the latest versions. Check the minimum versions with `MYSQL_VERSION=8.0 make test-mysql`, `POSTGRES_VERSION=14 make test-pgsql` and `MONGO_VERSION=7.0 make test-mongodb`. Keep the CI `databases` matrix, `docker-compose.yml` defaults and the README Requirements section in sync when these change.

## Task Plan Workflow

All development is driven by `PLAN.md` at the repo root. It holds the plan for **one task at a time**, and no task starts without it. Read `PLAN.md` and `git log -- PLAN.md` before doing anything else, to see which phase the task is in.

When no task is active, `PLAN.md` is **blank**: it contains only this line:

```
<!-- No active task. See AGENTS.md → Task Plan Workflow. -->
```

Agents never commit, push or merge. The user does all of that. Each phase ends by stopping and waiting for the user.

### Phase 1: Planning (when a task is given)

1. Check that `PLAN.md` is blank. If it holds another task, stop and ask the user how to proceed. Never overwrite an active plan.
2. Don't assume anything. Read the relevant code, config, tests and docs to understand the current state.
3. Ask the user about anything the code doesn't answer: requirements, edge cases, naming, behavior, the task branch name, and the target branch (`main` or a release branch). Keep asking until no open questions remain.
4. Write the plan into `PLAN.md` using the format below. Split the work into **steps that can each be validated and committed on their own**:
   - Each step leaves the package working, with tests passing.
   - Each step is small enough to review in one commit.
   - Each step lists the commands that validate it.
5. Stop. The user reviews the plan, asks for changes if needed, and commits it. Don't start development until the plan is committed. Check with `git log -- PLAN.md` and `git status`.

### Phase 2: Development (after the plan is committed)

1. Work through the steps in order, **one step at a time**.
2. While working on a step, tick its checkboxes (`- [x]`) in `PLAN.md` as each item is done. Add short notes under the step for anything the reviewer should know, such as deviations or surprises.
3. When a step is finished:
   - run its validation commands and report the results honestly
   - set the step to done in `PLAN.md`
   - stop and wait for the user to commit
   - don't start the next step until the previous one is committed
4. If the plan turns out to be wrong or incomplete, stop and ask the user. Change the plan only with the user's agreement, and record the change in `PLAN.md`.

### Phase 3: Review (after all steps are committed)

An agent, preferably one that didn't do the development, validates the whole task:

- Check every step and acceptance criterion against the actual code, not against the ticked boxes.
- Do a full review looking for gaps and errors: missing behavior, edge cases, untested paths, broken publish tags or config, documentation that doesn't match the code, and compatibility with Laravel 12/13 and every supported database.
- Run the full validation: `make test` and `make test-all`.
- Write the findings in the plan's **Review** section. Fixes for any findings get added as new steps, with the user's agreement, and then go through Phase 2.
- Stop and wait for the user to validate.

### Phase 4: Close

Once the user has validated the task, blank `PLAN.md` again (back to the single line above) as the last commit on the task branch. The user then merges the branch into its target branch. The target branch's `PLAN.md` therefore stays blank, and the plan remains in git history.

### `PLAN.md` format

```markdown
# <Task title>

**Status:** planning | approved | in progress | in review | done
**Branch:** <task branch> → **Target:** <main | release branch>

## Context
Why this task exists, what problem it solves, and the intended outcome.

## Decisions
Answers to the questions asked during planning (question → answer).

## Steps

### Step 1: <short title>
**Status:** todo | in progress | done
- [ ] <concrete change, with file paths>
- [ ] <tests added or updated>
**Validate:** `make test` (and any step-specific commands)
**Commit:** `<suggested commit message>`

### Step 2: ...

## Acceptance Criteria
- [ ] <observable behavior that proves the task is done>

## Review
Findings from Phase 3 (filled in by the reviewing agent).
```

## Quick Commands

There is no local PHP: run everything through Docker with `make` (see `make help`). The equivalents are `make test`, `make lint`, `make analyse`, `make test-unit`, `make serve`, and `make composer ARGS="..."`. For database-specific runs, use `make test-mysql`, `make test-pgsql`, `make test-mongodb` or `make test-all`.

Composer scripts (run inside the container):

- Full validation: `composer test`
- Formatting check: `composer lint:check`
- Static analysis: `composer analyse`
- Pest tests: `composer test:unit`
- Workbench build: `composer build`
- Workbench server: `composer serve`

## Local Skills

- `package-scaffold`: use when adding package capabilities or wiring them through the service provider, including commands, migrations, routes, config, views, translations, assets, middleware, publish tags, workbench files, and console-only behavior.
- `package-testing`: use when adding or changing package tests with Pest 4/5 and Orchestra Testbench.
- `package-release`: use when preparing changelog, release notes, tags, or GitHub release workflow changes.
- `package-compatibility`: use when reviewing code, dependencies, or CI against the PHP and Laravel support matrix.
- `package-generate-skill`: use when updating the bundled Boost skill from the package implementation, README, and examples.
