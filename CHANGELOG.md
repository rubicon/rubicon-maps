# Changelog

## 1.0.0
**Maps, Meet Manners**  
2026-03-18

Rubicon Maps now ships as a stable v1 release with Leaflet-first maps, linked lists, hardened admin flows, and release packaging that matches the actual product.

### Added
- Added CSV import/export with a canonical Rubicon Maps schema for locations, taxonomies, and structured metadata.
- Added an Import / Export admin page with secure upload/download handlers.
- Added branded admin styling, richer location-edit panels, and category marker-icon previews.
- Added a server-side geocoding helper for admin address lookup instead of a direct browser-side Nominatim call.
- Added `AGENTS.md`, `CLAUDE.md`, and release packaging/verification scripts.
- Added a canonical frontend map config builder with standalone PHP test coverage.
- Added marker clustering, popup behavior controls, and fixed-height synced list behavior for the v1 runtime.

### Changed
- Promoted the plugin metadata and docs from `1.0.0-rc.1` to the stable `1.0.0` release state.
- Updated the settings page and release docs to reflect the Leaflet-first v1 support boundary.
- Added explicit submenu entries for Categories and Regions under Rubicon Maps.
- Improved frontend map behavior to respect tile URLs, popup behavior, multi-marker bounds fitting, custom marker icons, and cluster-aware list syncing.

### Fixed
- Fixed media picker behavior so marker-icon selection works consistently in location and category admin screens.
- Fixed popup output escaping in the frontend JS layer.
- Fixed release packaging rules so Divi 5 runtime source metadata stays in the distributable zip.
- Fixed default listing presentation so location items no longer inherit browser bullet styling.

### Security
- Routed admin geocoding through authenticated WordPress AJAX with nonce protection.

## 1.0.0-rc.1
**The Candidate Stops Wandering**  
2026-03-17

Rubicon Maps reached its first tagged v1 release candidate with the Leaflet-first product shape, release packaging flow, and the split Divi 4/Divi 5 architecture that the stable release was finalized from.

### Added
- Added release packaging and verification scripts for installable plugin artifacts.
- Added updater and lifecycle support for Forgejo-hosted release metadata.
- Added a canonical release-candidate metadata pass across plugin headers, readmes, and changelog content.

### Changed
- Promoted the plugin metadata to the `1.0.0-rc.1` release-candidate state.
- Clarified the Leaflet-first support boundary for the initial v1 release line.

### Fixed
- Fixed release metadata alignment so the candidate package and repository docs referenced the same pre-release version.

## 0.5.0
**Two Builders, One Plugin**  
2026-03-16

Rubicon Maps stopped pretending Divi 5 was just Divi 4 with better lighting.

### Added
- Added an official-style `divi-5` module layer with dedicated server registration and Visual Builder assets.
- Added native Divi 5 Rubicon Map and Rubicon Location List module definitions with instance-level filtering controls.
- Added a buildable Divi 5 Visual Builder package and compiled builder bundle.
- Added a dedicated `divi-4` bootstrap so legacy builder modules are no longer registered from core loader code.

### Changed
- Split Divi support into a Divi 4 compatibility layer and a Divi 5-first module architecture.
- Moved Divi generation decisions into a small tested support class instead of scattering the checks across core bootstrap code.
- Updated repository ignore rules to keep Composer `vendor/` out of source control.

### Fixed
- Prevented the legacy Divi 4 module registration path from colliding with Divi 5 module registration.

## 0.4.0
**Core Alignment for Divi-First v1**  
2026-03-16

The plugin finally stopped arguing with itself and started acting like a single product again.

### Changed
- Canonicalized plugin constants for option names, post type slugs, taxonomy slugs, REST namespace, and shortcode names.
- Aligned settings storage, runtime reads, and uninstall cleanup to the same option key.
- Moved shortcode rendering to shared frontend renderers so shortcodes, REST, and future Divi 5 rendering can share one data model.
- Updated the location post type to support `title`, `excerpt`, `content`, `thumbnail`, and ordering fields.

### Added
- Added a canonical location query args builder with standalone PHP tests.
- Added structured location payload serialization with formatted address handling.
- Added linked map/list frontend rendering with multi-instance JavaScript support.
- Added cleaner settings fields for provider, center, zoom, map height, tile URL, and scroll-wheel behavior.
- Added `CHANGELOG.md` and `.editorconfig` for repository hygiene.

### Fixed
- Fixed category taxonomy registration to use the actual Rubicon Maps location post type.
- Fixed REST filtering to support category, region, and explicit location IDs.
- Fixed Divi PHP module shortcode targets and parameter signatures.
- Fixed the frontend list path so it no longer routes to a placeholder implementation.
- Fixed address rendering to use structured address parts instead of a dead `address` meta assumption.

### Security
- Added nonce and capability checks to category metadata saves.
- Tightened uninstall cleanup and settings sanitization.
