# Architecture

Rubicon Maps is a Divi-first WordPress mapping plugin. It keeps builder-agnostic
business logic in a shared core and layers the Divi 5 builder integration and
frontend assets on top.

## Top-level layout

```
rubicon-maps.php        Plugin bootstrap: header, RTV_RM_* constants, autoload, Loader
uninstall.php           Uninstall cleanup entry point
src/                    Builder-agnostic WordPress/plugin logic (PSR-4 RubiconMaps\)
divi-5/                 Divi 5 integration (server registration + Visual Builder)
assets/                 Shared frontend/admin CSS and JS, Leaflet assets
tests/php/              Lightweight pure-PHP verification scripts
docs/                   Release notes, process docs, specs/plans
scripts/                Release packaging (package-release.sh)
```

## `src/` — the core

Namespaced `RubiconMaps\` (PSR-4, autoloaded via Composer), grouped by responsibility:

- **`PostType/`, `Taxonomy/`** — register the `rtv_rm_location` custom post type and
  the `rtv_rm_category` / `rtv_rm_region` taxonomies.
- **`Admin/`** — settings page, location metabox, category meta, import/export,
  server-side geocoding, list filters, plugin action links.
- **`Frontend/`** — the render path: `MapRenderer` and `LocationListRenderer` emit
  the map shell and linked list; `MapInstanceConfigBuilder` builds the JSON config;
  `LocationRepository` / `LocationQueryArgsBuilder` query locations;
  `FrontendAssetManager` enqueues assets and localizes strings.
- **`ImportExport/`** — CSV transform/import/export.
- **`Support/`** — `Plugin` (constants + paths + version), `PluginLifecycle`
  (activation/version option), `PluginUpdater` (GitHub Releases auto-update),
  `Loader` (wires everything on load).
- **`Helpers/`** — `SettingsHelper` and small utilities.

## `divi-5/` — the builder layer

- **`divi-5.php`** — bootstrap; gated on `Plugin::isDivi5Enabled()`.
- **`server/`** — PHP module registration (`RubiconMaps\Divi5\`, PSR-4 → `divi-5/server/`).
- **`visual-builder/`** — the React/JSX Visual Builder source, built with webpack
  (`npm run build`) to `visual-builder/build/`. Never hand-edit `build/`.

The map and list are independent modules linked by a shared `sync-id`. Both the
Divi 5 modules and the plugin's WordPress shortcodes render through the **same**
`src/Frontend` renderers, so there is one implementation, not two.

## Frontend runtime

`MapRenderer` outputs a map shell plus a JSON config; `assets/js/rubicon-maps-frontend.js`
boots Leaflet, fetches locations over the REST route `rubicon-maps/v1/locations`,
renders markers (with optional clustering), fits bounds, and keeps any linked list
in sync. Styling flows through a semantic design-token layer
(`assets/css/rubicon-maps-tokens.css`, `--rtv-rm-*`) that is theme-deferential and
supports dark mode; `design-system/MASTER.md` is its system-of-record.

## Updates

`Support/PluginUpdater` implements auto-update against **GitHub Releases**
(`Plugin::UPDATE_*`), reading `/releases/latest` and selecting the
`rubicon-maps-X.Y.Z.zip` asset. Updates require published GitHub Releases with that
asset attached.

## Conventions

Internal identifiers use the `rtv_rm_` / `rtv-rm-` prefix; public-facing slug,
text domain (`rubicon-maps`), REST namespace, and shortcodes are stable. WCAG 2.2
AA is enforced. See [CONTRIBUTING.md](CONTRIBUTING.md).
