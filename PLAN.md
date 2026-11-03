# Project documentation: vision, concepts, roadmap, ideas and features

**Status:** planning
**Branch:** `docs/project` → **Target:** `release/0.x`
**Planned on:** `gandalf`
**Planning session:**

```shell
claude --ide --resume 88963194-682b-472c-9d64-33580c4b6038
```

## Implementation

- [x] **1. Scaffold `docs/`**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `same`
  - **Resume:** `claude --ide --resume 88963194-682b-472c-9d64-33580c4b6038` (gandalf)
  - **Why:** Creating files from a structure already agreed in the planning session, plus a one-line `.gitattributes` change. The planning session has the structure loaded.
  - **Prompt:** Implement Step 1 of PLAN.md: Scaffold `docs/`. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. The structure, file purposes, status labels and feature template fields are in PLAN.md → Decisions. Create headings and short placeholder comments only: no vision or feature content, which later steps write with the user. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 2.
- [ ] **2. Write `docs/vision.md` with the user**
  - **Model:** `opus`
  - **Effort:** `high`
  - **Session:** `same`
  - **Resume:** –
  - **Why:** The vision comes from the user's head, not the code: it needs a careful interview and good judgement on goals and non-goals. Same session keeps the planning context about the ecosystem.
  - **Prompt:** Implement Step 2 of PLAN.md: Write `docs/vision.md` with the user. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Step 1 created `docs/vision.md` with its section headings. Interview the user section by section, draft each section from their answers, and revise until they approve it. Don't invent goals, features or facts: anything the user hasn't decided goes under Open questions. You may read the sibling repos `../route-connex-web`, `../route-connex-agent` and `../route-connex-sdk-php` (read-only) for context, but confirm with the user before writing anything taken from them. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 3.
- [ ] **3. Write `docs/concepts.md` with the user**
  - **Model:** `opus`
  - **Effort:** `high`
  - **Session:** `same`
  - **Resume:** –
  - **Why:** Defining the domain vocabulary needs judgement and builds directly on the vision just written in the same session.
  - **Prompt:** Implement Step 3 of PLAN.md: Write `docs/concepts.md` with the user. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Step 2 wrote `docs/vision.md`; reuse its terms. Interview the user for each term and how the terms relate, draft, and revise until they approve. You may read the sibling repos `../route-connex-web`, `../route-connex-agent` and `../route-connex-sdk-php` (read-only) to keep the terms consistent with them, but confirm with the user before using anything from them. Don't invent definitions: unclear terms go under Open questions. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 4.
- [ ] **4. Write `docs/ideas.md` and `docs/roadmap.md` with the user**
  - **Model:** `sonnet`
  - **Effort:** `high`
  - **Session:** `new`
  - **Resume:** –
  - **Why:** Mostly collecting and ordering the user's ideas. The vision and concepts are on disk by now, and the earlier session will be long.
  - **Prompt:** Implement Step 4 of PLAN.md: Write `docs/ideas.md` and `docs/roadmap.md` with the user. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Read `docs/README.md`, `docs/vision.md` and `docs/concepts.md` first (Steps 1–3); use their terms and the status labels in `docs/README.md`. Collect the user's ideas into `docs/ideas.md`, then ask which ones become roadmap themes and in which version group (Now / Next / Later). Don't add ideas of your own unless the user asks for suggestions, and mark any you suggest as such until the user accepts them. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 5.
- [ ] **5. Write the first feature docs with the user**
  - **Model:** `opus`
  - **Effort:** `high`
  - **Session:** `same`
  - **Resume:** –
  - **Why:** Turning roadmap themes into concrete developer-facing APIs needs design judgement, Laravel package idioms and multi-database awareness. Builds directly on the roadmap from Step 4.
  - **Prompt:** Implement Step 5 of PLAN.md: Write the first feature docs with the user. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Step 4 wrote `docs/roadmap.md`; write one `docs/features/<name>.md` from `docs/features/_template.md` for each roadmap theme in the Now group (ask the user if Next themes should get one too). Interview the user per feature; you may propose the developer-experience examples (config, facade, Artisan) in Laravel-native style, marked as proposals until the user approves. Fill the database & compatibility notes against every supported database and Laravel 12/13 (use the `package-compatibility` skill). Link each feature from `docs/roadmap.md` and the idea it came from in `docs/ideas.md`. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start Step 6.
- [ ] **6. Record decisions and cross-link the docs**
  - **Model:** `sonnet`
  - **Effort:** `medium`
  - **Session:** `same`
  - **Resume:** –
  - **Why:** Writing ADRs for decisions already taken in Steps 2–5 and checking links: the Step 5 session knows which decisions were made.
  - **Prompt:** Implement Step 6 of PLAN.md: Record decisions and cross-link the docs. Follow AGENTS.md (Task Plan Workflow) and the step's section in PLAN.md. Write `docs/decisions/0001-project-documentation-structure.md` from `docs/decisions/_template.md` using PLAN.md → Decisions, then list the decisions taken while writing Steps 2–5 and ask the user which ones get an ADR. Check that every relative link in `docs/` and the README's docs link resolves, and that `docs/README.md` lists every file. Tick the step's boxes as you go, update the plan if the implementation differs from it, and ask before any unplanned change. Before changing any code, fill this entry's Resume item with `claude --ide --resume <this session's ID>` followed by this computer's hostname in parentheses. Run its Validate commands, set this entry's Done to [x], then stop and give a suggested commit message. Don't start the review.

## Context

The package is still a bare skeleton (service provider, publish tags, Docker toolchain) with no RouteConnex behavior. Before building features, the user wants to write down why the package exists, the vocabulary it uses, where it's heading and which features are planned, so that later `PLAN.md` tasks can start from written requirements instead of from scratch.

The package is one part of the RouteConnex ecosystem, next to `route-connex-web`, `route-connex-agent` and `route-connex-sdk-php`, so the docs also say where it fits.

The outcome is a `docs/` folder for maintainers and contributors (not shipped in the Composer archive), with a clear path from a raw idea to a shipped feature.

## Decisions

- **Location:** `docs/` at the repo root.
- **Scope:** the full set, decisions folder (ADRs) included:

  ```
  docs/
  ├── README.md              # index, status labels, idea → feature lifecycle
  ├── vision.md
  ├── concepts.md
  ├── roadmap.md
  ├── ideas.md
  ├── features/
  │   └── _template.md
  └── decisions/
      └── _template.md
  ```

- **`docs/README.md`:** what each file is for, the status labels and the lifecycle below.
- **`vision.md` sections:** Problem, Who it's for, Goals, Non-goals, Guiding principles, Where it fits (the ecosystem: web, agent, PHP SDK), Open questions.
- **`concepts.md`:** a glossary (one heading per term: definition, related terms) and a short section on how the terms relate, plus Open questions.
- **`roadmap.md`:** themes grouped as Now / Next / Later, each mapped to a target version range (0.x → 1.0), with no dates. Each theme links to its `features/*.md` once one exists.
- **`ideas.md`:** a parking lot. One entry per idea: a dated heading and one or two lines. An idea that matures moves into `features/` and its entry links there.
- **Feature template fields:** Status, Target version, Summary, Motivation, Developer experience (example config, facade calls, Artisan commands), Scope, Non-goals, Database & compatibility notes (MySQL, PostgreSQL, MongoDB, SQLite; Laravel 12/13), Open questions, Related.
- **Status labels** (features and roadmap themes): `idea` → `proposed` → `planned` → `in progress` → `shipped (vX.Y.Z)`, plus `dropped`.
- **Lifecycle:** `ideas.md` → `features/<name>.md` (`proposed`) → a `PLAN.md` task (`in progress`) → `shipped (vX.Y.Z)`. User-facing usage goes in the README and release notes in `CHANGELOG.md`; the feature doc stays as the design record.
- **ADR template:** Status (proposed, accepted, superseded by NNNN), Date, Context, Decision, Consequences. Files are numbered `NNNN-<kebab-title>.md`. The first ADR records this documentation structure.
- **Dist archive:** `docs/` is export-ignored in `.gitattributes`; these docs are for maintainers and contributors, not for apps that install the package.
- **CI:** no change. `**.md` in `paths-ignore` already skips docs-only changes; Step 1's `.gitattributes` change will trigger CI, which is fine.
- **Agents:** AGENTS.md → Phase 1 step 2 gets a line telling planning agents to read `docs/vision.md`, `docs/concepts.md` and the relevant `docs/features/*.md`.
- **README:** a short "Project docs" line linking to `docs/README.md`, since `docs/` isn't shipped and GitHub is where contributors find it.
- **Writing the content:** Steps 2–5 are written with the user, interview-style. Agents don't invent vision, terms, ideas or features; anything undecided goes under Open questions.
- **Steps:** Step 1 scaffolds every file, then one step per document (or pair of documents), then the ADRs and the link check.
- **Branch:** `docs/project`, from and into `release/0.x`.

## Steps

### Step 1: Scaffold `docs/`
**Status:** done
- [x] `docs/README.md`: index of every file with a one-line purpose, the status labels and the lifecycle (see Decisions)
- [x] `docs/vision.md`, `docs/concepts.md`, `docs/roadmap.md`, `docs/ideas.md`: title, section headings from Decisions and a short placeholder comment per section
- [x] `docs/features/_template.md` with the feature template fields
- [x] `docs/decisions/_template.md` with the ADR fields
- [x] `.gitattributes`: add `/docs export-ignore`
- [x] `AGENTS.md` → Phase 1 step 2: tell planning agents to read the project docs
- [x] `README.md`: link to `docs/README.md`
- [x] `CHANGELOG.md`: add `# docs/project` with this step's items, above a `---` and `# v0.1.0-rc`

**Validate:** `git check-attr export-ignore docs` prints `export-ignore: set`; `make test`
**Commit:** `Scaffold the project documentation in docs/`

**Note:** the validate command checks `docs`, not `docs/README.md`: `git check-attr` doesn't apply a directory pattern like `/docs` to the files inside it, though `git archive` does exclude them.

### Step 2: Write `docs/vision.md` with the user
**Status:** todo
- [ ] Problem, Who it's for, Goals, Non-goals, Guiding principles, Where it fits, all approved by the user
- [ ] Undecided points listed under Open questions
- [ ] `CHANGELOG.md`: item under `# docs/project`

**Validate:** the user approves the document; `git status` shows only `docs/vision.md`, `CHANGELOG.md` and `PLAN.md` changed
**Commit:** `Write the project vision`

### Step 3: Write `docs/concepts.md` with the user
**Status:** todo
- [ ] Glossary entries and how the terms relate, approved by the user
- [ ] `docs/vision.md` updated if it uses a term differently (tell the user)
- [ ] `CHANGELOG.md`: item under `# docs/project`

**Validate:** the user approves the document; `git status` shows only files in `docs/`, `CHANGELOG.md` and `PLAN.md` changed
**Commit:** `Write the RouteConnex concepts and glossary`

### Step 4: Write `docs/ideas.md` and `docs/roadmap.md` with the user
**Status:** todo
- [ ] `docs/ideas.md`: the user's ideas, one dated entry each
- [ ] `docs/roadmap.md`: themes in Now / Next / Later with target version ranges and status labels
- [ ] `CHANGELOG.md`: item under `# docs/project`

**Validate:** the user approves both documents; `git status` shows only files in `docs/`, `CHANGELOG.md` and `PLAN.md` changed
**Commit:** `Write the ideas list and the roadmap`

### Step 5: Write the first feature docs with the user
**Status:** todo
- [ ] One `docs/features/<name>.md` per Now theme (and Next themes if the user asks), from the template
- [ ] Database & compatibility notes cover MySQL, PostgreSQL, MongoDB, SQLite and Laravel 12/13
- [ ] Links from `docs/roadmap.md` and `docs/ideas.md` to each feature
- [ ] `CHANGELOG.md`: item under `# docs/project`

**Validate:** the user approves each feature doc; `git status` shows only files in `docs/`, `CHANGELOG.md` and `PLAN.md` changed
**Commit:** `Write the first feature docs`

### Step 6: Record decisions and cross-link the docs
**Status:** todo
- [ ] `docs/decisions/0001-project-documentation-structure.md`
- [ ] ADRs for the decisions from Steps 2–5 that the user picks
- [ ] Every relative link in `docs/` and the README's docs link resolves; `docs/README.md` lists every file
- [ ] `CHANGELOG.md`: item under `# docs/project`

**Validate:** `grep -rnoE '\]\([^)#:]+\.md' docs README.md` lists only paths that exist; `git status` shows only files in `docs/`, `CHANGELOG.md` and `PLAN.md` changed
**Commit:** `Record the documentation decisions and cross-link the docs`

## Acceptance Criteria
- [ ] `docs/` holds every file listed in Decisions, and `docs/README.md` indexes them with the status labels and lifecycle
- [ ] `vision.md`, `concepts.md`, `ideas.md`, `roadmap.md` and the feature docs hold content the user approved, with no invented content
- [ ] Every feature doc follows the template, including the database & compatibility notes
- [ ] `docs/` is export-ignored (`git archive HEAD | tar -t | grep '^docs/'` prints nothing)
- [ ] AGENTS.md points planning agents to the project docs, and the README links to `docs/README.md`
- [ ] All relative links in `docs/` resolve
- [ ] `CHANGELOG.md` lists each step's items under `# docs/project`, and `version` is `docs/project`

## Review
Findings from Phase 3 (filled in by the reviewing agent).
