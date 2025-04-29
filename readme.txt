=== Rubicon Maps ===
Contributors: rubicon  
Tags: maps, divi, leaflet, google maps, shortcode, custom post type  
Requires at least: 5.6  
Tested up to: 6.7  
Stable tag: 0.2.0  
Requires PHP: 7.4  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Rubicon Maps adds interactive map modules and shortcodes for Divi with support for Google and Leaflet maps, dynamic marker loading, clustering, categories, and more.

== Description ==

Rubicon Maps is a powerful location mapping plugin for WordPress, built specifically for Divi. It provides flexible map modules and a custom location content type, ideal for directories, stores, and listings.

- Custom post type: Location
- Location categories (hierarchical taxonomy)
- Frontend shortcode and Divi module: `[rubicon_location_list]`
- REST API endpoint: `/wp-json/rubicon-maps/v1/locations`
- Leaflet and Google Maps providers
- Marker clustering, geolocation, and search
- Settings page for global defaults
- AJAX + dynamic map rendering

== Installation ==

1. Upload `rubicon-maps` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Visit `Settings > Rubicon Maps` to configure.
4. Use the `[rubicon_location_list]` shortcode or the Divi builder module.

== Frequently Asked Questions ==

= Can I use both Leaflet and Google Maps? =  
Yes — choose your default in Settings, or override per page/module.

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

= 0.2.0 =
* Added dynamic marker loading via REST API
* Added Leaflet map support and default assets
* Introduced shortcode `[rubicon_location_list]`
* Modularized with PSR-4 and Composer autoloading
* Created settings page and CPT/taxonomy registration
* Cleaned legacy code from Supreme Maps
* Reset SemVer and folder structure

== Upgrade Notice ==

= 0.2.0 =
This is a complete rewrite. If upgrading from pre-1.0 versions, remove the previous plugin before installing.

== License ==

GPLv2 or later
