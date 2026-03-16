# Changelog

## 0.5.0
**Two Builders, One Plugin**  
2026-03-16

Rubicon Maps stopped pretending Divi 5 was just Divi 4 with better lighting.

### Added
- Added an official-style `divi-5` module layer with dedicated server registration and Visual Builder assets.
- Added native Divi 5 Rubicon Map and Rubicon Location List module definitions with instance-level filtering controls.
- Added a buildable Divi 5 Visual Builder package and compiled builder bundle.

### Changed
- Split Divi support into a Divi 4 compatibility layer and a Divi 5-first module architecture.
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
