---
name: package-release
description: "Use this skill when preparing Laravel package releases: CHANGELOG.md updates, generated release notes, GitHub release workflows, version checks, tags, release validation, or release automation changes. Never publish autonomously."
license: MIT
metadata:
  author: laravel
---

# Package Release

## Primary Goal

Prepare a safe package release checklist and implementation without tagging, pushing, or publishing unless the user explicitly approves that action.

## Workflow

1. Work on the `release/<N>.x` branch being released (see Branching and Versioning in `AGENTS.md`). Review the open diff and pending package changes, `CHANGELOG.md` and any release workflows.
2. Validate the release state with `make test` and `make test-all` before recommending a release.
3. Composer takes the package version from the Git tag, so `composer.json` has no `version`. The `version` file must match the tag instead (step 5).
4. Update `CHANGELOG.md`: rename the top `# vX.Y.Z-rc` to `# vX.Y.Z (YYYY-MM-DD)`. The release version is the rc's `vX.Y.Z`, and the tag is `vX.Y.Z`, matching that heading. The next task's close then asks for the next version and starts a new `# vX.Y.Z-rc` at the top, above a `---` line.
5. Set the `version` file to `vX.Y.Z`. It must equal the tag name. The `version` workflow (`.github/workflows/version.yml`) fails a tag push when the tag isn't a plain `vX.Y.Z` (such as `vX.Y.Z-rc`), `version` differs from the tag or the first heading of `CHANGELOG.md` isn't `# vX.Y.Z (`.
6. Review tag naming, release branch, and GitHub release workflow behavior before any release command.
7. Draft the release commands for the user: commit, `git tag vX.Y.Z` on the release branch and, when `N` is the latest major, `git switch main && git merge --ff-only vX.Y.Z`, so `main` points at the newest stable tag even if the release branch has moved on. After the release, the next task's close asks for the next version and sets `version` to its `vX.Y.Z-rc`.
8. To start a new major, the user creates `release/<N+1>.x` from `release/<N>.x`. `main` stays on the `<N>` tags until `v<N+1>.0.0` is tagged, and then it is fast-forwarded to `release/<N+1>.x`. Work branches for the older major keep their `<origin_major>` (`hotfix/<N>/...`).
9. Do not tag, push, or publish without explicit user approval.

## References

- `.github/workflows/tests.yml`
- `.github/workflows/version.yml`
- `CHANGELOG.md`
- `composer.json`
- `Makefile`

## Examples

- Prepare a release by checking the pending diff, running `make test` and `make test-all`, and drafting the tag command for user approval.
- Release `v0.1.0` from `release/0.x`, whose `version` is `v0.1.0-rc`: rename `# v0.1.0-rc` in `CHANGELOG.md` to `# v0.1.0 (YYYY-MM-DD)`, set `version` to `v0.1.0`, then draft `git tag v0.1.0` and `git merge --ff-only v0.1.0` on `main` for user approval.

## Anti-Patterns

- Creating tags, pushing branches, or publishing releases without explicit approval.
- Tagging a commit whose `version` file or CHANGELOG heading doesn't match the tag.
- Tagging a release candidate (`vX.Y.Z-rc`). Only plain `vX.Y.Z` tags exist, and the release renames the rc first.
- Merging anything into `main` other than the fast-forward to a new tag.
- Skipping `make test` or `make test-all` before a release recommendation.
- Treating generated release notes as a replacement for the meaningful entries in `CHANGELOG.md`.
- Changing release workflows without checking the supported matrix in `.github/workflows/tests.yml`.
