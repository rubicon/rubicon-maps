=== Rubicon Maps ===
Contributors: rubicon
Tags: maps, leaflet, locations, directory, Divi, shortcodes
Requires at least: 5.8
Tested up to: 6.9.4
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Rubicon Maps is a Divi-first mapping plugin that helps you manage real locations without turning setup into a second job, with linked map/list rendering, CSV import/export, REST support, and Leaflet-based frontend maps.

== Description ==

Rubicon Maps allows you to create interactive, category-based maps with linked location lists, admin-friendly location management, and frontend filtering. It ships with shortcode, REST, CSV, and Divi Builder integration, including dedicated runtime layers for both Divi 4 and Divi 5.

Compatibility for v1.0.0:
* Requires WordPress 5.8 or newer
* Tested through WordPress 6.9.4
* Supports Divi 4.x and Divi 5.x
* Verified in the current release cycle with Divi 5.1.0

== Features ==
* Dedicated Divi 4 and Divi 5 module runtimes plus shortcodes
* WordPress-native location entry using title, excerpt, content, featured image, and structured metadata
* Leaflet maps with OpenStreetMap tiles
* Structured location metadata
* Map/list filtering by category, region, or explicit location IDs
* WP REST API integration
* CSV import/export for locations
* Server-side geocoding helper for admin location entry
* Settings for provider defaults, center, zoom, map height, and tile URL
* Uninstall cleanup

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/rubicon-maps/`
2. Activate through the 'Plugins' menu
3. Visit “Rubicon Maps → Settings” to configure default map options
4. Add locations under “Rubicon Maps → Locations”
5. Use “Rubicon Maps → Import / Export” for bulk CSV workflows when needed

== Shortcodes ==

**[rubicon_maps]**  
Display a map with optional filters.  
Example:  
`[rubicon_maps lat="40.73" lng="-73.93" zoom="11" category="retail,events" region="new-york"]`

**[rubicon_maps_list]**  
Display a clickable location list that synchronizes with the map.  
Example:  
`[rubicon_maps_list category="retail" region="brooklyn"]`

== Frequently Asked Questions ==

= Can I use both Leaflet and Google Maps? =  
Leaflet/OpenStreetMap is the supported provider path for v1.0.0. Additional provider work is future scope and not part of the v1.0.0 support promise.

= Which WordPress and Divi versions are supported? =
Rubicon Maps requires WordPress 5.8 or newer and has been tested through WordPress 6.9.4. It supports Divi 4.x and Divi 5.x, and the current v1 release cycle was verified with Divi 5.1.0.

= Can I use map and list separately? =  
Yes — use separate shortcodes or Divi modules and link them via a shared `id`.

= Is it compatible with Multisite? =  
Multisite has not been verified as part of the v1.0.0 release scope.

= Does it support custom fields? =  
It uses native WordPress title, excerpt, content, thumbnail, and custom metadata for structured location data.

== Screenshots ==

1. Location list and map module
2. Settings page with provider and default map controls
3. Custom post type editor for Locations
4. Divi module example with Rubicon Maps

== Changelog ==

= 1.0.0 - 2026-03-18 =
* Added CSV import/export for canonical location transfers
* Added branded admin panels, category icon previews, and a server-side geocoding helper
* Added release packaging scripts and repository process docs for maintained releases
* Added a canonical frontend map config builder with test coverage
* Improved frontend map behavior for tile URLs, popup behavior, marker clustering, safer popup output, and map bounds fitting
* Added fixed-height synced list behavior, cleaner default list presentation, and admin address autocomplete
* Maintained dedicated Divi 4 and Divi 5 runtimes with linked map/list modules

= 0.5.0 - 2026-03-16 =
* Added dedicated Divi 5 Visual Builder modules for Rubicon Map and Rubicon Location List
* Kept Divi 4 support as a compatibility layer instead of overloading one module path
* Added a buildable `divi-5/visual-builder` package and compiled builder bundle
* Updated source-control rules to keep Composer `vendor/` out of the repo

= 0.4.0 - 2026-03-16 =
* Canonicalized plugin settings, taxonomy slugs, and REST filters
* Reconnected map/list rendering through shared frontend renderers
* Fixed Divi module shortcode targets and linked map/list behavior
* Added structured address formatting and multi-instance frontend JavaScript

= 0.3.5 - 2025-05-01 =

* Verified and corrected all plugin files for completeness and autoload compliance.
* Added missing `SettingsHelper` class for safe access to plugin options.
* Refactored shortcodes:
  - `[rubicon_maps]`: renders map with markers.
  - `[rubicon_maps_list]`: list rendering scaffold added.
* Fixed shortcode rendering bug where shortcode text appeared instead of output.
* Ensured proper loading of Google Maps or Leaflet assets only when used.
* Improved Loader initialization to always register shortcodes on frontend.
* Updated PSR-4 autoload configuration and rebuilt `composer.json` autoload index.
* Verified inclusion of all frontend assets (JS, CSS, leaflet icons).
* Enhancement: Address autocomplete using OSM/Nominatim
* Enhancement: Classic editor for `rubicon_maps_location`
* Enhancement: Admin UI label improvements ("Add Location")
* Enhancement: Featured image fallback for logo/marker
* Enhancement: Added support for crimson location icon

= 0.3.4 - 2025-05-01 =
* Feature: Redesigned settings page based on Leaflet Map UI
* Feature: Added structured address fields (street, city, etc.)
* Feature: Added marker icon upload with preview
* Feature: Classic editor support (Gutenberg disabled)
* Feature: Settings allow Google vs OSM autocomplete
* Fix: Menu title says “Rubicon Maps” and “Add Location”

= 0.3.3 - 2025-04-30 =
* Feature: Add Region taxonomy
* Feature: Add `[rubicon_maps]` and `[rubicon_maps_list]` shortcodes
* Feature: Updated admin settings page with autocomplete provider (Nominatim or Google)
* Enhancement: New shortcode ID linking for map + list interaction
* Dev: Restructured plugin with PSR-4 autoloading support
* Improved frontend loader logic

= v0.3.2 (2024-04-29) =

* Introduced Divi modules
* ID-based map/list synchronization

= 0.3.1 =
* Split into two shortcodes: `[rubicon_map]` and `[rubicon_location_list]`
* Enabled syncing between map and list via `id` attribute
* Added category filtering to both shortcodes
* JavaScript now supports multiple instances and interactions
* Multi-category support
* Frontend linking enhancements

= 0.3.0 =
* Added REST API filtering via `?category=`
* Shortcode supports `[rubicon_location_list category="x"]`
* Map/list rendering separated, interactive behavior initialized
* Category-level marker icon and popup defaults

= 0.2.0 =
* Dynamic frontend loader with Leaflet/Google support
* REST API endpoint and shortcode
* Location CPT + Category taxonomy + Settings page

== Upgrade Notice ==

= 1.0.0 =
Delivers the first stable Rubicon Maps release with Leaflet-first map/list behavior, CSV workflows, Divi 4 and Divi 5 support, admin geocoding, clustering, and release-ready packaging.

== License ==

GPLv2 or later
