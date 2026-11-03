# RouteConnex Laravel: project docs

These documents describe the vision, vocabulary, plans and design decisions of `route-connex/route-connex-laravel`. They are for maintainers and contributors and are not shipped in the Composer archive. For installing and using the package, see the [README](../README.md).

## Index

| File | Purpose |
| --- | --- |
| [vision.md](vision.md) | Why the package exists, who it's for, goals, non-goals and principles |
| [concepts.md](concepts.md) | Glossary and domain model: the shared vocabulary |
| [roadmap.md](roadmap.md) | Themes grouped as Now / Next / Later, mapped to versions (0.x → 1.0) |
| [ideas.md](ideas.md) | Parking lot for rough ideas that haven't been checked yet |
| [features/](features/) | One design doc per planned capability, from [`_template.md`](features/_template.md) |
| [decisions/](decisions/) | Architecture decision records, from [`_template.md`](decisions/_template.md) |

## Status labels

Features and roadmap themes use one of these:

- `idea`: written down in `ideas.md`, nothing more
- `proposed`: has a `features/<name>.md` and is open for discussion
- `planned`: agreed and placed on the roadmap
- `in progress`: being built in a `PLAN.md` task
- `shipped (vX.Y.Z)`: released in that version
- `dropped`: decided against; the doc stays as the record

## From idea to feature

1. A rough idea goes into [ideas.md](ideas.md).
2. When it matures, it gets a `features/<name>.md` (`proposed`) and the idea entry links to it.
3. Once agreed, it's placed on the [roadmap](roadmap.md) (`planned`).
4. A `PLAN.md` task builds it (`in progress`); see the Task Plan Workflow in [AGENTS.md](../AGENTS.md).
5. When released, the feature doc is marked `shipped (vX.Y.Z)`. Usage for package users goes in the README and the release notes in `CHANGELOG.md`; the feature doc stays as the design record.

Decisions that shape the project go in [decisions/](decisions/) as numbered records (`NNNN-<kebab-title>.md`).
