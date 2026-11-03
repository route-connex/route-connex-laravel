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
3. Confirm whether version metadata needs to change; many Laravel packages rely on Git tags rather than a hardcoded package version.
4. Update `CHANGELOG.md`: rename `# Unreleased` to `# vX.Y.Z (YYYY-MM-DD)`. The tag is `vX.Y.Z`, matching that heading. The next task's close then starts a new `# Unreleased` at the top, above a `---` line.
5. Set the `version` file to `vX.Y.Z`. It must equal the tag name.
6. Review tag naming, release branch, and GitHub release workflow behavior before any release command.
7. Draft the release commands for the user: commit, `git tag vX.Y.Z` on the release branch and, when `N` is the latest major, `git switch main && git merge --ff-only release/<N>.x`, so `main` points at the newest stable tag. After the release, the next task's close sets `version` back to `release/<N>.x`.
8. To start a new major, the user creates `release/<N+1>.x` from `release/<N>.x`. `main` stays on the `<N>` tags until `v<N+1>.0.0` is tagged, and then it is fast-forwarded to `release/<N+1>.x`. Work branches for the older major keep their `<origin_major>` (`hotfix/<N>/...`).
9. Do not tag, push, or publish without explicit user approval.

## References

- `.github/workflows/tests.yml`
- `CHANGELOG.md`
- `composer.json`
- `Makefile`

## Examples

- Prepare a release by checking the pending diff, running `make test` and `make test-all`, and drafting the tag command for user approval.
- Release `v0.1.0` from `release/0.x`: rename `# Unreleased` in `CHANGELOG.md` to `# v0.1.0 (YYYY-MM-DD)`, set `version` to `v0.1.0`, then draft `git tag v0.1.0` and the fast-forward of `main` for user approval.

## Anti-Patterns

- Creating tags, pushing branches, or publishing releases without explicit approval.
- Tagging a commit whose `version` file or CHANGELOG heading doesn't match the tag.
- Merging anything into `main` other than the fast-forward to a new tag.
- Skipping `make test` or `make test-all` before a release recommendation.
- Treating generated release notes as a replacement for the meaningful entries in `CHANGELOG.md`.
- Changing release workflows without checking the supported matrix in `.github/workflows/tests.yml`.
