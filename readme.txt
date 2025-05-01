=== Rubicon Maps ===
Contributors: rubicon
Tags: maps, leaflet, locations, directory, Divi, shortcodes
Requires at least: 5.8
Tested up to: 6.8.1
Requires PHP: 7.4
Stable tag: 0.3.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Rubicon Maps is a powerful Divi-compatible mapping plugin with Leaflet and Google Maps support, clustering, geolocation, categories, shortcode & Divi module.

== Description ==

Rubicon Maps allows you to create interactive, category-based maps with clustering, custom markers, and frontend filtering. Built for performance and extensibility with full shortcode, REST, and Divi Builder integration.

== Features ==
* Divi Modules and Shortcodes
* Classic Editor & WP Admin UI
* Leaflet Maps with OpenStreetMap tiles
* Google & OSM Autocomplete Provider
* Marker Icons Per Location or Category
* Popup WYSIWYG Editor (uses post content)
* Clustering, zoom, and scroll toggles
* Location submission
* Custom categories and regions
* Geolocation "locate me" button
* Fully styled Divi modules
* WP REST API and WPGraphQL integration
* CSV import/export
* Classic editor location entry
* RTL, A11y, WCAG 2.1 AA compatible
* Uninstall cleanup

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/rubicon-maps/`
2. Activate through the 'Plugins' menu
3. Visit “Rubicon Maps → Settings” to configure default map options
4. Add locations under “Rubicon Maps → All Locations”

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
Yes — choose your default in Settings, or override per module/shortcode.

= Can I use map and list separately? =  
Yes — use separate shortcodes or Divi modules and link them via a shared `id`.

= Is it compatible with Multisite? =  
Yes, full support for multisite and network activation.

= Does it support custom fields? =  
Yes — ACF integration is supported via hooks.

== Screenshots ==

1. Location list and map module
2. Settings page with API and clustering controls
3. Custom post type editor for Locations
4. Divi module example with Rubicon Maps

== Changelog ==

= 0.3.7 - 2025-05-02 =
* Added full uninstall support via `uninstall.php`
* Introduced tabbed settings page with default map configuration
* Added options for Google API Key, Mapbox tiles, default zoom/center
* Updated README formatting
* Bug fixes and cleanup

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

= 0.3.2 =
Add Divi module support for both Rubicon Map and Location List

== License ==

GPLv2 or later
