# Shorter CHANGELOG items

**Status:** planning **Branch:** `docs/shorter-changelog-items` → **Target:** `release/0.x`
**Planned on:** `gandalf`
**Planning session:**

```shell
claude --ide --resume 301035d8-61c2-4388-b85e-f83b2e1c0f59
```

## Implementation

- [x] **1. Shorten the `v0.1.0-rc` items and add the item rules**
  - **Model:** `haiku`
  - **Effort:** `low`
  - **Session:** `new`
  - **Resume:** `claude --ide --resume 0fc88acb-3af9-4ed7-921f-c3b65089bfac` (gandalf)
  - **Why:** The new CHANGELOG section and the rule text are written out in full below, so the step is copying text into three places and checking the word counts. The planning session is long and was about other tasks.
  - **Prompt:** Implement Step 1 of PLAN.md: Shorten the `v0.1.0-rc` items and add the item rules. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Use the text in Decisions exactly as written, and ask the user before changing its wording. `.claude` is a symlink to `.agents`, so the `package-release` skill has only one file to edit. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start the review.

## Context

The `v0.1.0-rc` items in `CHANGELOG.md` average 40 words, and some run past 80. They list every file, term and method, which belongs in the docs, commit messages and pull requests. The user wants items about a third as long. The rules in AGENTS.md only say "one line each", so they need to say how short an item is, and how fixes made before a release are handled.

The outcome: `v0.1.0-rc` has 17 items averaging 13 words (226 words, down from 26 items and 1,030 words). AGENTS.md and the `package-release` skill keep future items that short.

## Decisions

- **Item rules**, replacing the sentence "Items are one line each, written for package users and maintainers." in AGENTS.md → the `CHANGELOG.md` paragraph (Phase 4):

  ```markdown
  Items are written for package users and maintainers, and kept short:

  - One line and one sentence per item, about 15 words and never more than 25: what changed, not how or why.
  - Name the area or the main API, not every file, method, option or term. The docs, commit messages and pull requests hold the details.
  - One item per change, not per step. A fix to something added since the last release updates that item instead of adding a new one.
  ```

  The sentence after it ("The release renames `# vX.Y.Z-rc` …") stays, as its own paragraph.
- **AGENTS.md → Phase 3:** "Check that `CHANGELOG.md` lists what each step did under `# <task branch>`" becomes "Check that `CHANGELOG.md` lists what each step did under `# <task branch>`, following the item rules (see the `CHANGELOG.md` paragraph in Phase 4)".
- **`package-release` skill** (`.agents/skills/package-release/SKILL.md`):
  - Workflow step 4 starts with: "Check that the rc's items follow the item rules in `AGENTS.md` (one short sentence each) and shorten any that don't."
  - Anti-Patterns gets: "- CHANGELOG items that list every file, method or term, or run past one short sentence."
- **New `# v0.1.0-rc` section** in `CHANGELOG.md`, replacing the current one in full and in this order:

  ```markdown
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
  ```

  Merged or dropped: the eight project-docs items became one, the two callback items became one, and "Doc consistency fixes" was dropped because it only fixed docs added in the same unreleased version.
- **This task's own item** goes under `# docs/shorter-changelog-items` above it and follows the new rules: `- Shorter CHANGELOG items, with rules for their length in AGENTS.md and the release skill.`
- **Branch:** `docs/shorter-changelog-items`, from and into `release/0.x`.
- **CI:** the changed files are all covered by `paths-ignore` except `version`, so the plan commit and the close commit run CI. That's expected.

## Steps

### Step 1: Shorten the `v0.1.0-rc` items and add the item rules

**Status:** done

- [x] `AGENTS.md`: the item rules and the Phase 3 line from Decisions
- [x] `.agents/skills/package-release/SKILL.md`: workflow step 4 and the anti-pattern from Decisions
- [x] `CHANGELOG.md`: `# docs/shorter-changelog-items` with this task's item, a `---`, then the new `# v0.1.0-rc` section from Decisions in place of the current one

**Validate:**

- every item under `# v0.1.0-rc` is at most 25 words, and the average is about 13: `awk '/^# v0.1.0-rc/{f=1;next} /^---/{f=0} f && /^- /{n++; w+=NF-1; if (NF-1>25) print "too long: " $0} END {print n " items, " w/n " words on average"}' CHANGELOG.md`
- `make test` (the `version` file test reads `version`)
- `git status` shows only `AGENTS.md`, `.agents/skills/package-release/SKILL.md`, `CHANGELOG.md` and `PLAN.md` changed

**Commit:** `Shorten the CHANGELOG items and add rules for their length`

## Acceptance Criteria

- [ ] `# v0.1.0-rc` holds the 17 items from Decisions, none over 25 words
- [ ] AGENTS.md and the `package-release` skill state the item rules
- [ ] `CHANGELOG.md` lists this task under `# docs/shorter-changelog-items`, and `version` is `docs/shorter-changelog-items`

## Review

Findings from Phase 3 (filled in by the reviewing agent).
