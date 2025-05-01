# 🗺️ Rubicon Maps

Rubicon Maps is a powerful WordPress plugin for creating interactive, category-based maps using **Leaflet.js** or **Google Maps**. Perfect for Divi users and developers, it supports clustering, regions, CSV import/export, REST API, and more.

> **Version:** 0.3.7  
> **License:** GPLv2 or later  
> **Status:** Pre-1.0 – Actively developing core features  
> **Tested up to:** WordPress 6.8.1

---

## ✨ Key Features

- 📍 Custom Post Type: `rubicon_maps_location`
- 🏷️ Taxonomies: `rubicon_maps_category`, `rubicon_maps_region`
- 🗺️ Interactive maps with clustering & filtering
- 🔧 Plugin settings for map provider, zoom, scroll, default center
- 🧩 Divi module integration
- 🌐 REST API: `/wp-json/rubicon-maps/v1/locations`
- 📥 CSV import/export
- 🌎 Multilingual and RTL support
- 🧹 Uninstall hook: cleans up all data
- 🚦 Settings page with tabbed interface (General, Display, APIs, Regions)
- ⚙️ Uses PSR-4 namespaced autoloading via Composer

---

## 🚀 Shortcodes

```php
[rubicon_maps id="storemap" category="retail,wholesale" provider="leaflet"]
[rubicon_maps_list id="storemap" category="retail,wholesale"]
```

- Use the same id to link map and list components
- Supports multiple categories and providers

⸻

## 🧩 Divi Modules

- Rubicon Map – displays interactive map
- Rubicon Location List – clickable location list
- Shared ID syncs map and list dynamically

⸻

## 🧪 Developer Info

- 🔌 REST API endpoints and filters
- 📦 PSR-4 Composer autoloading (RubiconMaps\\)
- ⚙️ Hooks: create/update/delete actions
- 🧠 Transient-based caching
- 🧼 Uninstall hook cleanup
- 🗃️ Custom MetaBoxes for location metadata

⸻

## ⚙️ Plugin Settings

Accessible under Rubicon Maps → Settings in the WP Admin sidebar.

- 🗺️ Default map provider, zoom, height/width
- 🔍 Toggle scroll/zoom/double-click
- 📍 Cluster toggle and tile provider URL
- 🗂️ Region definitions (lat/lng/zoom)

⸻

## 📂 Folder Structure

rubicon-maps/
├── assets/
├── src/
│ ├── Admin/
│ ├── PostType/
│ ├── Rest/
│ ├── Shortcodes/
│ ├── Divi/
│ └── Taxonomy/
├── rubicon-maps.php
├── composer.json
├── README.md
└── readme.txt

⸻

## 📦 Changelog

### v0.3.7 - 2025-05-02

- ✅ Added uninstall.php for clean deletion
- 🎨 Restructured settings UI using Open User Map style
- 🌍 Updated Google Maps / Leaflet initialization
- 🐞 Fixed settings default fallback bugs

### v0.3.5

- 🔧 Fixed [rubicon_maps] and [rubicon_maps_list] output/rendering
- ✅ Verified plugin structure and PSR-4 loading
- 🚀 Improved frontend loader logic

### v0.3.2 (2024-04-29)

- 🧩 Introduced Divi modules
- 🔗 ID-based map/list synchronization

### v0.3.1

- ✂️ Split into separate [rubicon_maps] and [rubicon_maps_list]
- 🗂️ Multi-category support
- 🔄 Frontend linking enhancements

### v0.3.0

- 🌐 REST API filtering by category
- 🖼️ Category marker icon fallback
- 📝 WYSIWYG popups

⸻

## 👨‍💻 Development

```bash
composer install
```

- Minimum PHP: 7.4
- Recommended WP: 6.8.1+

⸻

👤 Author

Rubicon
GitHub: [https://github.com/rubicon](https://github.com/rubicon)  
Website: [https://rubicontv.com](https://rubicontv.com)
