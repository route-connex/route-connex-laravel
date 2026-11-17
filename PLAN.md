# Cap PHPStan below 2.3.6 for the type coverage plugin

**Status:** planning **Branch:** `hotfix/cap-phpstan-type-coverage` → **Target:** `release/0.x`
**Planned on:** `gandalf`
**Planning session:**

```shell
claude --ide --resume 301035d8-61c2-4388-b85e-f83b2e1c0f59
```

## Implementation

- [ ] **1. Cap `phpstan/phpstan` in `require-dev`**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `new`
  - **Resume:** –
  - **Why:** A one-line constraint and a changelog item, but validating it means reproducing CI's dependency resolution in Docker for several lanes, which needs some care. The planning session is long and about another task, and Context below has the full diagnosis.
  - **Prompt:** Implement Step 1 of PLAN.md: Cap `phpstan/phpstan` in `require-dev`. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Read PLAN.md → Context first: it explains why CI fails on `composer test:types` and why the cap is `^2.0 <2.3.6`. Only `composer.json` (`require-dev`), `CHANGELOG.md` and `PLAN.md` should change in git (`composer.lock` is gitignored). Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start the review.

## Context

CI on pull request #1 (`docs/project` → `release/0.x`) failed in `P8.3 - L12.* - prefer-stable - ubuntu-latest`, at the Type Coverage step (`composer test:types`). Fail-fast then cancelled the other matrix jobs. The database jobs passed.

```
Call to undefined method PHPStan\Analyser\AnalyserResult::getCollectedData()
at vendor/pestphp/pest-plugin-type-coverage/src/TestCaseForTypeCoverage.php:106
```

- PHPStan 2.3.6 (released 2026-11-15) removed `AnalyserResult::getCollectedData()`: the method is in `src/Analyser/AnalyserResult.php` at tag 2.3.5 and not at 2.3.6.
- `pestphp/pest-plugin-type-coverage` calls it. It was fixed on 2026-11-16 in v5.1.1 (5.x line, Pest 5, PR #75). The 4.x line (Pest 4), which the Laravel 12 lanes resolve, has no fix yet: the failing job resolved Pest 4.7.8, type-coverage v4.0.4 and PHPStan 2.3.6.
- Nothing in this package changed: any CI run since 2026-11-15 fails the same way, on every branch. The last green run was on 2026-11-03. Locally, `composer.lock` has PHPStan 2.3.4, so `make test` passes.
- PHPStan is only a dev dependency, through `larastan/larastan` (`^2.3.0` in v3.13.2) and the type coverage plugin (v4.0.4: `^1.12.24|^2.1.46`; v5.1.1: `^2.3.5`).

The outcome: CI is green again on every lane, with no effect on apps that install the package.

## Decisions

- **Fix:** cap PHPStan in the package's `require-dev` with `"phpstan/phpstan": "^2.0 <2.3.6"`. `require-dev` only applies to the package's own tests and CI, never to apps that install it. A `conflict` entry would apply to those apps too, so it's not used.
- **Why this constraint:** `^2.0` raises no minimum, so the `prefer-lowest` lanes resolve as before. `<2.3.6` keeps every `prefer-stable` lane on PHPStan 2.3.5 or older, which works with type-coverage v4.0.4 (Laravel 12) and v5.1.1 (Laravel 13, needs `^2.3.5`).
- **Removal:** the cap goes once the type coverage plugin's 4.x line works with PHPStan 2.3.6 or later, or once Laravel 12 lanes no longer use Pest 4. The CHANGELOG item says so.
- **Branch:** `hotfix/cap-phpstan-type-coverage`, from and into `release/0.x`.
- **Pull request #1:** after this hotfix is merged into `release/0.x`, the user merges `release/0.x` into `docs/project` so its CI reruns green.

## Steps

### Step 1: Cap `phpstan/phpstan` in `require-dev`

**Status:** todo

- [ ] Reproduce the failure first: run the "CI lane" command below with `12.*` / `10.*` / `prefer-stable` and confirm it fails on `getCollectedData()`
- [ ] `composer.json` → `require-dev`: add `"phpstan/phpstan": "^2.0 <2.3.6"` (sorted, after `phpstan/extension-installer`), with `make composer ARGS="require --dev 'phpstan/phpstan:^2.0 <2.3.6' --no-update"` or by hand
- [ ] `make composer ARGS="update phpstan/phpstan"` so the local, gitignored `composer.lock` follows the constraint
- [ ] `CHANGELOG.md`: add `# hotfix/cap-phpstan-type-coverage` above a `---` and `# v0.1.0-rc`, with one item: PHPStan is capped below 2.3.6 in `require-dev` because the type coverage plugin's 4.x line calls a method PHPStan 2.3.6 removed; apps that install the package aren't affected, and the cap goes once the plugin is fixed

**CI lane command** (runs in a throwaway copy inside the container, so the host's `vendor/` and `composer.lock` are untouched; set `L`, `TB` and `S` per lane):

```shell
docker compose run --rm -e L='12.*' -e TB='10.*' -e S=prefer-stable app sh -c '
  rm -rf /tmp/ci && mkdir /tmp/ci && tar --exclude=./vendor --exclude=./composer.lock -cf - . | tar -xf - -C /tmp/ci && cd /tmp/ci &&
  composer require "laravel/framework:$L" "orchestra/testbench:$TB" --no-interaction --no-update &&
  composer update --$S --prefer-dist --no-interaction --no-progress --no-scripts &&
  composer run prepare &&
  composer show phpstan/phpstan pestphp/pest-plugin-type-coverage | grep -E "^(name|versions)" &&
  composer test:types && composer test:unit'
```

**Validate:**

- the CI lane command passes for `12.*`/`10.*` and `13.*`/`11.*`, each with `prefer-stable` and `prefer-lowest`, and shows PHPStan below 2.3.6 on the `prefer-stable` lanes
- `make test`
- `git status` shows only `composer.json`, `CHANGELOG.md` and `PLAN.md` changed (`version` is set by the plan commit)

**Commit:** `Cap PHPStan below 2.3.6 until the type coverage plugin supports it`

## Acceptance Criteria

- [ ] `composer.json` → `require-dev` has `"phpstan/phpstan": "^2.0 <2.3.6"`, and nothing in `require` or `conflict` changed
- [ ] `composer test:types` passes on the Laravel 12 and 13 lanes, `prefer-stable` and `prefer-lowest`, reproduced locally
- [ ] CI is green on the pull request for this branch
- [ ] `CHANGELOG.md` lists the cap under `# hotfix/cap-phpstan-type-coverage`, and `version` is `hotfix/cap-phpstan-type-coverage`

## Review

Findings from Phase 3 (filled in by the reviewing agent).
