# Rubicon Maps

Rubicon Maps is a Divi-first WordPress location mapping plugin built for people who want real location management without turning every map into a shortcode scavenger hunt. It ships with native location management, linked map/list output, CSV import/export, and a dedicated `divi-5` Visual Builder runtime for Divi 5.

> Version: `1.0.1`  
> License: `GPLv2 or later`  
> Requires WordPress: `5.8+`  
> Requires PHP: `8.1+`  
> Tested up to: `WordPress 6.9.4`  
> Requires Divi: `5.0+`  
> Tested with Divi: `5.1.0`

## What ships in v1.0.1

- Custom post type: `rubicon_location`
- Taxonomies: `rubicon_maps_category`, `rubicon_maps_region`
- Linked frontend map and location list rendering
- Divi 5 modules: `Rubicon Map`, `Rubicon Location List`
- Shortcodes: `[rubicon_maps]`, `[rubicon_maps_list]`
- REST API: `/wp-json/rubicon-maps/v1/locations`
- Structured address, contact, and coordinate metadata
- Leaflet/OpenStreetMap-first frontend rendering
- CSV import/export for locations
- Server-side admin geocoding helper and consistent media picker flows
- Searchable multi-select filter pickers with pill tokens in Divi modules
- Release packaging scripts for distributable plugin zips

## Divi support

Rubicon Maps uses a dedicated Divi 5 integration path:

- `divi-5/` contains the dedicated Divi 5 server registration and Visual Builder assets.
- `src/` contains the builder-agnostic WordPress core: CPTs, taxonomies, REST, settings, shortcodes, and shared render/query logic.

This keeps the shared mapping/query logic in the plugin core while the Divi 5 layer uses the architecture Elegant Themes documents.

Compatibility position for v1:

- Requires WordPress `5.8+`
- Tested through WordPress `6.9.4`
- Supports Divi `5.x`
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
./scripts/package-release.sh 1.0.1
```

## Provider stance

- Leaflet/OpenStreetMap is the supported provider path for the `1.0.x` line.
- Additional provider work is future scope, not part of the current `1.0.x` support promise.

## Release notes

See [CHANGELOG.md](CHANGELOG.md) for version history and [docs/releases/1.0.1.md](docs/releases/1.0.1.md) for the current release notes body.
