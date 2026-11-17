# 0001: Project documentation structure

**Status:** accepted **Date:** 2026-11-17

## Context

The package was a bare skeleton with no RouteConnex behavior. Before building features, the maintainer wanted to write down why the package exists, the vocabulary it uses, where it's heading and which features are planned, so that later `PLAN.md` tasks start from written requirements. The docs are for maintainers and contributors, not for apps that install the package.

## Decision

- **Location:** `docs/` at the repo root, kept out of the Composer dist archive (`/docs export-ignore` in `.gitattributes`).
- **Files:**
  - `README.md`: the index, the status labels and the idea → feature lifecycle
  - `vision.md`: problem, who it's for, goals, non-goals, guiding principles, where it fits, open questions
  - `concepts.md`: the glossary, how the terms relate, open questions
  - `roadmap.md`: themes grouped as Now / Next / Later, mapped to version ranges (0.x → 1.0), with no dates
  - `ideas.md`: a parking lot, one dated entry per idea
  - `features/`: one design doc per capability, from `_template.md`
  - `decisions/`: numbered architecture decision records (`NNNN-<kebab-title>.md`), from `_template.md`
- **Status labels** for features and roadmap themes: `idea` → `proposed` → `planned` → `in progress` → `shipped (vX.Y.Z)`, plus `dropped`.
- **Lifecycle:** `ideas.md` → `features/<name>.md` (`proposed`) → placed on the roadmap (`planned`) → a `PLAN.md` task (`in progress`) → `shipped (vX.Y.Z)`. Usage for package users goes in the README and release notes in `CHANGELOG.md`; the feature doc stays as the design record.
- **Writing:** the docs are written with the maintainer, interview-style. Agents don't invent vision, terms, ideas or features; anything undecided goes under Open questions.
- **Naming:** the docs don't name consumer apps or RouteConnex's private repos; they mention the RouteConnex API and the public SDK only.
- **Agents and README:** the Task Plan Workflow tells planning agents to read the project docs, and the README links to `docs/README.md`.
- **CI:** no change; `**.md` in `paths-ignore` already skips docs-only changes.

## Consequences

- A task starts from written requirements, and planning agents have the vision, the vocabulary and the feature designs to read first.
- Contributors find the docs on GitHub, and apps that install the package don't download them.
- Each document needs maintaining: a feature's status and the roadmap change as work is planned and released, and the feature doc stays as the design record after release.
- The docs are less formal than code: nothing checks them, apart from the link check in review.
