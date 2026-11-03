# RouteConnex

This repository is a Laravel package. Keep the package focused, idiomatic, and easy for Laravel developers to install, test, and maintain.

## Package Conventions

- Use Laravel-native package APIs and the existing service provider shape before adding abstractions.
- Keep package names, namespaces, Composer metadata, publish tags, documentation, and examples aligned with `route-connex/route-connex-laravel`.
- Add only the files and dependencies needed for the package behavior being implemented.
- Prefer explicit Laravel package code over helper abstractions unless the extension point is real.
- Keep tests focused on observable package behavior through public APIs, service provider wiring, commands, routes, published resources, and documentation promises.
- Supported versions: PHP 8.3+, Laravel 12 and 13, MySQL 8.0+, PostgreSQL 14+, MongoDB 7.0+ and SQLite. Database code (especially migrations) must work on every supported database, not only the latest versions. Check the minimum versions with `MYSQL_VERSION=8.0 make test-mysql`, `POSTGRES_VERSION=14 make test-pgsql` and `MONGO_VERSION=7.0 make test-mongodb`. Keep the CI `databases` matrix, `docker-compose.yml` defaults and the README Requirements section in sync when these change.
- CI skips docs-only changes (`paths-ignore` in `.github/workflows/tests.yml`: `**.md`, `LICENSE`, `CLAUDE.md`, `.agents/**`, `.claude`). Never add an ignore entry that covers a file the tests or build depend on.

## Branching and Versioning

- **`release/<major>.x`**: one long-lived branch per major version (`release/0.x` until 1.0). Releases are cut and tagged on it.
- **`main`**: always the latest stable release. Nothing is merged into it directly: when a tag is made on the latest major's release branch, the user fast-forwards `main` to it. The latest major is the highest-numbered `release/<N>.x`. A new major starts when the user creates `release/<N+1>.x` from `release/<N>.x`, and `main` stays on the old major until `v<N+1>.0.0` is tagged.
- **Work branches** start from a release branch and are merged back into that same branch. Their names are `<type>/<origin_major>/<quick-description>`, where:
  - `<type>` is `feature` (new behavior), `hotfix` (a fix) or `docs` (documentation only)
  - `<origin_major>` is the major of the release branch the work starts from, as a bare number (`1`, not `1.x`). It's left out when that is the latest major.
  - `<quick-description>` is lowercase kebab-case
  - examples, with `release/2.x` the latest: `feature/add-route-cache` (from `release/2.x`), `hotfix/1/fix-cache-key` (from `release/1.x`), `docs/init` (from `release/2.x`)
- There is no `develop` branch: unreleased work lives on the release branch.
- **Tags**: every release is tagged `v<major>.<minor>.<patch>` (SemVer) on its release branch, matching the CHANGELOG heading `# vX.Y.Z (YYYY-MM-DD)`. CI runs on pushes to `main` and `release/*.x`, and `.github/workflows/version.yml` fails a tag push unless `version` equals the tag and the first line of `CHANGELOG.md` starts with `# <tag> (`.
- **`version`** (a file at the repo root, one line) names where the code is:
  - on a tagged commit: the tag name, e.g. `v0.1.0`
  - on a work branch: the branch name, e.g. `docs/init`. The plan commit (Phase 1) sets it.
  - on an untagged release branch commit (after a task closes, before the next release): the release branch name, e.g. `release/0.x`. The close commit (Phase 4) sets it.
  - The release commit sets it to the tag (see the `package-release` skill). It stays in the Composer dist archive, which is built from a tag.

## Task Plan Workflow

All development is driven by `PLAN.md` at the repo root. It holds the plan for **one task at a time**, and no task starts without it. Read `PLAN.md` and `git log -- PLAN.md` before doing anything else, to see which phase the task is in.

When no task is active, `PLAN.md` is **blank**: it contains only this line:

```
<!-- No active task. See AGENTS.md → Task Plan Workflow. -->
```

Agents never commit, push or merge. The user does all of that. Each phase ends by stopping and waiting for the user.

Whenever an agent stops for the user to commit (a plan, a step, a review, or the close), it ends its message with a **suggested commit message** in a code block:
- a subject line in the imperative mood, at most ~72 characters
- a short body listing what changed and why
- no hard wrapping: put a line break only where a new line starts (subject, blank line, each list item), never in the middle of a sentence

The message must describe what was actually changed, not only what the plan predicted.

### Phase 1: Planning (when a task is given)

1. Check that `PLAN.md` is blank. If it holds another task, stop and ask the user how to proceed. Never overwrite an active plan.
2. Don't assume anything. Read the relevant code, config, tests and docs to understand the current state.
3. Ask the user about anything the code doesn't answer: requirements, edge cases, naming, behavior, the task branch name (it must follow the work branch scheme in Branching and Versioning), and the target branch (the `release/<N>.x` branch it starts from and merges into). Keep asking until no open questions remain.
4. Write the plan into `PLAN.md` using the format below. Split the work into **steps that can each be validated and committed on their own**:
   - Each step leaves the package working, with tests passing.
   - Each step is small enough to review in one commit.
   - Each step lists the commands that validate it.
5. At the top of the plan, before the Implementation list, record where the plan was created, so the user can return to the planning session:
   - **Computer:** the output of `hostname`.
   - **Planning session:** the command to resume it, `claude --ide --resume <session-id>`, run from the repo root on that computer. The session ID is shown by `/status`. It is also the name of the newest `*.jsonl` file in `~/.claude/projects/<project-folder>/`, where the folder is the repo path with `/` replaced by `-`. If unsure, ask the user.
6. Fill the **Implementation** list: one entry per step, with the model, effort level and prompt for the agent that will implement it.
   - **Done:** the checkbox in front of the step's title, starts as `[ ]`. The implementing agent changes it to `[x]` when it finishes the step (see Phase 2).
   - **Resume:** starts as `–`. The implementing agent fills it with `claude --ide --resume <session-id>` (_hostname_) for its own session **as soon as it starts the step**: the command, followed by the name of the computer the session runs on (from `hostname`), e.g. `claude --ide --resume 1e90e112-…` (xps-omarchy). Sessions only exist on the computer that started them, which is why the name is needed., before changing any code (find the ID as for the planning session). That way an unfinished step can be picked up again in the same session.
   - **Model:** use a Claude Code model alias, e.g. `haiku`, `sonnet` or `opus`. Pick the cheapest model that will do the step reliably:
     - `haiku` for mechanical edits (renames, config values, docs)
     - `sonnet` for typical feature work with tests
     - `opus` for steps that need design judgement, cross-cutting changes, tricky database or compatibility work, or debugging
   - **Effort:** `low`, `medium`, `high` or `xhigh`, matching how much reasoning the step needs. Recommend `max` only for steps that are unusually hard and high-risk.
   - **Session:** `same` or `new`, telling the user whether to continue in the current Claude Code session or start a fresh one. For Step 1, the current session is the one used for planning; for later steps, it's the one that implemented the previous step.
     - `same` when the step builds directly on context from that session (decisions just made, code just written in the same area) and the session isn't already long. If the model or effort differs, the user switches with `/model` in the same session.
     - `new` when the step is independent, when that session's context is long or about unrelated work, or when a fresh look matters. The Phase 3 review is always `new`.
   - **Why:** one short reason for the model, effort and session choice.
   - **Prompt:** a self-contained prompt the user can paste into a fresh session. It must:
     - name the step (`Implement Step N of PLAN.md: <title>`)
     - point to `AGENTS.md` and the step's section in `PLAN.md` instead of repeating them
     - mention any context the step depends on (e.g. code added by an earlier step)
     - restate the rules: tick boxes as you go, keep the plan aligned with what was implemented, ask before unplanned changes, fill the step's **Resume** command in the Implementation list before starting, run the step's validation, tick the step's **Done** box, stop and suggest a commit message, don't start the next step
7. Write the task branch name into `version`, then stop. The user reviews the plan, asks for changes if needed, and commits it together with `version`. Don't start development until the plan is committed. Check with `git log -- PLAN.md` and `git status`.

### Phase 2: Development (after the plan is committed)

1. Work through the steps in order, **one step at a time**.
2. When starting a step, before changing any code, fill its **Resume** item in the Implementation list with `claude --ide --resume <session-id>` (_hostname_) for the current session.
   - If the session is `same` as the previous step, that's the same ID as the previous entry.
   - If an unfinished step is continued in a different session later, add the new command next to the old one instead of replacing it.
3. While working on a step, tick its checkboxes (`- [x]`) in `PLAN.md` as each item is done. Add short notes under the step for anything the reviewer should know, such as deviations or surprises.
4. When a step is finished:
   - add the step's items to `CHANGELOG.md` under the `# <task branch>` heading (see the `CHANGELOG.md` paragraph in Phase 4)
   - run its validation commands and report the results honestly
   - set the step to done in `PLAN.md`, and tick its **Done** box (`[x]`) in the Implementation list. This happens before the user validates the step; it's fine, because nothing is committed until the user has validated it.
   - stop, give the suggested commit message (start from the step's **Commit** line and adjust it to what was actually done), and wait for the user to commit
   - don't start the next step until the previous one is committed
5. Keep the plan aligned with the implementation:
   - **The work differs from what the plan says** (a different file, approach, command or detail, within the step's scope): update the step in `PLAN.md` so it describes what was actually implemented, and add a short note under the step saying what changed and why. The plan must never describe something the code doesn't do.
   - **A change isn't covered by the plan at all** (extra behavior, files, dependencies, refactors, or anything outside the step's scope): stop and ask the user before making it. If the user agrees, add it to the plan (to the current step, or as a new step with an Implementation entry), then implement it.
   - **The plan turns out to be wrong or incomplete in a way that affects other steps:** stop and ask the user before continuing.

### Phase 3: Review (after all steps are committed)

An agent, preferably one that didn't do the development, validates the whole task:

- Check every step and acceptance criterion against the actual code, not against the ticked boxes.
- Do a full review looking for gaps and errors: missing behavior, edge cases, untested paths, broken publish tags or config, documentation that doesn't match the code, and compatibility with Laravel 12/13 and every supported database.
- Check that `CHANGELOG.md` lists what each step did under `# <task branch>`, and that `version` holds the task branch name.
- Run the full validation: `make test` and `make test-all`.
- Write the findings in the plan's **Review** section. Fixes for any findings get added as new steps, with the user's agreement, and then go through Phase 2.
  - New steps also get an entry in the **Implementation** list.
- Stop, give the suggested commit message for the review, and wait for the user to validate.

### Phase 4: Close

Once the user has validated the task, fold its `CHANGELOG.md` items into the target branch's `# Unreleased` section (create it above a `---` if it's missing) and delete the `# <task branch>` heading and its `---`. Set `version` to the target branch name (e.g. `release/0.x`), then blank `PLAN.md` again (back to the single line above). All of this goes in the same last commit on the task branch. Give the suggested commit message (e.g. `Close task: <task title>`). The user then merges the branch into its target branch. The target branch's `PLAN.md` therefore stays blank, and the plan remains in git history.

`CHANGELOG.md` is hand-written, newest release first, with no title above the first H1. Each release is one H1, `# vX.Y.Z (YYYY-MM-DD)`, followed by one line per item, and sections are separated by a line `---`. The top section depends on the branch:

- On `main` and `release/<N>.x`, work that isn't released yet goes under `# Unreleased`.
- On a work branch, the top H1 is the branch name instead (e.g. `# docs/init`) and lists only what was done in that branch. Below it come a `---` and the sections the branch started from (`# Unreleased` and older releases). Each step's commit adds its items there, creating the section with the first one. The close commit folds them into `# Unreleased`, so a merged branch never leaves its name behind.

Items are one line each, written for package users and maintainers. The release renames `# Unreleased` to the version and date (see the `package-release` skill).

```markdown
# docs/init

- Branching model documented.

---

# Unreleased

- Docker toolchain for PHP 8.4.

---

# v0.1.0 (2026-01-01)

- First release.
```

### `PLAN.md` format

````markdown
# <Task title>

**Status:** planning | approved | in progress | in review | done
**Branch:** <task branch> → **Target:** <release/<N>.x>
**Planned on:** `<hostname>`
**Planning session:**

```shell
claude --ide --resume <session-id>
```

## Implementation

- [ ] **1. <title>**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `same`
  - **Resume:** –
  - **Why:** <short reason>
  - **Prompt:** Implement Step 1 of PLAN.md: <title>. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. <any context it depends on>. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 2.
- [ ] **2. ...**
  - **Model:** …
  - **Effort:** …
  - **Session:** …
  - **Resume:** –
  - **Why:** …
  - **Prompt:** …

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
````

## Quick Commands

There is no local PHP: run everything through Docker with `make` (see `make help`). The equivalents are `make test`, `make lint`, `make analyse`, `make test-unit ARGS="--filter=..."`, `make serve`, and `make composer ARGS="..."`. For database-specific runs, use `make test-mysql`, `make test-pgsql`, `make test-mongodb` or `make test-all`; the single-database targets accept `ARGS` too.

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
