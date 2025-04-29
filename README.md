# Rubicon Maps

Rubicon Maps is a powerful, modern, and extensible WordPress plugin that enables interactive, mobile-friendly maps for your Divi-powered websites.

- 🗺️ Supports Google Maps & Leaflet providers
- 📍 Custom post type: `location` with category taxonomy
- 🔍 Marker clustering, radius filtering, search & autocomplete
- ⚡️ Dynamic frontend marker loading via REST API
- 🧩 Divi modules and shortcode support
- 🚀 Built for scale, accessibility, and extensibility

---

## 🔧 Features

- Fully integrated with Divi Builder (RubiconTV modules)
- Shortcode `[rubicon_location_list]` with dynamic map + list
- Settings page for API keys, clustering, zoom, and styling
- Custom post type `location` + hierarchical taxonomy
- Dynamic marker loading via REST API (`/wp-json/rubicon-maps/v1/locations`)
- REST and WPGraphQL support
- ACF, WooCommerce, and Events plugin bridges (in progress)
- Leaflet default icons + popup support
- PSR-4 autoloading with Composer
- WCAG AA accessibility + ARIA + translation-ready
- Multisite compatible

---

## 🧪 Development Setup

```bash
composer install
```

### Recommended WordPress setup:

- WordPress 6.x+
- PHP 7.4+
- Divi Builder enabled
- Enable location CPT from WP Admin > Locations

## 🗂️ Folder Structure

rubicon-maps/
├── assets/
│ ├── css/
│ ├── js/
│ └── leaflet/
├── src/
│ ├── Admin/
│ ├── PostType/
│ ├── Taxonomy/
│ ├── Rest/
│ ├── Shortcodes/
├── rubicon-maps.php
├── composer.json
├── readme.txt
└── README.md

📦 Versioning
Current release: 0.2.0

Reset from v1.0.1 to reflect pre-1.0 development status

Semantic Versioning (SemVer)

🛠️ License
GPLv2 or later — free to use, modify, and redistribute.

## 👤 Author

RubiconTV
[https://github.com/rubico](https://github.com/rubicon)
[https://rubicontv.com/rubicon-maps](https://rubicontv.com/rubicon-maps)
