# Rubicon Maps Repository Overlay

## Purpose

This document applies the general repository process policy to Rubicon Maps.

Where this overlay is more specific, this overlay governs Rubicon Maps. Where it is silent, the general policy governs.

## Canonical Host

This overlay intentionally overrides the general Forgejo-first default (see the general policy's "Repo overlays are the approved mechanism for declaring a different canonical host when an exception is intentional").

- GitHub is the canonical host for Rubicon Maps.
- `origin` points to GitHub.
- The repository is public.
- CI (GitHub Actions), branch protection, issues, and pull requests all live on GitHub.
- Forgejo is not currently the active host for this repo; reconcile or remove the `forgejo` remote if it still exists locally.

Current intended remote shape:

- `origin` → GitHub
- `remote.pushDefault` → `origin`

## Mainline Rule

From this point forward:

- no direct pushes to `main`
- all meaningful work starts with an issue
- all issue work happens on a `dev/...` branch
- all merges to `main` happen through GitHub pull requests

Historical bootstrap commits that were pushed directly to `main` before adoption of this policy are treated as pre-policy exceptions and do not need to be rewritten.

## Branch Naming

Use:

- `dev/<issue-number>-<short-kebab-description>`

Examples for this repository:

- `dev/12-build-csv-import-export`
- `dev/18-finish-divi5-module-registration`
- `dev/24-harden-location-save-validation`

## Baseline Verification for Rubicon Maps

Before merging changes that affect plugin behavior, run:

- `composer dump-autoload`
- `find src divi-5 tests -name '*.php' -print0 | xargs -0 -n1 php -l`
- `for f in tests/php/*.php; do php "$f"; done` — the full suite, not a subset
- `cd divi-5/visual-builder && npm ci && npm run build`

GitHub Actions CI (`.github/workflows/`) runs the same checks automatically on every PR and is required by branch protection on `main`.

When relevant, also verify:

- admin settings behavior
- location editor save flows
- REST endpoint behavior
- shortcode output
- Divi 5 module registration, configuration, save, and output
- release packaging

## Divi-Specific Rule for Rubicon Maps

Rubicon Maps uses a dedicated Divi 5 runtime:

- `divi-5/` for Divi 5 runtime and Visual Builder assets
- `src/` for shared WordPress/plugin logic

Do not move builder-generation-specific behavior back into core loader code unless there is a compelling reason. Shared query, rendering, settings, REST, and data logic should remain generation-agnostic.

## WordPress Plugin Release Rules for Rubicon Maps

Every release candidate must verify:

- plugin header version matches the intended release version
- `README.md`, `readme.txt`, `CHANGELOG.md`, and `docs/releases/X.Y.Z.md` agree on the intended release state
- `src/Support/Plugin.php` and any maintained versioned builder/package metadata agree on the intended release version
- the Divi 5 compiled bundle is present when required for runtime
- release packaging excludes local/editor junk such as `.DS_Store`, `__MACOSX`, and machine-local files
- the installable plugin zip contains the assets required for runtime
- the GitHub release has the installable zip and checksum attached when those artifacts are produced

## Release Naming for Rubicon Maps

- git tags use `v` prefix, for example `v1.0.0`
- release titles use version-only, for example `1.0.0`
- release notes begin with:
  - `Rubicon Maps v1.0.0 (YYYY-MM-DD)`

Recommended release notes structure:

1. product/version/date line
2. concise release subtitle
3. witty intro
4. `Added`
5. `Changed`
6. `Fixed`
7. `Security`

## Immediate Follow-Through

The next meaningful changes in this repository should follow the new process:

1. open or confirm the issue
2. branch from `main` with `dev/...`
3. implement and verify
4. push branch to GitHub
5. open PR
6. merge through PR only
