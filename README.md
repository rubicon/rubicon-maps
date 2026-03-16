# Rubicon Maps

Rubicon Maps is a Divi-first WordPress location mapping plugin with native location management, linked map/list output, and dual Divi support: a compatibility layer for Divi 4 and a dedicated Visual Builder module layer for Divi 5.

> Version: `0.5.0`  
> License: `GPLv2 or later`  
> Status: `Pre-1.0`  
> Tested up to: `WordPress 6.8.1`

## What ships today

- Custom post type: `rubicon_maps_location`
- Taxonomies: `rubicon_maps_category`, `rubicon_maps_region`
- Linked frontend map and location list rendering
- Divi 4 modules: `Rubicon Map`, `Rubicon Location List`
- Divi 5 modules: `Rubicon Map`, `Rubicon Location List`
- Shortcodes: `[rubicon_maps]`, `[rubicon_maps_list]`
- REST API: `/wp-json/rubicon-maps/v1/locations`
- Structured address, contact, and coordinate metadata
- Leaflet/OpenStreetMap-first frontend rendering

## Divi support

Rubicon Maps now uses two distinct integration paths:

- `src/Divi/` contains the Divi 4 compatibility modules.
- `divi-5/` contains the dedicated Divi 5 server registration and Visual Builder assets.

This keeps the shared mapping/query logic in the plugin core while letting each Divi generation use the architecture Elegant Themes is currently documenting.

## Shortcodes

```text
[rubicon_maps id="storemap" category="retail,wholesale" region="houston"]
[rubicon_maps_list id="storemap" category="retail,wholesale" region="houston"]
```

Use a shared `id` to keep the map and list synced.

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

## Current v1 stance

- Divi module support is a first-class requirement.
- Leaflet/OpenStreetMap is the supported provider path for v1.
- Google Maps parity is planned for a later version.
- CSV import/export remains in scope for v1 but is not complete in this snapshot.

## Release notes

See [CHANGELOG.md](CHANGELOG.md) for the full version history.
