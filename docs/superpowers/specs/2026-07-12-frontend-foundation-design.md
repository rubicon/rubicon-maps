# Frontend Map + List UI Foundation (Phase 1) — Design Spec

**Date:** 2026-07-12
**Status:** Draft for review
**Issue:** #10
**Related:** #6 (phase 2: embedded + cross-page list), #8 (native-block core), #9 (test modernization), #5 (phase 3: search/filter, parked)

## 1. Context

Rubicon Maps renders a frontend map and a linked location list through shared PHP renderers (`MapRenderer`, `LocationListRenderer`) consumed by the Divi 5 module and WordPress shortcodes, driven by `assets/js/rubicon-maps-frontend.js` and `assets/css/rubicon-maps-frontend.css`. The code hygiene is good (escaping, keyboard basics, i18n on the PHP side), but the design is minimal and has real gaps:

- No **loading** state — the async REST fetch leaves the map blank, then markers pop in; synced lists sit empty.
- **Silent failure** — `fetchLocations().catch(console.error)`: a failed REST call shows the visitor an empty map with no message.
- No frontend **design tokens** — hardcoded hex, disconnected from the admin token system; no dark mode; no theme deference.
- **Minimal owned components** — default Leaflet markers, basic circle clusters, plain popups, subtle list active-state.
- **i18n gap** — JS strings are hardcoded English.
- **Accessibility gaps** — no `aria-live` for state changes, no `prefers-reduced-motion` handling.

## 2. Goals / Non-goals

**Goals**
- A unified, theme-deferential design-token system across admin and frontend, with a measured Rubicon identity and dark-mode support.
- Designed, accessible loading / error / empty states (closing the silent-failure gap).
- Elevated owned components: list states, map chrome, branded marker, cluster ramp, popups; opt-in list thumbnails.
- WCAG 2.2 AA, including the target-size criterion; full JS i18n.
- Identical result via the Divi 5 module and shortcodes, with Visual Builder preview parity.

**Non-goals (this phase)**
- No change to how modules compose (embedded list, cross-page list, detail-page links are phase 2 / #6).
- No native-block rebuild (#8) — this polishes the existing shared render path.
- No search/filter/legend (#5, parked).
- No new test infrastructure beyond extending existing PHP tests (full CI/E2E is #9).

## 3. Design

### 3.1 Theming & tokens
- A semantic CSS-custom-property layer scoped under `.rubicon-maps` and `.rubicon-location-list`, so nothing leaks into the host theme.
- **Theme-deferential:** visitor-facing type/color inherit from the host theme (`inherit`/`currentColor`); the plugin owns only chrome values (accent, surface/hover/active, borders, marker/cluster colors, popup surface).
- **Dark mode** via `prefers-color-scheme: dark`, plus a `[data-rubicon-theme="light|dark"]` override hook. Contrast validated at WCAG 2.2 AA in both modes.
- Exact palette/values are produced and contrast-validated at implementation via the `ui-ux-pro-max` skill and recorded in `design-system/MASTER.md`. This spec fixes the token architecture, not the hex values.

### 3.2 States
- **Loading** (fetch in flight): a lightweight indicator over the map canvas (fixed height → no layout shift) and **skeleton rows** in synced lists, replaced on arrival.
- **Error** (fetch fails): an inline, non-blocking message in the map and list areas plus a **Retry** control that re-runs the fetch; still logs to console. Replaces the current silent `catch`.
- **Empty:** keep the "no locations matched" case, restyled through tokens; copy localized.
- Cross-cutting: token-styled, consistent map/list treatment; shimmer/spinner respect `prefers-reduced-motion`; loading/error announced via `aria-live="polite"` / `role="alert"`.

### 3.3 Owned components
- **List items:** keep title/address/excerpt; add a clear focus ring distinct from hover and a stronger active state (accent left-border + surface tint). Structure leaves room for a phase-2 "View details" affordance.
- **Map chrome:** token-based canvas framing; Leaflet zoom controls lightly restyled to tokens without overriding behavior; attribution untouched (license requirement).
- **Markers:** a branded default marker (accent SVG pin) replacing Leaflet blue; per-location custom icons still honored.
- **Clusters:** token-based size/color ramp (small/medium/large tiers by count) with AA-contrast count text.
- **Popups:** token-styled surface (padding, radius, shadow, config-driven max-width), clear title/address/excerpt hierarchy, shared class structure across providers, room reserved for the phase-2 detail link.
- **List thumbnails:** opt-in list setting (off by default) surfacing the location's native WP thumbnail.

### 3.4 Accessibility & i18n
- Target **WCAG 2.2 AA**, including **Target Size >=24px (2.5.8)** for list items, markers, and map controls, and focus-appearance/not-obscured.
- **The list is the accessible index of the map:** the keyboard/screen-reader path to any marker is its list item (`role="button"` + `aria-label`). Formalize and document this rather than trying to make Leaflet markers individually focusable.
- Announce state via a polite `aria-live` region ("Showing map location: X" on activation, plus loading/error).
- `prefers-reduced-motion` disables shimmer and Leaflet pan/zoom animation (`animate:false`).
- **i18n:** inject all user-facing JS strings via `wp_localize_script` (a translated strings object). Load the text domain on `init` (not `plugins_loaded`); if the plugin targets WordPress.org, omit `load_plugin_textdomain()` entirely (core auto-loads).

### 3.5 Delivery across consumers
- The PHP renderers + shared CSS/JS are the single source; the Divi 5 module (SSR) and WP shortcodes both call them, so phase-1 changes apply everywhere with no duplication.
- Conditional asset loading stays (enqueue only when a map/list renders).
- **Visual Builder parity:** the token/component CSS is honored inside the Divi 5 VB canvas, and the React preview renders the same class structure, so a builder editing the module sees the real styled result.

## 4. Units (for isolation and testability)

- **Token layer** (`assets/css`): semantic variables + light/dark; consumed by all components.
- **State controller** (frontend JS): owns loading/error/empty transitions and `aria-live` announcements; single place that wraps the fetch lifecycle.
- **Marker/cluster styling** (CSS + SVG + JS icon options): branded default, custom-icon passthrough, cluster ramp.
- **List component** (`LocationListRenderer` + list CSS/JS): item markup, states, optional thumbnail; shared by standalone and (phase 2) embedded use.
- **Popup builder** (JS `buildPopupHtml`): shared structure/classes across providers.
- **i18n strings bag** (PHP `wp_localize_script`): one localized object consumed by the JS.

## 5. Verification

- **Automated:** existing pure-PHP tests stay green + `php -l`; extend `LocationListRenderer`/`MapRenderer` tests to assert new state markup, class hooks, and ARIA attributes; run PHPCS (WPCS) + PHPStan on changed files (#9 phase A).
- **Behavioral/visual (local WP + Divi 5 site, using the Divi Developer License):** render via both shortcode and Divi 5 module; verify loading → resolve, error + Retry (simulated REST failure), empty, dark mode (light + dark), branded marker/cluster/popup, list active/focus + keyboard + `aria-live`, VB-preview parity.
- **Accessibility:** axe/Lighthouse for WCAG 2.2 AA + manual screen-reader and reduced-motion passes; confirm target size >=24px and focus visibility.
- **Responsive:** 375px and landscape.
- The `ui-ux-pro-max` pre-delivery checklist (contrast, touch targets, states, dark mode) is the visual QA gate.

## 6. Dependencies & sequencing

- Best sequenced after #4 (rtv_rm_ rename) merges and #7 (remove Divi 4) lands, so this builds on the final identifiers and a Divi-5-only tree. The renderers/CSS/JS are builder-agnostic, so the work itself does not depend on Divi 4 removal, but doing it after avoids churn.
- Requires a local WP + Divi 5 dev site (Local) for behavioral verification.

## 7. Out of scope (tracked elsewhere)

- Embedded list in the Map module, cross-page list, detail-page links: #6.
- Native-block core architecture: #8.
- PHPUnit/Playwright CI: #9.
- Frontend search/filter/legend: #5.
