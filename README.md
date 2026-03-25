# Rubicon Maps

Rubicon Maps is a Divi-first WordPress location mapping plugin built for people who want real location management without turning every map into a shortcode scavenger hunt. It ships with native location management, linked map/list output, CSV import/export, and dual Divi support: a dedicated `divi-4` compatibility layer for legacy builder modules and a dedicated `divi-5` Visual Builder runtime for Divi 5.

> Version: `1.0.0`  
> License: `GPLv2 or later`  
> Requires WordPress: `5.8+`  
> Requires PHP: `8.1+`  
> Tested up to: `WordPress 6.9.4`  
> Lowest supported Divi: `4.0+`  
> Tested with Divi: `5.1.0`

## What ships in v1.0.0

- Custom post type: `rubicon_location`
- Taxonomies: `rubicon_maps_category`, `rubicon_maps_region`
- Linked frontend map and location list rendering
- Divi 4 modules: `Rubicon Map`, `Rubicon Location List`
- Divi 5 modules: `Rubicon Map`, `Rubicon Location List`
- Shortcodes: `[rubicon_maps]`, `[rubicon_maps_list]`
- REST API: `/wp-json/rubicon-maps/v1/locations`
- Structured address, contact, and coordinate metadata
- Leaflet/OpenStreetMap-first frontend rendering
- CSV import/export for locations
- Server-side admin geocoding helper and consistent media picker flows
- Release packaging scripts for distributable plugin zips

## Divi support

Rubicon Maps now uses two distinct integration paths:

- `divi-4/` contains the Divi 4 compatibility bootstrap and legacy builder modules.
- `divi-5/` contains the dedicated Divi 5 server registration and Visual Builder assets.
- `src/` contains the generation-agnostic WordPress core: CPTs, taxonomies, REST, settings, shortcodes, and shared render/query logic.

This keeps the shared mapping/query logic in the plugin core while letting each Divi generation use the architecture Elegant Themes is currently documenting.

Compatibility position for v1:

- Requires WordPress `5.8+`
- Tested through WordPress `6.9.4`
- Supports Divi `4.x` and `5.x`
- Verified in the current release cycle with Divi `5.1.0`

## Shortcodes

```text
[rubicon_maps id="storemap" category="retail,wholesale" region="houston"]
[rubicon_maps_list id="storemap" category="retail,wholesale" region="houston"]
```

Use a shared `id` to keep the map and list synced.

## Admin UX

- `Rubicon Maps → Locations` manages the location CPT.
- `Rubicon Maps → Categories` and `Rubicon Maps → Regions` manage taxonomy-driven filtering.
- `Rubicon Maps → Import / Export` handles canonical CSV transfers.
- `Rubicon Maps → Settings` manages provider defaults, map defaults, and runtime behavior.

Location content remains WordPress-native:

- `title` = location name
- `excerpt` = short summary
- `content` = detailed description or popup body
- custom meta = structured address, coordinates, contact data, and marker icon

## Development

Install PHP dependencies:

```bash
composer install
```

Build Divi 5 Visual Builder assets:

```bash
cd divi-5/visual-builder
npm install
npm run build
```

Create a distributable plugin zip:

```bash
./scripts/package-release.sh 1.0.0
```

## Provider stance

- Leaflet/OpenStreetMap is the supported provider path for v1.0.0.
- Additional provider work is future scope, not part of the v1.0.0 support promise.

## Release notes

See [CHANGELOG.md](CHANGELOG.md) for version history and [docs/releases/1.0.0.md](docs/releases/1.0.0.md) for the release notes body.
