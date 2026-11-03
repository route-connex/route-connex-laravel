# Branching model, branch-aware CHANGELOG and `version`

**Status:** planning
**Branch:** `docs/init` → **Target:** `release/0.x`
**Planned on:** `gandalf`
**Planning session:**

```shell
claude --ide --resume b796e158-cec8-4b7c-8633-a25c4fb63c33
```

## Implementation

- [ ] **1. Document the branching model, the branch-aware CHANGELOG and `version`**
  - **Model:** `sonnet`
  - **Effort:** `high`
  - **Session:** `same`
  - **Resume:** –
  - **Why:** Docs only, but it rewrites parts of the Task Plan Workflow that have to stay consistent with each other. The planning session already has every decision loaded.
  - **Prompt:** Implement Step 1 of PLAN.md: Document the branching model, the branch-aware CHANGELOG and `version`. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md; the rules to write down are in PLAN.md → Decisions. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 2.
- [ ] **2. Test the `version` format**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `new`
  - **Resume:** –
  - **Why:** A small, self-contained Pest test. A fresh session avoids carrying over the long planning context.
  - **Prompt:** Implement Step 2 of PLAN.md: Test the `version` format. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Step 1 added the `version` file at the repo root and documented its allowed values in AGENTS.md (see PLAN.md → Decisions); use the `package-testing` skill. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 3.
- [ ] **3. Run CI on release branches and check `version` on tag pushes**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `same`
  - **Resume:** –
  - **Why:** Small GitHub Actions change. The Step 2 session already knows the `version` rules and the test that enforces its format.
  - **Prompt:** Implement Step 3 of PLAN.md: Run CI on release branches and check `version` on tag pushes. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Step 1 documented the branching model and `version`, and Step 2 added `tests/Unit/VersionFileTest.php` for its format; use the `package-release` and `package-compatibility` skills. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start the review.

## Context

The repo has no written branching model: CONTRIBUTING only says "create a new branch", and the plan format offers `main | release branch` as targets. `CHANGELOG.md` collects unreleased work under `# Unreleased` and only gets items in a task's close commit, so a task branch's changelog doesn't show what that branch did.

This task:

- writes down a branching model: one `release/<major>.x` branch per major, `main` = latest stable tag, and `feature/`, `hotfix/` and `docs/` work branches that start from a release branch and merge back into it
- makes `CHANGELOG.md` branch-aware: on a work branch, its items sit under `# <branch name>` above the target's sections, and they fold into `# Unreleased` when the task closes
- adds a `version` file at the repo root that names the tag (on a tagged commit) or the branch the code is on
- checks the file's format in Pest, and checks it against the tag in CI on tag pushes

## Decisions

- **Release branches:** every major has a long-lived `release/<major>.x` branch (pre-1.0: `release/0.x`). Releases are cut and tagged there.
- **`main`:** always the latest stable release. When a `vX.Y.Z` tag is made on the latest major's release branch, the user fast-forwards `main` to it. So `main` always points at the newest stable tag, and nothing merges into `main` otherwise.
- **No `develop` branch:** the user dropped it ("not necessary").
- **Work branches:** `feature/<origin_major>/<quick-description>`, `hotfix/<origin_major>/<quick-description>` and `docs/<origin_major>/<quick-description>` (e.g. `feature/1/add-route-cache`). `<origin_major>` is the major of the release branch the work branch starts from, written as a bare number (`1`, not `1.x`). It's left out when that is the latest major: `feature/add-route-cache`. A work branch starts from a release branch and merges back into the same one. `<quick-description>` is lowercase kebab-case.
- **Latest major:** the highest-numbered `release/<N>.x`. A new major starts when the user creates `release/<N+1>.x` from `release/<N>.x`. `main` stays on the newest `N` tag until `v<N+1>.0.0` is tagged.
- **Tags:** `v<major>.<minor>.<patch>` (SemVer), made on a release branch. They match the CHANGELOG heading `# vX.Y.Z (YYYY-MM-DD)`.
- **CHANGELOG on a work branch:** the top H1 is the branch name (e.g. `# docs/init`), followed by only that branch's items, then `---` and the sections the branch started from (`# Unreleased`, older releases). Each step's commit adds its own items under `# <branch>`, creating the section on the first item, so the heading always lists what the branch has done so far.
- **CHANGELOG on `main` / `release/<N>.x`:** unreleased work sits under `# Unreleased`, as before.
- **Close commit:** moves the `# <branch>` items under the target's `# Unreleased`, creating it above a `---` if it's missing, removes the `# <branch>` heading and its `---`, sets `version` to the target branch name, and blanks `PLAN.md`.
- **Release:** on `release/<N>.x`, rename `# Unreleased` to `# vX.Y.Z (YYYY-MM-DD)`, set `version` to `vX.Y.Z`, then (with the user's approval) tag `vX.Y.Z`. If `N` is the latest major, fast-forward `main` to it.
- **`version`:** one line (plus a trailing newline) at the repo root, with no extension:
  - on a tagged commit: the tag name, e.g. `v0.1.0`
  - on a work branch: the branch name, e.g. `docs/init`. Phase 1 writes it together with `PLAN.md`, so the plan commit sets it.
  - on an untagged `release/<N>.x` commit (after a close, before the next release): the release branch name, e.g. `release/0.x`
  - For this task it is `docs/init` (written in Step 1, because the Phase 1 rule doesn't exist yet).
- **Dist archive:** `version` stays in the Composer dist archive. Dist archives come from tags, so it holds the tag name there.
- **Automated checks:** a Pest test for the file's format, and a CI check on tag pushes that `version` equals the tag.
- **This task's target:** `release/0.x`. The user creates it from `main` before committing this plan. `main` is updated when `v0.1.0` is tagged.
- **Out of scope:** Composer sees `release/1.x` as `dev-release/1.x`, not `1.x-dev`. A `branch-alias` in `composer.json` can map it later if dev installs of a release branch are wanted. Forward-porting hotfixes to newer majors isn't defined; it's done with a separate branch per major when needed.

## Steps

### Step 1: Document the branching model, the branch-aware CHANGELOG and `version`
**Status:** todo
- [ ] `AGENTS.md`: add a **Branching and Versioning** section (before Task Plan Workflow) with the release branches, `main`, the work branch names with the origin-major rule and examples, where each branch starts and merges, tags, and the `version` values.
- [ ] `AGENTS.md` → Task Plan Workflow:
  - Phase 1: the branch name must follow the scheme, the target is a `release/<N>.x` branch, and the planning agent writes `version` (the branch name) together with `PLAN.md`
  - Phase 2: each step adds its CHANGELOG items under `# <branch>`
  - Phase 3: the review checks the CHANGELOG items and `version`
  - Phase 4: the close folds the items into `# Unreleased` and sets `version` to the target branch
  - the `CHANGELOG.md` paragraph: describe the branch heading
  - the `PLAN.md` format: `**Target:** <release/<N>.x>`
- [ ] `.github/CONTRIBUTING.md`: in the Process section, contributors pick a branch name from the scheme, start from the right `release/<N>.x` and open the PR against it. Mention the `# <branch>` CHANGELOG section and `version`.
- [ ] `.agents/skills/package-release/SKILL.md`: releases happen on `release/<N>.x`. Set `version` to the tag, fast-forward `main` when `N` is the latest major, and include the steps for starting a new major. Update the examples.
- [ ] `version`: create it with `docs/init`.
- [ ] `CHANGELOG.md`: add `# docs/init` with this step's item(s) above a `---` and the existing `# Unreleased`.
**Validate:** `make test`. Check by hand that AGENTS.md, CONTRIBUTING and the skill describe the same rules as PLAN.md → Decisions.
**Commit:** `Document the branching model, branch-aware CHANGELOG and version file`

### Step 2: Test the `version` format
**Status:** todo
- [ ] `tests/Unit/VersionFileTest.php`: `version` exists at the repo root and holds exactly one line (a trailing newline is allowed). That line is one of:
  - a tag: `vX.Y.Z` (`^v\d+\.\d+\.\d+$`)
  - a release branch: `release/<N>.x`
  - a work branch: `feature|hotfix|docs` + optional `/<N>` + `/<kebab-case>`
- [ ] Tests for the regex's edge cases, through a small dataset of valid and invalid values (e.g. `v1.2`, `release/1`, `feature/Foo`, `fix/x`), so the rule is pinned down beyond the file's current value.
- [ ] `CHANGELOG.md`: add this step's item under `# docs/init`.
**Validate:** `make test`, `make test-unit ARGS="--filter=VersionFile"`
**Commit:** `Test the version file format`

### Step 3: Run CI on release branches and check `version` on tag pushes
**Status:** todo
- [ ] `.github/workflows/tests.yml`: change the push `branches` to `main` and `release/*.x` (GitHub's `*` doesn't match `/`, so the current `'*.x'` misses `release/1.x`).
- [ ] New `.github/workflows/version.yml`: on `push` of tags `v*.*.*`, fail unless `version` equals `$GITHUB_REF_NAME` and the first line of `CHANGELOG.md` starts with `# $GITHUB_REF_NAME (`.
- [ ] Mention the tag check in the `package-release` skill and in AGENTS.md → Branching and Versioning.
- [ ] `CHANGELOG.md`: add this step's item under `# docs/init`.
**Validate:** `make test`. Lint both workflows with `docker run --rm -v "$PWD":/repo -w /repo rhysd/actionlint:latest`. Run the check's shell script by hand with `GITHUB_REF_NAME=v0.1.0` (expect a failure on this branch) and against a temp copy with matching files (expect a pass).
**Commit:** `Run CI on release branches and check the version file on tag pushes`

## Acceptance Criteria
- [ ] AGENTS.md documents the release branches, `main` = latest stable tag, the `feature/` `hotfix/` `docs/` names (with the optional origin major), tags `vX.Y.Z`, and no `develop` branch.
- [ ] The Task Plan Workflow writes CHANGELOG items per step under `# <branch>` and folds them into `# Unreleased` at close. CONTRIBUTING and the `package-release` skill agree with it.
- [ ] `version` exists, holds `docs/init` on this branch, and the docs define its value for tags, release branches and work branches.
- [ ] A Pest test fails for a malformed `version`.
- [ ] CI runs on pushes to `main` and `release/*.x`. A tag push fails when `version` or the CHANGELOG heading doesn't match the tag.
- [ ] `make test` and `make test-all` pass.

## Review
