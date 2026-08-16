# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 2026-08-16

First public, open-source release. **Breaking:** Divi 4 support is removed (Divi 5 only) and internal identifiers were renamed to the `rtv_rm_` prefix.

### Added
- GPL-2.0-or-later `LICENSE` and plugin license headers.
- Frontend map + list UI foundation: a unified `--rtv-rm-*` design-token layer (light and dark), loading / error / empty states, a branded default marker and cluster ramp, popup styling, WCAG 2.2 AA support, and full JavaScript i18n via `wp_localize_script`.
- GitHub Actions CI (build/test, PR policy, commit-lint) and a release workflow that attaches the installable zip and checksum to GitHub Releases.
- Dependabot for dependency and GitHub Actions updates.
- Public-repository documentation: `SECURITY.md`, `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, `SUPPORT.md`, and `ARCHITECTURE.md`.

### Changed
- Standardized internal machine identifiers on the `rtv_rm_` / `rtv-rm-` prefix (post type, taxonomies, options, nonces, asset handles). Public-facing slug, text domain, REST namespace, and shortcodes are unchanged.
- Migrated the plugin auto-updater to GitHub Releases.
- Made GitHub the canonical host; the repository is now public and open-source.
- Converted `AGENTS.md` to a pointer stub and aligned repository docs to the current state.

### Removed
- **Divi 4 runtime and modules. The plugin now targets Divi 5 only.**

## [1.0.1] - 2026-03-25

### Added
- Searchable multi-select filter pickers with pill tokens for Divi 5 map and listing modules.
- Structured filter value helpers and normalization so modern saved module values and legacy comma-separated values both continue to work.
- A Divi 4 builder-side filter enhancement path for categories, regions, and explicit locations.

### Changed
- Promoted plugin metadata, readmes, and release references from `1.0.0` to `1.0.1`.
- Improved the Divi 5 picker interaction model with open-on-click behavior, fixed-height scrolling, checkbox rows, and footer actions.

### Fixed
- Closed the release-discipline gap where merged post-v1 changes were not yet reflected in the changelog and release metadata.

## [1.0.0] - 2026-03-18

### Added
- CSV import/export with a canonical Rubicon Maps schema for locations, taxonomies, and structured metadata.
- An Import / Export admin page with secure upload/download handlers.
- Branded admin styling, richer location-edit panels, and category marker-icon previews.
- A server-side geocoding helper for admin address lookup instead of a direct browser-side Nominatim call.
- `AGENTS.md`, `CLAUDE.md`, and release packaging/verification scripts.
- A canonical frontend map config builder with standalone PHP test coverage.
- Marker clustering, popup behavior controls, and fixed-height synced list behavior.

### Changed
- Promoted the plugin metadata and docs from `1.0.0-rc.1` to the stable `1.0.0` release state.
- Updated the settings page and release docs to reflect the Leaflet-first support boundary.
- Added explicit submenu entries for Categories and Regions under Rubicon Maps.
- Improved frontend map behavior to respect tile URLs, popup behavior, multi-marker bounds fitting, custom marker icons, and cluster-aware list syncing.

### Fixed
- Media picker behavior so marker-icon selection works consistently in location and category admin screens.
- Popup output escaping in the frontend JS layer.
- Release packaging rules so Divi 5 runtime source metadata stays in the distributable zip.
- Default listing presentation so location items no longer inherit browser bullet styling.

### Security
- Routed admin geocoding through authenticated WordPress AJAX with nonce protection.

## [1.0.0-rc.1] - 2026-03-17

### Added
- Release packaging and verification scripts for installable plugin artifacts.
- Updater and lifecycle support for release metadata.
- A canonical release-candidate metadata pass across plugin headers, readmes, and changelog content.

### Changed
- Promoted the plugin metadata to the `1.0.0-rc.1` release-candidate state.
- Clarified the Leaflet-first support boundary for the initial release line.

### Fixed
- Release metadata alignment so the candidate package and repository docs referenced the same pre-release version.

## [0.5.0] - 2026-03-16

### Added
- An official-style `divi-5` module layer with dedicated server registration and Visual Builder assets.
- Native Divi 5 Rubicon Map and Rubicon Location List module definitions with instance-level filtering controls.
- A buildable Divi 5 Visual Builder package and compiled builder bundle.

### Changed
- Split Divi support into a compatibility layer and a Divi 5-first module architecture.
- Moved Divi generation decisions into a small tested support class instead of scattering the checks across core bootstrap code.
- Updated repository ignore rules to keep Composer `vendor/` out of source control.

### Fixed
- Prevented the legacy module registration path from colliding with Divi 5 module registration.

## [0.4.0] - 2026-03-16

### Added
- A canonical location query args builder with standalone PHP tests.
- Structured location payload serialization with formatted address handling.
- Linked map/list frontend rendering with multi-instance JavaScript support.
- Cleaner settings fields for provider, center, zoom, map height, tile URL, and scroll-wheel behavior.
- `CHANGELOG.md` and `.editorconfig` for repository hygiene.

### Changed
- Canonicalized plugin constants for option names, post type slugs, taxonomy slugs, REST namespace, and shortcode names.
- Aligned settings storage, runtime reads, and uninstall cleanup to the same option key.
- Moved shortcode rendering to shared frontend renderers so shortcodes, REST, and Divi 5 rendering share one data model.
- Updated the location post type to support `title`, `excerpt`, `content`, `thumbnail`, and ordering fields.

### Fixed
- Category taxonomy registration to use the actual location post type.
- REST filtering to support category, region, and explicit location IDs.
- The frontend list path so it no longer routes to a placeholder implementation.
- Address rendering to use structured address parts instead of a dead `address` meta assumption.

### Security
- Added nonce and capability checks to category metadata saves.
- Tightened uninstall cleanup and settings sanitization.

[Unreleased]: https://github.com/rubicon/rubicon-maps/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/rubicon/rubicon-maps/compare/v1.0.1...v2.0.0
[1.0.1]: https://github.com/rubicon/rubicon-maps/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/rubicon/rubicon-maps/compare/v1.0.0-rc.1...v1.0.0
[1.0.0-rc.1]: https://github.com/rubicon/rubicon-maps/compare/v0.5.0...v1.0.0-rc.1
[0.5.0]: https://github.com/rubicon/rubicon-maps/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/rubicon/rubicon-maps/releases/tag/v0.4.0
