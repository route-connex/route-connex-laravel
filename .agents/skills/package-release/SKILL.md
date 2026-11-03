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

1. Review the open diff and pending package changes. If the repo has a changelog, release notes config or release workflows, review those too.
2. Validate the release state with `make test` and `make test-all` before recommending a release.
3. Confirm whether version metadata needs to change; many Laravel packages rely on Git tags rather than a hardcoded package version.
4. Review tag naming, release branch, and GitHub release workflow behavior before any release command.
5. Do not tag, push, or publish without explicit user approval.

## References

- `.github/workflows/tests.yml`
- `composer.json`
- `Makefile`

## Examples

- Prepare a release by checking the pending diff, running `make test` and `make test-all`, and drafting the tag command for user approval.
- If the repo adds release notes config, update its grouping when a new label convention is added.

## Anti-Patterns

- Creating tags, pushing branches, or publishing releases without explicit approval.
- Skipping `make test` or `make test-all` before a release recommendation.
- Treating generated release notes as a replacement for meaningful changelog entries, where the repo keeps a changelog.
- Changing release workflows without checking the supported matrix in `.github/workflows/tests.yml`.
