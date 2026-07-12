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
- `tests/php/` contains lightweight pure-PHP verification scripts.

## Core Contracts

- Location title, excerpt, content, thumbnail, and menu order are native WordPress fields.
- Structured metadata holds addresses, coordinates, contact fields, and marker icon references.
- Leaflet/OpenStreetMap is the v1 default provider path.
- Google provider support stays in the architecture but is not the primary v1 runtime path.
- Divi modules are first-class, not shortcode-only wrappers.

## Verification

- `composer dump-autoload`
- `php tests/php/LocationQueryArgsBuilderTest.php`
- `php tests/php/LocationAddressFormatterTest.php`
- `php tests/php/LocationCsvTransformerTest.php`
- `php tests/php/MapInstanceConfigBuilderTest.php`
- `find src divi-5 tests -name '*.php' -print0 | xargs -0 -n1 php -l`
- `cd divi-5/visual-builder && npm ci && npm run build`

## Packaging

- Build Divi 5 Visual Builder assets before packaging.
- Include Composer autoload output in the release zip.
- Exclude local/editor junk, tests, docs-only files, and development-only dependencies from the release artifact.
- Use `scripts/package-release.sh <version>` to create the distributable zip once the repo is release-ready.
- Treat release metadata as one atomic slice: plugin version header, support/version constants, `README.md`, `readme.txt`, `CHANGELOG.md`, and `docs/releases/X.Y.Z.md` should all move together.
- If versioned builder/package metadata exists in the maintained source tree, keep it aligned with the release version as part of the same release pass.
- Publish the Forgejo release with the installable zip and checksum attached, not just the source archive links.
