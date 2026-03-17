# General Repository Process Policy

## Purpose

This document defines the default source control, collaboration, verification, and release process for maintained repositories.

The default rule is simple:

- issue first
- branch per issue
- pull request before merge
- automated checks before merge
- releasable `main`

If a repository needs stricter rules or approved exceptions, it may add a repo-specific overlay. Repo-specific overlays should tighten or specialize this policy, not weaken it without explicit rationale.

## Platform Roles

- Forgejo is the authoritative source of truth for active development.
- GitHub is read-only archive or mirror infrastructure unless explicitly re-enabled for active collaboration.
- Local `origin` should point to Forgejo.
- GitHub push access should remain disabled locally unless there is a deliberate archive-sync task.
- Repo-specific overlays are the approved mechanism for declaring a different canonical host when an exception is intentional.

## Core Workflow

- Every meaningful change starts with an issue.
- Every issue gets its own branch.
- Every branch is merged through a pull request.
- No direct pushes to `main`.
- No force-pushes to `main`.
- `main` is always intended to remain releasable.

## Issue Process

- Open an issue before implementation unless the change is truly trivial.
- The issue should define scope, intent, and acceptance criteria.
- Keep one issue per unit of work.
- Use issues for bugs, features, refactors, docs, release tasks, CI work, and follow-up work.
- Close issues from PRs using explicit references such as `Closes #123`.

### Trivial exception

These may skip issue creation if they do not change behavior:

- typo-only documentation fixes
- metadata-only cleanup
- minor comment cleanups
- workflow label/name fixes with no behavior change

Anything affecting behavior, architecture, packaging, release output, CI behavior, or public documentation should have an issue.

Trivial changes still follow the branch and PR workflow unless a repo-specific overlay explicitly allows otherwise.

Trivial changes must never be pushed directly to `main`.

## Branch Naming

- Use the `dev/` prefix.
- Preferred format: `dev/<issue-number>-<short-kebab-description>`.
- Branch names should be short, descriptive, and tied to the issue.

Examples:

- `dev/102-fix-restore-manifest-validation`
- `dev/118-add-gui-history-panel`
- `dev/43-build-csv-importer`

## Commit Policy

- Commits must be semantic and focused.
- Use one of these prefixes:
  - `feat:`
  - `fix:`
  - `chore:`
  - `docs:`
  - `test:`
  - `ci:`
  - `refactor:`
- Write commits in imperative mood.
- Do not mix unrelated changes in one commit.
- Prefer multiple small coherent commits over one large mixed commit.
- Do not rewrite shared history unless there is a specific reason and coordination.

## Pull Request Policy

- Every branch merges through a PR, even for solo development.
- PRs should stay scoped to one issue or one tightly related slice of work.
- PR titles should be clear and match the semantic intent of the work.
- PR bodies should include:
  - what changed
  - why it changed
  - how it was verified
  - linked issue reference
- PR bodies should include closing syntax when appropriate, such as `Closes #123`.
- Do not merge PRs with failing required checks.
- Do not merge PRs that materially change behavior without tests or explicit rationale.

## Review Policy

- PRs are required for all non-trivial changes.
- Shared repositories should require approval according to branch protection policy.
- Solo repositories may allow self-merge after checks pass.
- If approval is enforced, require at least one meaningful review.
- Approval rules should reflect the actual maintainer model of the repository rather than being copied blindly.

## Required Checks for `main`

Branch protection on `main` should require PRs and passing status checks.

At minimum, each maintained repository should have checks that cover:

- build and test quality
- PR policy and branch naming policy
- semantic commit validation

Check names must match the actual workflows in the repository. If workflow job names change, branch protection must be updated to match.

## Branch Protection Rules

For `main`, enable:

- require pull request before merge
- require passing status checks
- block direct pushes
- block force pushes
- block branch deletion

If review is enforced, also enable:

- require at least one approval

Best practice:

- keep admin bypasses to an absolute minimum
- treat `main` as protected production history

## Verification Standard

Before merging:

- run or confirm the relevant automated checks
- verify behavior changed as intended
- verify documentation if user-facing behavior changed
- verify release metadata if the change affects packaging or release output

Each repository must define its own baseline verification commands. Those commands should be documented in repo-specific process docs and mirrored in CI where practical.

When applicable, also verify:

- formatting
- linting
- packaging
- installer/update flows
- GUI or browser-facing behavior
- migration behavior

## Release and Tagging Policy

- Use Semantic Versioning: `major.minor.patch`.
- Git tags should use a `v` prefix:
  - `v1.0.0`
  - `v1.0.1`
- Release titles should be version-only without extra prose:
  - `1.0.0`
  - `1.0.1`
- Release notes should begin with:
  - `Product Name v1.0.0 (YYYY-MM-DD)`

## Release Creation Rules

- Every published version gets:
  - a git tag
  - a release object
  - release notes
  - release assets when the repository produces distributables
- Tags and releases should stay in sync.
- Release notes should summarize features, fixes, documentation, packaging, and notable internal changes.
- Release assets should include the actual distributables and checksum artifacts when available.
- If signature or verification policy applies, tags should point at the intended verified commit.

## Tagging Best Practice

- Tag meaningful shipped milestones, not every micro-commit.
- Use tags for actual release points, not random internal checkpoints.
- Intermediate implementation history is preserved by commits and PRs.
- If more milestone visibility is needed between releases, use issues, milestones, or project planning instead of inflating tags.

## Release Notes Standard

Release notes should capture:

- new features
- bug fixes
- documentation changes
- CI and release pipeline changes
- packaging and signing changes
- known limitations when relevant

Release notes should not:

- read like raw commit spam
- omit significant user-facing changes
- hide breaking or risky changes

## Repository Hygiene

- Keep README, CHANGELOG, release notes, and product metadata aligned.
- Do not let docs drift from actual behavior.
- Do not commit local-only infrastructure notes unless they belong in the project.
- Do not store secrets in the repo.
- Do not commit machine-specific config unless intentionally part of project setup.
- Keep generated OS junk and editor junk out of the repository and release artifacts.

## Standard Repository Docs

Every maintained repository should include:

- `README.md`
- `CHANGELOG.md`
- `AGENTS.md`
- `CLAUDE.md`

Additional docs such as `CONTRIBUTING.md`, workflow docs, or release runbooks should be added when they improve collaboration or release safety.

## Remote Management

- Forgejo is the active push remote.
- GitHub is archive-only unless explicitly re-enabled.
- Avoid accidental pushes to GitHub by keeping its push URL disabled.
- Verify remotes after migration or host changes.

## CI/CD Policy

- CI must validate build and test quality before merge.
- PR policy automation should enforce branch naming and issue linkage where practical.
- Commit message validation should enforce semantic commit prefixes.
- Release automation should run from version tags, not from arbitrary branches.

## Conditional: If the Repository Ships a WordPress Plugin

Apply all standard rules above, plus:

- release assets must include the installable plugin zip when releases are distributed as zips
- plugin version headers, changelog, and release metadata must align
- required built assets must be included in release packaging
- development-only files and machine junk must not ship in the release zip
- upgrade and uninstall behavior should be considered part of release verification
- `readme.txt` must reflect actual plugin behavior and supported environments

## Conditional: If the Repository Ships a Divi-Integrated WordPress Plugin

Apply the WordPress plugin rules above, plus:

- the supported Divi generations must be documented explicitly
- if both Divi 4 and Divi 5 are supported, keep compatibility layers clearly separated
- shared plugin business logic should remain outside builder-generation runtimes wherever practical
- Divi 5 built Visual Builder assets must be included in release packaging when required for runtime
- if Divi 4 or Divi 5 is intentionally unsupported, document that clearly in product and release docs
- verification should include builder registration, configuration UX, save/render behavior, and frontend output for the supported Divi generation(s)

## Documentation Expectations

When process changes, update the relevant project docs:

- `README.md`
- `CHANGELOG.md`
- release notes
- `CLAUDE.md`
- `AGENTS.md`
- workflow docs if process behavior changes

## Practical Development Flow

1. Create an issue.
2. Create a branch from `main` using `dev/<issue>-<slug>`.
3. Make focused commits using semantic prefixes.
4. Run verification locally.
5. Push branch to Forgejo.
6. Open a PR.
7. Link the issue in the PR body with closing syntax.
8. Ensure required checks pass.
9. Merge via PR into `main`.
10. Tag and publish a release when the merged state is a release milestone.

## What We Avoid

- direct pushes to `main`
- long-lived feature branches with mixed concerns
- unlinked PRs
- vague commit messages
- releases without notes or assets
- tags without corresponding release objects
- undocumented process exceptions
- secret or token material in repo history

## Default Rule

If there is any ambiguity, prefer:

- issue first
- branch per issue
- PR before merge
- automated checks before merge
- tagged releases with complete release notes and assets
- Forgejo as the active canonical host
