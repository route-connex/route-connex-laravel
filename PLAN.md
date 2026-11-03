# Branching model, branch-aware CHANGELOG and `version`

**Status:** in review
**Branch:** `docs/init` → **Target:** `release/0.x`
**Planned on:** `gandalf`
**Planning session:**

```shell
claude --ide --resume b796e158-cec8-4b7c-8633-a25c4fb63c33
```

## Implementation

- [x] **1. Document the branching model, the branch-aware CHANGELOG and `version`**
  - **Model:** `sonnet`
  - **Effort:** `high`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume b796e158-cec8-4b7c-8633-a25c4fb63c33` (gandalf)
  - **Why:** Docs only, but it rewrites parts of the Task Plan Workflow that have to stay consistent with each other. The planning session already has every decision loaded.
  - **Prompt:** Implement Step 1 of PLAN.md: Document the branching model, the branch-aware CHANGELOG and `version`. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md; the rules to write down are in PLAN.md → Decisions. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 2.
- [x] **2. Test the `version` format**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `new`
  - **Resume:** `claude --ide --resume aafaac91-2d16-4d42-bfcd-901475cfed55` (gandalf)
  - **Why:** A small, self-contained Pest test. A fresh session avoids carrying over the long planning context.
  - **Prompt:** Implement Step 2 of PLAN.md: Test the `version` format. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Step 1 added the `version` file at the repo root and documented its allowed values in AGENTS.md (see PLAN.md → Decisions); use the `package-testing` skill. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 3.
- [x] **3. Run CI on release branches and check `version` on tag pushes**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume aafaac91-2d16-4d42-bfcd-901475cfed55` (gandalf)
  - **Why:** Small GitHub Actions change. The Step 2 session already knows the `version` rules and the test that enforces its format.
  - **Prompt:** Implement Step 3 of PLAN.md: Run CI on release branches and check `version` on tag pushes. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Step 1 documented the branching model and `version`, and Step 2 added `tests/Unit/VersionFileTest.php` for its format; use the `package-release` and `package-compatibility` skills. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start the review.
- [x] **4. Release candidate versions in the docs, and the close against the target's current state**
  - **Model:** `sonnet`
  - **Effort:** `high`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume 71acc65f-b7b0-4ace-a18f-e8798bca5fe8` (gandalf)
  - **Why:** Docs only, but it rewrites the close procedure (with a branch on whether an `-rc` exists) in AGENTS.md, CONTRIBUTING and the release skill, which have to stay consistent. The review session that planned it has every decision loaded.
  - **Prompt:** Implement Step 4 of PLAN.md: Release candidate versions in the docs, and the close against the target's current state. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md; the rules to write down are in PLAN.md → Decisions (the entries marked "changed after review") and the review findings 1–4 it fixes. Steps 1–3 wrote the current branching docs, the `version` test and the tag workflow; this step changes only docs and `CHANGELOG.md`. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 5.
- [x] **5. Accept `-rc` versions in the test and reject `-rc` tags in CI**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `new`
  - **Resume:** `claude --ide --resume cc1eea64-144c-4714-9fa9-1444c578dcbc` (gandalf)
  - **Why:** A small regex and dataset change plus a few lines of shell in the workflow. The rules are fully written in PLAN.md → Decisions, so a fresh session doesn't need the planning context.
  - **Prompt:** Implement Step 5 of PLAN.md: Accept `-rc` versions in the test and reject `-rc` tags in CI. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Step 2 added `tests/Unit/VersionFileTest.php` and Step 3 added `.github/workflows/version.yml`; Step 4 documented the `vX.Y.Z-rc` value and the plain-tag rule in AGENTS.md (see PLAN.md → Decisions). Use the `package-testing` and `package-release` skills. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start the review.

## Context

The repo has no written branching model: CONTRIBUTING only says "create a new branch", and the plan format offers `main | release branch` as targets. `CHANGELOG.md` collects unreleased work under `# Unreleased` and only gets items in a task's close commit, so a task branch's changelog doesn't show what that branch did.

This task:

- writes down a branching model: one `release/<major>.x` branch per major, `main` = latest stable tag, and `feature/`, `hotfix/` and `docs/` work branches that start from a release branch and merge back into it
- makes `CHANGELOG.md` branch-aware: on a work branch, its items sit under `# <branch name>` above the target's sections, and they fold into `# Unreleased` when the task closes
- adds a `version` file at the repo root that names the tag (on a tagged commit) or the branch the code is on
- checks the file's format in Pest, and checks it against the tag in CI on tag pushes

After the review, the user replaced `# Unreleased` and the release branch name with a release candidate version: on a release branch, unreleased work sits under `# vX.Y.Z-rc` and `version` holds the same `vX.Y.Z-rc`. Steps 4 and 5 make that change and fix review findings 1–4.

## Decisions

- **Release branches:** every major has a long-lived `release/<major>.x` branch (pre-1.0: `release/0.x`). Releases are cut and tagged there.
- **`main`:** always the latest stable release. When a `vX.Y.Z` tag is made on the latest major's release branch, the user fast-forwards `main` to it. So `main` always points at the newest stable tag, and nothing merges into `main` otherwise.
- **No `develop` branch:** the user dropped it ("not necessary").
- **Work branches:** `feature/<origin_major>/<quick-description>`, `hotfix/<origin_major>/<quick-description>` and `docs/<origin_major>/<quick-description>` (e.g. `feature/1/add-route-cache`). `<origin_major>` is the major of the release branch the work branch starts from, written as a bare number (`1`, not `1.x`). It's left out when that is the latest major: `feature/add-route-cache`. A work branch starts from a release branch and merges back into the same one. `<quick-description>` is lowercase kebab-case.
- **Latest major:** the highest-numbered `release/<N>.x`. A new major starts when the user creates `release/<N+1>.x` from `release/<N>.x`. `main` stays on the newest `N` tag until `v<N+1>.0.0` is tagged.
- **Tags:** `v<major>.<minor>.<patch>` (SemVer), made on a release branch. They match the CHANGELOG heading `# vX.Y.Z (YYYY-MM-DD)`. *Changed after review:* only plain `vX.Y.Z` tags are allowed. `-rc` versions are never tagged, and the tag workflow fails on any other tag that its `v*.*.*` filter lets through (e.g. `v0.1.0-rc`).
- **CHANGELOG on a work branch:** the top H1 is the branch name (e.g. `# docs/init`), followed by only that branch's items, then `---` and the sections the branch started from (*changed after review:* `# vX.Y.Z-rc` if the target has one, then older releases). Each step's commit adds its own items under `# <branch>`, creating the section on the first item, so the heading always lists what the branch has done so far.
- **CHANGELOG on `release/<N>.x`** (*changed after review*, was `# Unreleased`): unreleased work sits under `# vX.Y.Z-rc`, the version it will be released as, with no date. `# Unreleased` is no longer used anywhere. `main` only holds tagged commits, so its top section is always `# vX.Y.Z (YYYY-MM-DD)`.
- **Close commit** (*changed after review*):
  1. First, the target branch must be in the work branch (`git merge-base --is-ancestor <target> HEAD`). If it isn't, the agent stops and asks the user to merge or rebase the target into the work branch, because agents don't merge. This fixes review finding 1: the close then sees the target's current sections and `version`.
  2. If the first section below `# <branch>` and its `---` is a `# vX.Y.Z-rc`, the branch's items are added to it and `version` is set to that `vX.Y.Z-rc`. No new rc is created.
  3. Otherwise (the target's top section is a released `# vX.Y.Z (YYYY-MM-DD)`, or there is none), the agent asks the user for the version to target, suggesting one from the branch's changes: a patch bump for fixes and docs, a minor bump for new behavior, a major bump for breaking changes. It creates `# vX.Y.Z-rc` with the items above a `---`, and sets `version` to `vX.Y.Z-rc`.
  4. It removes the `# <branch>` heading and its `---`, and blanks `PLAN.md`.
- **Release** (*changed after review*): on `release/<N>.x`, whose `version` is `vX.Y.Z-rc`, rename `# vX.Y.Z-rc` to `# vX.Y.Z (YYYY-MM-DD)`, set `version` to `vX.Y.Z`, then (with the user's approval) tag `vX.Y.Z`. If `N` is the latest major, fast-forward `main` to the tag (`git merge --ff-only vX.Y.Z`, review finding 3). The next close then asks for the next version.
- **`version`:** one line (plus a trailing newline) at the repo root, with no extension:
  - on a tagged commit: the tag name, e.g. `v0.1.0`
  - on a work branch: the branch name, e.g. `docs/init`. Phase 1 writes it together with `PLAN.md`, so the plan commit sets it.
  - on an untagged `release/<N>.x` commit (after a close, before the next release): *changed after review*, was the release branch name. Now the release candidate, e.g. `v0.1.0-rc`, the same string as the top CHANGELOG heading. `release/<N>.x` is no longer a valid value.
  - For this task it is `docs/init` (written in Step 1, because the Phase 1 rule doesn't exist yet).
- **`-rc` format:** exactly `vX.Y.Z-rc`, with no number (`v0.1.0-rc.1`, `v0.1.0-rc1` and `v0.1.0-RC` are invalid). One rc collects every branch merged until the release.
- **This task's rc:** the target has no tag yet, so Step 4 renames the existing `# Unreleased` in `CHANGELOG.md` to `# v0.1.0-rc`. This task's close adds the `# docs/init` items to it and sets `version` to `v0.1.0-rc`.
- **External pull requests** (review finding 2): the maintainer does the close for them (fold the items into the rc, set `version`), on the PR branch before merging.
- **Dist archive:** `version` stays in the Composer dist archive. Dist archives come from tags, so it holds the tag name there.
- **Automated checks:** a Pest test for the file's format, and a CI check on tag pushes that `version` equals the tag (*changed after review:* and that the tag is a plain `vX.Y.Z`).
- **This task's target:** `release/0.x`. The user creates it from `main` before committing this plan. `main` is updated when `v0.1.0` is tagged.
- **Out of scope:** Composer sees `release/1.x` as `dev-release/1.x`, not `1.x-dev`. A `branch-alias` in `composer.json` can map it later if dev installs of a release branch are wanted. Forward-porting hotfixes to newer majors isn't defined; it's done with a separate branch per major when needed.

## Steps

### Step 1: Document the branching model, the branch-aware CHANGELOG and `version`
**Status:** done
- [x] `AGENTS.md`: add a **Branching and Versioning** section (before Task Plan Workflow) with the release branches, `main`, the work branch names with the origin-major rule and examples, where each branch starts and merges, tags, and the `version` values.
- [x] `AGENTS.md` → Task Plan Workflow:
  - Phase 1: the branch name must follow the scheme, the target is a `release/<N>.x` branch, and the planning agent writes `version` (the branch name) together with `PLAN.md`
  - Phase 2: each step adds its CHANGELOG items under `# <branch>`
  - Phase 3: the review checks the CHANGELOG items and `version`
  - Phase 4: the close folds the items into `# Unreleased` and sets `version` to the target branch
  - the `CHANGELOG.md` paragraph: describe the branch heading
  - the `PLAN.md` format: `**Target:** <release/<N>.x>`
- [x] `.github/CONTRIBUTING.md`: in the Process section, contributors pick a branch name from the scheme, start from the right `release/<N>.x` and open the PR against it. Mention the `# <branch>` CHANGELOG section and `version`.
- [x] `.agents/skills/package-release/SKILL.md`: releases happen on `release/<N>.x`. Set `version` to the tag, fast-forward `main` when `N` is the latest major, and include the steps for starting a new major. Update the examples.
- [x] `version`: create it with `docs/init`.
- [x] `CHANGELOG.md`: add `# docs/init` with this step's item(s) above a `---` and the existing `# Unreleased`.
**Notes:**
- `.github/CONTRIBUTING.md` got a new **Branching** section (linked from the Process list) instead of only a change to the Process list, so contributors have the full naming rules in one place. It also tells contributors to put their CHANGELOG items under `# <branch>` and set `version`.
- `AGENTS.md` Phase 1 step 7 now says the plan commit includes `version`. This task's plan commit predates that rule, so Step 1 created `version`.
- `CHANGELOG.md`: `# docs/init` holds one item for this step. Steps 2 and 3 add their own lines.

**Validate:** `make test`. Check by hand that AGENTS.md, CONTRIBUTING and the skill describe the same rules as PLAN.md → Decisions.
**Commit:** `Document the branching model, branch-aware CHANGELOG and version file`

### Step 2: Test the `version` format
**Status:** done
- [x] `tests/Unit/VersionFileTest.php`: `version` exists at the repo root and holds exactly one line (a trailing newline is allowed). That line is one of:
  - a tag: `vX.Y.Z` (`^v\d+\.\d+\.\d+$`)
  - a release branch: `release/<N>.x`
  - a work branch: `feature|hotfix|docs` + optional `/<N>` + `/<kebab-case>`
- [x] Tests for the regex's edge cases, through a small dataset of valid and invalid values (e.g. `v1.2`, `release/1`, `feature/Foo`, `fix/x`), so the rule is pinned down beyond the file's current value.
- [x] `CHANGELOG.md`: add this step's item under `# docs/init`.
**Notes:**
- The regex lives in `VERSION_PATTERN` (a constant in the test file); it uses `\A…\z` so a trailing newline in a value is rejected. The file test strips the single trailing newline itself and requires exactly one.

**Validate:** `make test`, `make test-unit ARGS="--filter=VersionFile"`
**Commit:** `Test the version file format`

### Step 3: Run CI on release branches and check `version` on tag pushes
**Status:** done
- [x] `.github/workflows/tests.yml`: change the push `branches` to `main` and `release/*.x` (GitHub's `*` doesn't match `/`, so the current `'*.x'` misses `release/1.x`).
- [x] New `.github/workflows/version.yml`: on `push` of tags `v*.*.*`, fail unless `version` equals `$GITHUB_REF_NAME` and the first line of `CHANGELOG.md` starts with `# $GITHUB_REF_NAME (`.
- [x] Mention the tag check in the `package-release` skill and in AGENTS.md → Branching and Versioning.
- [x] `CHANGELOG.md`: add this step's item under `# docs/init`.
**Notes:**
- The workflow checks out with the same pinned `actions/checkout` SHA as `tests.yml`. The tag check was run by hand: it fails on this branch (`version` is `docs/init`), passes against a temp copy with `v0.1.0` and a `# v0.1.0 (…)` heading, and fails when the heading is `# Unreleased`.
- The `package-release` skill (`.agents/skills`, also reachable as `.claude/skills`) now names the workflow in step 5 and References; AGENTS.md → Branching and Versioning → Tags mentions the CI trigger and the tag check.

**Validate:** `make test`. Lint both workflows with `docker run --rm -v "$PWD":/repo -w /repo rhysd/actionlint:latest`. Run the check's shell script by hand with `GITHUB_REF_NAME=v0.1.0` (expect a failure on this branch) and against a temp copy with matching files (expect a pass).
**Commit:** `Run CI on release branches and check the version file on tag pushes`

### Step 4: Release candidate versions in the docs, and the close against the target's current state
**Status:** done
- [x] `AGENTS.md` → Branching and Versioning: `version` on an untagged release branch commit is `vX.Y.Z-rc` (the same string as the top CHANGELOG heading), set by the close commit. Tags are plain `vX.Y.Z` only, and `-rc` versions are never tagged.
- [x] `AGENTS.md` → Phase 4: rewrite the close as in PLAN.md → Decisions → Close commit: check that the target is merged into the work branch (otherwise stop and ask the user to merge or rebase it), add the items to an existing `# vX.Y.Z-rc` or ask the user for the version (with a patch/minor/major suggestion) and create one, set `version` to the rc, remove `# <branch>` and its `---`, blank `PLAN.md`.
- [x] `AGENTS.md` → the `CHANGELOG.md` paragraph and its example: `# vX.Y.Z-rc` (no date) replaces `# Unreleased` on release branches. The example shows `# docs/init`, `---`, `# v0.2.0-rc`, `---`, `# v0.1.0 (2026-01-01)`. The release renames the rc heading.
- [x] `.github/CONTRIBUTING.md` → Branching: contributors keep their items under `# <branch>`. The maintainer does the close on the PR branch before merging (folds the items into the rc, sets `version`). Review finding 2.
- [x] `.agents/skills/package-release/SKILL.md`:
  - step 3: say that Composer versions come from the Git tag and the `version` file must match it (step 5). Review finding 4.
  - step 4: rename `# vX.Y.Z-rc` to `# vX.Y.Z (YYYY-MM-DD)`. The release version is the rc's `vX.Y.Z`.
  - step 7: fast-forward `main` with `git merge --ff-only vX.Y.Z`. After the release, the next close asks for the next version. Review finding 3.
  - the example (`v0.1.0` from `release/0.x`, starting from `# v0.1.0-rc`) and an anti-pattern for tagging an `-rc` version.
- [x] `CHANGELOG.md`: rename `# Unreleased` to `# v0.1.0-rc`, and add this step's item under `# docs/init`.
- [x] No `# Unreleased` or release-branch `version` value is left in the docs: `git grep -n -E "Unreleased|version.*release/<N>\.x|release branch name" -- ':!PLAN.md'` finds nothing that describes the old rules.

**Notes:**
- `AGENTS.md` also gained a **Release candidate** bullet in Branching and Versioning (the `vX.Y.Z-rc` format: no number, no date, one rc until the release), and the Phase 4 close is now a numbered list. Phase 2 and Phase 3 needed no change.
- The remaining `Unreleased` hits in `git grep` are the statements that it is no longer used (AGENTS.md lines 26 and 132) and the `CHANGELOG.md` items that say what changed.
- `CHANGELOG.md`: the Step 2 item still says the test accepts a `release/<N>.x` branch, which is true until Step 5 changes the test. Step 5 should update that line when it does.
- Validation: `make test` passes (PHPStan, Pint, 100 % type coverage, 58 tests). The docs-only change doesn't touch the database tests, so `make test-all` wasn't run.

**Validate:** `make test`. Check by hand that AGENTS.md, CONTRIBUTING and the skill describe the same rules as PLAN.md → Decisions, and run the `git grep` above.
**Commit:** `Use release candidate versions on release branches and close against the target's current state`

### Step 5: Accept `-rc` versions in the test and reject `-rc` tags in CI
**Status:** done
- [x] `tests/Unit/VersionFileTest.php`: `VERSION_PATTERN` accepts `vX.Y.Z`, `vX.Y.Z-rc` and work branch names, and no longer accepts `release/<N>.x`.
  - valid dataset: add `v0.1.0-rc` and `v12.34.56-rc`, and remove the two release branch values
  - invalid dataset: add `release/0.x`, `v0.1.0-rc.1`, `v0.1.0-rc1`, `v0.1.0-RC`, `0.1.0-rc`, `v0.1-rc` and `v0.1.0-rc-1`
- [x] `.github/workflows/version.yml`: before the existing checks, fail unless `$GITHUB_REF_NAME` matches `^v[0-9]+\.[0-9]+\.[0-9]+$`, with an `::error::` that says only plain `vX.Y.Z` tags are allowed.
- [x] `AGENTS.md` → Branching and Versioning → Tags and the `package-release` skill (step 5): mention that the workflow rejects other tags.
- [x] `CHANGELOG.md`: add this step's item under `# docs/init`.

**Notes:**
- The CHANGELOG's Step 2 item now says the test accepts a `vX.Y.Z-rc` release candidate instead of a `release/<N>.x` branch, and a new item records the tag workflow check. The `package-release` skill (step 5) also mentions the tag rejection.
- Validation: `make test` passes (65 tests), actionlint is clean, and the workflow's shell script fails for `v0.1.0-rc` and passes for `v0.1.0`. `make test-all` wasn't run (no database code changed).

**Validate:** `make test`, `make test-unit ARGS="--filter=VersionFile"`. Lint the workflows with `docker run --rm -v "$PWD":/repo -w /repo rhysd/actionlint:latest`. Run the check's shell script by hand against a temp copy: `GITHUB_REF_NAME=v0.1.0-rc` with `version` = `v0.1.0-rc` and a `# v0.1.0-rc` heading (expect a failure on the tag format), and `GITHUB_REF_NAME=v0.1.0` with `version` = `v0.1.0` and a `# v0.1.0 (…)` heading (expect a pass).
**Commit:** `Accept release candidate versions in the version test and reject rc tags in CI`

## Acceptance Criteria
- [x] AGENTS.md documents the release branches, `main` = latest stable tag, the `feature/` `hotfix/` `docs/` names (with the optional origin major), tags `vX.Y.Z`, and no `develop` branch.
- [x] The Task Plan Workflow writes CHANGELOG items per step under `# <branch>` and, at close, folds them into the target's `# vX.Y.Z-rc` (creating it with a user-chosen version when there is none). CONTRIBUTING and the `package-release` skill agree with it, and `# Unreleased` is no longer used.
- [x] `version` exists, holds `docs/init` on this branch, and the docs define its value for tags (`vX.Y.Z`), untagged release branch commits (`vX.Y.Z-rc`) and work branches.
- [x] The close checks that the target is merged into the work branch before it runs (review finding 1).
- [x] A Pest test fails for a malformed `version`, including `release/<N>.x` and numbered or uppercase `-rc` values.
- [x] CI runs on pushes to `main` and `release/*.x`. A tag push fails when the tag isn't a plain `vX.Y.Z`, or when `version` or the CHANGELOG heading doesn't match the tag.
- [x] `make test` and `make test-all` pass.

## Review

Reviewed on `gandalf` against commits `317f000`, `7a77b28` and `eb7b91b`, checking the code rather than the ticked boxes.

**Result:** every step and acceptance criterion is met. AGENTS.md, CONTRIBUTING, the `package-release` skill and PLAN.md → Decisions describe the same rules. `CHANGELOG.md` has one item per step under `# docs/init`, and `version` holds `docs/init`.

**Validation:**
- `make test`: PHPStan, Pint and 100 % type coverage pass, and Pest runs 58 tests, all passing.
- `make test-all`: 58 tests pass on SQLite, MySQL, PostgreSQL and MongoDB.
- Not re-run: actionlint (the tool was denied during the review; Step 3 records a clean run) and the tag check's shell script (Step 3 records a manual run). The `version.yml` script was read: `"# $GITHUB_REF_NAME ("*` is matched literally, so the dots in the tag can't act as wildcards.

**Findings:**

1. **A branch closed after a release silently loses its `version` and CHANGELOG changes (gap, medium).** A work branch forks from `release/0.x` while `version` is `release/0.x`. Then `v0.1.0` is released (`version` becomes `v0.1.0`, `# Unreleased` becomes `# v0.1.0 (…)`), and only after that does the branch close and merge. Its close commit sets `version` back to `release/0.x`, so the branch's side shows no change and git keeps `v0.1.0` on an untagged commit. The Pest test still passes, because `v0.1.0` is a valid value. The branch's items are appended under its old `# Unreleased` lines, far from the renamed heading, so they merge without a conflict into the **released** `# v0.1.0` section. Fix: Phase 4 (and CONTRIBUTING) should first bring the target branch into the work branch (merge or rebase) before folding the CHANGELOG items and setting `version`, so the close works against the target's current sections.
2. **External pull requests have no close step (gap, low).** CONTRIBUTING tells contributors to set `version` to their branch name and says the maintainer folds the CHANGELOG items. It doesn't say who sets `version` back to the release branch. Merging the PR as it is leaves `version = feature/x` on `release/<N>.x`, and the Pest test accepts it. Fix: say that the maintainer does the Phase 4 close (fold the items, reset `version`) on the PR branch, or in a follow-up commit, before or right after merging.
3. **The `main` fast-forward targets the branch, not the tag (inconsistency, low).** AGENTS.md says `main` is fast-forwarded to the tag, but `package-release` step 7 drafts `git merge --ff-only release/<N>.x`. They are the same commit only while nothing has landed on the release branch since the tag. `git merge --ff-only vX.Y.Z` always matches the rule "`main` = latest stable tag".
4. **`package-release` step 3 is out of date (docs, low).** It still says "many Laravel packages rely on Git tags rather than a hardcoded package version", but step 5 now requires the `version` file to equal the tag. Reword step 3 to point to step 5, e.g. "Composer versions come from the Git tag; the `version` file must match it (step 5)."
5. **The tag check tolerates extra blank lines in `version` (nit).** `$(cat version)` strips every trailing newline, so `v0.1.0` followed by blank lines passes `version.yml`. The Pest test rejects that, and it runs on the release commit's push to `release/*.x`, so it's caught before tagging. No change needed unless the workflow should be strict without relying on the Pest test.

**Observations, no action:**
- `v*.*.*` also matches pre-release tags like `v1.0.0-beta`. Their `version` value would fail the Pest regex, which is consistent with the decision to use plain `vX.Y.Z` tags only.
- Pushes to work branches don't run CI; only pull requests do. That was already the case and it's unchanged.
- The README's Tests badge (`branch=main`) now shows the status of the latest release, not the latest work, because `main` only moves on releases.

**Follow-up:** after the review, the user replaced `# Unreleased` and the release branch `version` value with a `vX.Y.Z-rc` release candidate (see Decisions, "changed after review"). Steps 4 and 5 implement that and fix findings 1–4. Finding 5 needs no change: Step 5's tag format check doesn't change it, and the Pest test still catches extra blank lines. After Step 5, the task goes through a new Phase 3 review in a fresh session.

### Second review (after Steps 4 and 5)

Reviewed on `gandalf` against commits `34db36b` and `4fdcd75`, checking the code rather than the ticked boxes.

**Result:** Steps 4 and 5 are implemented as planned, and review findings 1–4 are fixed. Every acceptance criterion is met. `CHANGELOG.md` has an item for each step under `# docs/init` above `# v0.1.0-rc`, `version` holds `docs/init`, and `git merge-base --is-ancestor release/0.x HEAD` succeeds, so the close can run on this branch as it stands.

**Validation:**
- `make test`: PHPStan, Pint and 100 % type coverage pass, and Pest runs 65 tests, all passing.
- `make test-all`: 65 tests pass on SQLite, MySQL, PostgreSQL and MongoDB.
- actionlint: clean.
- The `version.yml` script, run by hand: it fails for tag `v0.1.0-rc` (with matching `version` and heading), passes for `v0.1.0` with `version` = `v0.1.0` and `# v0.1.0 (2026-11-04)`, and fails when `version` is `v0.1.0-rc`, when the heading is `# v0.1.0-rc`, or when `version` is `docs/init`.

**Findings:**

6. **`package-release` step 8 still fast-forwards `main` to the branch (inconsistency, low).** It says that once `v<N+1>.0.0` is tagged, `main` "is fast-forwarded to `release/<N+1>.x`". Finding 3 fixed step 7, but this line was missed. It should say `git merge --ff-only v<N+1>.0.0`, to match AGENTS.md ("fast-forwards `main` to it", meaning the tag) and step 7.
7. **The close can suggest a version the release branch can't release (gap, low).** Phase 4 step 2 suggests "the next major for breaking changes". But a close on `release/<N>.x` can only feed a `v<N>.*` rc: a new major needs a new `release/<N+1>.x`, which the user creates. So on `release/0.x` a breaking change would get a `v1.0.0-rc`. Fix: the rc on `release/<N>.x` is always `v<N>.Y.Z-rc`. Before 1.0 (SemVer 0.x), breaking changes suggest the next minor. From 1.0, the agent stops and asks the user whether to start `release/<N+1>.x`, rather than suggesting `v<N+1>.0.0-rc` on `release/<N>.x`.
8. **Some CHANGELOG items describe this branch's intermediate states (nit).** At the close, these items become `v0.1.0-rc` lines for package users. But "`version` holds the same `vX.Y.Z-rc` instead of the release branch name" and "`main` is fast-forwarded to the tag, not to the release branch" compare against rules that only existed inside `docs/init` and were never released. The Step 1 item also still says `version` names "the tag or branch". Fix: reword the items to describe the end result only. "Replacing `# Unreleased`" can stay, because `# Unreleased` existed before this branch.

**Observations, no action:**
- `release/0.x` exists locally (at `16c8a5b`, the same commit as `main`) but not on `origin`, which only has `main` and `docs/init`. Push it before opening or merging the PR into it. The repo's default branch is still `main`, so pull requests open against `main` unless contributors pick the release branch.
- Phase 4 step 1 checks against the local `<target>`. If merges into the target happen on GitHub, run `git fetch` first and check `origin/<target>`, or the check can pass on a stale local branch.
- `main` currently holds `# Unreleased` and no `version`, because nothing is tagged yet. This is expected until `v0.1.0` is released and `main` is fast-forwarded to it.
- Once `release/<N+1>.x` exists, `v<N>` hotfix tags don't move `main` (`N` is no longer the latest major), so `main` lags the newest stable tag until `v<N+1>.0.0`. This is what keeps the later fast-forward possible, so it's consistent, but "always the latest stable release" doesn't hold strictly in that window.
