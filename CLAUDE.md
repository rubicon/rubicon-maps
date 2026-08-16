# CLAUDE.md

## Project

Rubicon Maps is a Divi-first WordPress mapping plugin with:

- a `rubicon_maps_location` custom post type
- `rubicon_maps_category` and `rubicon_maps_region` taxonomies
- linked map and location-list rendering
- Divi 5 Visual Builder modules
- shortcode fallbacks
- CSV import/export for location data

## Architecture

- `src/` contains shared WordPress/plugin logic.
- `divi-5/` contains Divi 5 server registration and Visual Builder assets.
- `assets/` contains shared frontend/admin CSS and JS.
- `assets/css/rubicon-maps-tokens.css` is the semantic design-token layer (`--rtv-rm-*` custom properties) consumed by both frontend and admin CSS; `design-system/MASTER.md` is its system-of-record (palette, contrast validation, component specs).
- `tests/php/` contains lightweight pure-PHP verification scripts.
- `.github/workflows/` contains the CI that gates every PR to `main` (build/test, PR policy, commit lint) — see Verification.

## Core Contracts

- Location title, excerpt, content, thumbnail, and menu order are native WordPress fields.
- Structured metadata holds addresses, coordinates, contact fields, and marker icon references.
- Leaflet/OpenStreetMap is the v1 default provider path.
- Google provider support stays in the architecture but is not the primary v1 runtime path.
- Divi modules are first-class, not shortcode-only wrappers.

## Conventions

- Internal identifiers (PHP constants/classes, CSS classes/custom properties, JS handles) use the `rtv_rm_`/`rtv-rm-` prefix. Public-facing surfaces stay unprefixed and unchanged: plugin slug/text-domain `rubicon-maps`, REST namespace `rubicon-maps/v1`, shortcodes.
- Frontend design tokens are theme-deferential: `--rtv-rm-text` always resolves to `currentColor` so body text inherits the host theme; the plugin only owns its own chrome (accent, surfaces, states, marker/cluster, popup).
- WCAG 2.2 AA is an active, enforced constraint, not an aspiration — includes Target Size (2.5.8, ≥24px), visible/unobscured focus, `prefers-reduced-motion`, and full JS i18n via `wp_localize_script` (see `FrontendAssetManager::frontendStrings()`). New user-facing strings go through that localized-strings bag, never hardcoded.

## Verification

- `composer dump-autoload`
- `find src divi-5 tests -name '*.php' -print0 | xargs -0 -n1 php -l`
- Run the full test suite, not a subset — `for f in tests/php/*.php; do php "$f"; done`. A partial/scoped run has already let a real regression through once; always run every file.
- `cd divi-5/visual-builder && npm ci && npm run build`
- CI runs the same checks automatically on every PR to `main` (`.github/workflows/build-and-test.yaml`, `pr-policy.yaml`, `commitlint.yaml`) and is required to merge — branch protection on `main` enforces it.

## Packaging

- Build Divi 5 Visual Builder assets before packaging.
- Include Composer autoload output in the release zip.
- Exclude local/editor junk, tests, docs-only files, and development-only dependencies from the release artifact.
- Use `scripts/package-release.sh <version>` to create the distributable zip once the repo is release-ready.
- Treat release metadata as one atomic slice: plugin version header, support/version constants, `README.md`, `readme.txt`, `CHANGELOG.md`, and `docs/releases/X.Y.Z.md` should all move together.
- If versioned builder/package metadata exists in the maintained source tree, keep it aligned with the release version as part of the same release pass.
- Publish the GitHub release with the installable zip and checksum attached, not just the source archive links.
