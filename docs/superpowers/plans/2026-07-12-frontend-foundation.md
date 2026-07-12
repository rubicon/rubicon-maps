# Frontend Map + List UI Foundation (Phase 1) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Bring the frontend map + list to a top-notch, accessible, Divi-5-native standard by polishing the existing shared render path — unified tokens, loading/error/empty states, branded components, WCAG 2.2 AA, and full JS i18n — with no architecture change.

**Architecture:** The PHP renderers (`MapRenderer`, `LocationListRenderer`) and shared `assets/css` + `assets/js` are the single source consumed by the Divi 5 module and WordPress shortcodes. This plan adds a semantic CSS-token layer, designed states, and component polish on top of that path. Exact token/marker values are generated via `ui-ux-pro-max` and referenced by semantic name.

**Tech Stack:** PHP 8.1+ (PSR-4 `RubiconMaps\`), vanilla frontend JS, Leaflet + MarkerCluster, CSS custom properties. Pure-PHP test scripts (`php tests/php/<Name>Test.php`). Local WP + Divi 5 (Divi Developer License) for behavioral/visual verification.

## Global Constraints

- Divi 5 only. No Divi 4, no `et_pb_*`.
- Internal identifiers use the `rtv_rm_` / `rtv-rm-` prefix. Public-facing unchanged: slug/text-domain `rubicon-maps`, REST `rubicon-maps/v1`, shortcodes `[rubicon_maps]`/`[rubicon_maps_list]`.
- Theme-deferential: inherit host type/color where visitor-facing; the plugin owns only chrome (accent, surfaces, states, marker/cluster, popup).
- WCAG 2.2 AA, including Target Size ≥24px (2.5.8) and focus appearance/not-obscured.
- i18n: all user-facing JS strings via `wp_localize_script`; load the text domain on `init` (drop `load_plugin_textdomain()` if/when targeting WordPress.org).
- No layout shift from states (map canvas has a fixed height).
- Reduced motion: `prefers-reduced-motion` disables shimmer and Leaflet pan/zoom animation.
- Conventional Commits, signed commits, one focused commit per task step-group.
- Verify each task: `composer dump-autoload`; `php -l` on changed PHP; run every touched `tests/php/*.php`; behavioral/visual in local WP + Divi 5.

---

## File Structure

- Create `design-system/MASTER.md` — generated design-system of record (palette, semantic tokens, states, component specs) from `ui-ux-pro-max`.
- Create `assets/css/rubicon-maps-tokens.css` — semantic CSS-variable layer (light + `prefers-color-scheme: dark` + `[data-rubicon-theme]` override), scoped under `.rubicon-maps`, `.rubicon-location-list`.
- Create `assets/img/rtv-rm-marker.svg` — branded default marker (accent), generated via `ui-ux-pro-max`.
- Modify `src/Frontend/FrontendAssetManager.php` — enqueue the tokens stylesheet; add a localized strings bag; keep conditional loading.
- Modify `src/Frontend/LocationListRenderer.php` — state markup hooks, ARIA, opt-in thumbnail, localized empty copy.
- Modify `src/Frontend/MapRenderer.php` — loading/error state container + ARIA on the map root.
- Modify `assets/js/rubicon-maps-frontend.js` — state controller (loading/error/empty), aria-live announcements, Retry, reduced-motion, localized strings, branded/cluster marker options, thumbnail in synced list items.
- Modify `assets/css/rubicon-maps-frontend.css` — consume tokens; list focus/active states; map chrome; state styling; popup/cluster styling; dark mode.
- Modify `assets/css/rubicon-maps-admin.css` — consume the shared tokens (align admin with the same layer).
- Modify the i18n bootstrap (locate current `load_plugin_textdomain` call; move to `init`) — likely `src/Loader.php` or `src/Support/Plugin.php` (confirm in Task 2).
- Modify `tests/php/LocationListRendererTest.php`, add `tests/php/FrontendStringsTest.php` — assert state markup/ARIA and the localized-strings contract.

---

## Task 1: Generate the design-system tokens

**Files:**
- Create: `design-system/MASTER.md`
- Create: `assets/css/rubicon-maps-tokens.css`
- Create: `assets/img/rtv-rm-marker.svg`

**Interfaces:**
- Produces: semantic CSS variables consumed by all later CSS tasks. Required names (light + dark values filled by generation): `--rtv-rm-accent`, `--rtv-rm-accent-contrast`, `--rtv-rm-surface`, `--rtv-rm-surface-hover`, `--rtv-rm-surface-active`, `--rtv-rm-border`, `--rtv-rm-text` (defaults to `currentColor`), `--rtv-rm-text-muted`, `--rtv-rm-focus-ring`, `--rtv-rm-cluster-sm/md/lg`, `--rtv-rm-popup-surface`, `--rtv-rm-skeleton`. Marker SVG at the path above.

- [ ] **Step 1: Run ui-ux-pro-max design-system generation**

Run the skill's generator for a WordPress/Divi map plugin, theme-deferential with a Rubicon accent, and `--persist`:
`python3 <ui-ux-pro-max>/scripts/search.py "wordpress divi map plugin admin+frontend, theme-deferential, accent identity, dark mode" --design-system --persist -p "Rubicon Maps"`

- [ ] **Step 2: Author `assets/css/rubicon-maps-tokens.css`** from the generated system: define all semantic variables above under `.rubicon-maps, .rubicon-location-list` for light; add a `@media (prefers-color-scheme: dark)` block and `:root[data-rubicon-theme="dark"]`/`[data-rubicon-theme="light"]` overrides. `--rtv-rm-text` defaults to `currentColor`; type inherits.

- [ ] **Step 3: Contrast-validate** every foreground/background token pair at WCAG AA in light and dark (use the ui-ux-pro-max validator / an axe contrast check). Record pass in `design-system/MASTER.md`.

- [ ] **Step 4: Create `assets/img/rtv-rm-marker.svg`** — accent pin using `--rtv-rm-accent` (inline `fill`), 36×36 viewport to match current Leaflet icon sizing.

- [ ] **Step 5: Commit**

```bash
git add design-system/MASTER.md assets/css/rubicon-maps-tokens.css assets/img/rtv-rm-marker.svg
git commit -S -m "feat: add frontend design tokens and branded marker"
```

**Verification:** tokens file parses (load in a browser, inspect `getComputedStyle`); contrast recorded AA in both modes.

---

## Task 2: Enqueue tokens, localize strings, load text domain on init

**Files:**
- Modify: `src/Frontend/FrontendAssetManager.php`
- Modify: i18n bootstrap (confirm location via `grep -rn "load_plugin_textdomain" src`)
- Test: `tests/php/FrontendStringsTest.php` (create)

**Interfaces:**
- Produces: `FrontendAssetManager::frontendStrings(): array` returning the localized bag with keys `emptyStandalone`, `emptySynced`, `loading`, `error`, `retry`, `focusOnLocation` (the last a sprintf template with `%s`). Enqueued as JS object `rubiconMapsStrings` on the frontend handles. Tokens stylesheet handle `rtv-rm-tokens` enqueued as a dependency of `rtv-rm-frontend` and `rtv-rm-admin`.

- [ ] **Step 1: Write the failing test** — `tests/php/FrontendStringsTest.php`

```php
<?php
declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';
if (!defined('ABSPATH')) { define('ABSPATH', __DIR__); }
if (!function_exists('__')) { function __($t, $d = 'default') { return $t; } }

use RubiconMaps\Frontend\FrontendAssetManager;

$strings = FrontendAssetManager::frontendStrings();
$required = ['emptyStandalone', 'emptySynced', 'loading', 'error', 'retry', 'focusOnLocation'];
foreach ($required as $key) {
    if (!array_key_exists($key, $strings) || '' === (string) $strings[$key]) {
        fwrite(STDERR, "Missing localized string: {$key}" . PHP_EOL);
        exit(1);
    }
}
if (!str_contains($strings['focusOnLocation'], '%s')) {
    fwrite(STDERR, 'focusOnLocation must be a sprintf template with %s' . PHP_EOL);
    exit(1);
}
echo 'FrontendStringsTest passed.' . PHP_EOL;
```

- [ ] **Step 2: Run it, verify it fails**
Run: `php tests/php/FrontendStringsTest.php`
Expected: FAIL — `frontendStrings` not defined.

- [ ] **Step 3: Implement `frontendStrings()`** in `FrontendAssetManager` returning each key wrapped in `__( ..., 'rubicon-maps' )`; `focusOnLocation` = `__( 'Focus map on %s', 'rubicon-maps' )`. In the existing enqueue methods, register/enqueue `rtv-rm-tokens` (the tokens stylesheet) and add it to the `deps` of the frontend/admin styles; `wp_localize_script` the frontend script handle with `rubiconMapsStrings => self::frontendStrings()`.

- [ ] **Step 4: Run test, verify it passes**
Run: `php tests/php/FrontendStringsTest.php` → PASS.

- [ ] **Step 5: Move text-domain load to `init`** — locate the current `load_plugin_textdomain()` call; ensure it is hooked on `init` (not `plugins_loaded`). Add a comment noting it may be dropped if targeting WordPress.org.

- [ ] **Step 6: Verify + commit**
Run: `composer dump-autoload && php -l src/Frontend/FrontendAssetManager.php && php tests/php/FrontendStringsTest.php`

```bash
git add src/Frontend/FrontendAssetManager.php tests/php/FrontendStringsTest.php <i18n-bootstrap-file>
git commit -S -m "feat: localize frontend JS strings, enqueue tokens, load textdomain on init"
```

---

## Task 3: List renderer — state markup, ARIA, opt-in thumbnail

**Files:**
- Modify: `src/Frontend/LocationListRenderer.php`
- Test: `tests/php/LocationListRendererTest.php`

**Interfaces:**
- Consumes: `FrontendAssetManager` strings (Task 2).
- Produces: list root gains `data-state="ready|loading|empty|error"` and an `aria-live="polite"` status node (`.rubicon-location-list__status`); each item optionally renders `.rubicon-location-list__thumb` when `atts['show_thumbnail']` is truthy and a thumbnail exists; empty copy uses the localized string.

- [ ] **Step 1: Write failing test additions** to `tests/php/LocationListRendererTest.php`: render with no matches and assert the output contains `data-state="empty"` and an element with `aria-live="polite"`; render a synced list (`sync_id` set) and assert the container carries `data-state="loading"` initially; render with `show_thumbnail` true and a location having a `thumbnail_url` and assert `rubicon-location-list__thumb` appears. (Follow the file's existing assertion style.)

- [ ] **Step 2: Run it, verify it fails**
Run: `php tests/php/LocationListRendererTest.php` → FAIL on the new assertions.

- [ ] **Step 3: Implement** in `LocationListRenderer::render()` / `renderListItem()`: add `data-state` (`loading` when synced, `empty` when standalone+no results, else `ready`); add the `aria-live` status node; when `show_thumbnail` truthy and `location['thumbnail_url']` present, output an `img.rubicon-location-list__thumb` with `esc_url` + empty `alt` (decorative; name is in the title). Use the localized empty string.

- [ ] **Step 4: Run test, verify passes** → PASS.

- [ ] **Step 5: Verify + commit**
Run: `php -l src/Frontend/LocationListRenderer.php && php tests/php/LocationListRendererTest.php`
```bash
git add src/Frontend/LocationListRenderer.php tests/php/LocationListRendererTest.php
git commit -S -m "feat: list renderer state markup, aria-live, opt-in thumbnail"
```

---

## Task 4: Map renderer — state container + ARIA

**Files:**
- Modify: `src/Frontend/MapRenderer.php`
- Test: `tests/php/MapInstanceConfigBuilderTest.php` (or a small new `MapRendererMarkupTest.php` if the config test is unsuitable)

**Interfaces:**
- Produces: map root gains `data-state="loading"` initially and contains a `.rubicon-maps__status[aria-live="polite"]` node and a `.rubicon-maps__canvas` (unchanged height behavior). Consumed by the JS state controller (Task 5) by class name.

- [ ] **Step 1: Write failing test** asserting `MapRenderer::render([])` output contains `data-state="loading"`, `.rubicon-maps__status`, and `aria-live="polite"`.
- [ ] **Step 2: Run, verify fails.**
- [ ] **Step 3: Implement** the status node + `data-state="loading"` on the map root in `MapRenderer::render()`; no change to the JSON config payload.
- [ ] **Step 4: Run, verify passes.**
- [ ] **Step 5: Verify + commit**
```bash
git add src/Frontend/MapRenderer.php tests/php/MapRendererMarkupTest.php
git commit -S -m "feat: map renderer loading state container and aria-live"
```

---

## Task 5: JS state controller (loading/error/empty, retry, aria-live, reduced-motion, i18n)

**Files:**
- Modify: `assets/js/rubicon-maps-frontend.js`

**Interfaces:**
- Consumes: `window.rubiconMapsStrings` (Task 2); `data-state` + `.rubicon-maps__status` / `.rubicon-location-list__status` (Tasks 3-4).
- Produces: on init sets `data-state="loading"` and announces `strings.loading`; on fetch success sets `ready`/`empty` and (synced) renders items incl. thumbnail; on fetch failure sets `error`, renders `strings.error` + a Retry button that re-runs `fetchLocations`; activation announces `sprintf(strings.focusOnLocation, title)`; all hardcoded English removed; when `matchMedia('(prefers-reduced-motion: reduce)')` matches, Leaflet uses `animate:false` and shimmer is skipped.

- [ ] **Step 1: Replace hardcoded strings** ("No locations matched this synced map.", the built `aria-label`s) with `window.rubiconMapsStrings` lookups; add a tiny `sprintf`-style helper for `focusOnLocation`.
- [ ] **Step 2: Add state helper** `setState(root, state)` that toggles `data-state` and writes the status node's text from `strings`.
- [ ] **Step 3: Wire loading/error** around `fetchLocations()`: set loading before, `error` + Retry in `.catch` (replacing the silent `console.error`-only path — keep the console log too), `ready`/`empty` on success.
- [ ] **Step 4: Reduced-motion + announcements** — read `prefers-reduced-motion`; pass `animate:false` to Leaflet set/fly calls when reduced; announce active location via the status node.
- [ ] **Step 5: Marker/cluster options** — default marker uses `assets/img/rtv-rm-marker.svg` when no per-location icon; cluster `iconCreateFunction` applies the size/color ramp classes (`rtv-rm-cluster--sm/md/lg` by count).
- [ ] **Step 6: Behavioral verification** in local WP + Divi 5: load a map+list page; confirm loading→ready, empty state, and — with DevTools offline / a forced 500 — the error state + working Retry; toggle OS reduced-motion and confirm no fly animation; screen-reader announces the active location.
- [ ] **Step 7: Commit**
```bash
git add assets/js/rubicon-maps-frontend.js
git commit -S -m "feat: frontend state controller, retry, i18n, reduced-motion, branded markers"
```

---

## Task 6: Frontend CSS — tokens, list states, map chrome, state styling, dark mode

**Files:**
- Modify: `assets/css/rubicon-maps-frontend.css`

- [ ] **Step 1: Replace hardcoded hex** with the semantic tokens from Task 1.
- [ ] **Step 2: List item states** — visible focus ring (`--rtv-rm-focus-ring`, ≥2px, not obscured) distinct from hover; active state = accent left-border + `--rtv-rm-surface-active`; ensure item min tap height ≥24px (2.5.8).
- [ ] **Step 3: State styling** — `[data-state="loading"]` skeleton rows (shimmer via `--rtv-rm-skeleton`, gated behind `@media (prefers-reduced-motion: no-preference)`); `[data-state="error"]` message + Retry button; `[data-state="empty"]` styled empty copy; `.rubicon-maps__status`/`.rubicon-location-list__status` visually-hidden but screen-reader available (or shown for error).
- [ ] **Step 4: Map chrome** — token border/radius on `.rubicon-maps__canvas`; lightly style Leaflet zoom controls via tokens without breaking them.
- [ ] **Step 5: Dark mode** — verify all of the above through the dark token set.
- [ ] **Step 6: Visual verification** at 375px + landscape, light + dark; axe/Lighthouse AA.
- [ ] **Step 7: Commit**
```bash
git add assets/css/rubicon-maps-frontend.css
git commit -S -m "feat: token-based frontend styling, states, focus/active, dark mode"
```

---

## Task 7: Marker, cluster, and popup styling

**Files:**
- Modify: `assets/css/rubicon-maps-frontend.css`

- [ ] **Step 1: Cluster ramp** — style `.rtv-rm-cluster--sm/md/lg` (Task 5 classes) with the `--rtv-rm-cluster-*` tokens; count text AA contrast; keep round shape.
- [ ] **Step 2: Popup** — token surface (padding, radius, shadow, config max-width), clear title/address/excerpt hierarchy, shared for both providers; reserve a slot for the phase-2 detail link (styled but empty hook `.rubicon-maps__popup-actions`).
- [ ] **Step 3: Visual verification** — many-marker map shows the ramp; popups match tokens light + dark.
- [ ] **Step 4: Commit**
```bash
git add assets/css/rubicon-maps-frontend.css
git commit -S -m "feat: branded cluster ramp and popup styling"
```

---

## Task 8: Admin token alignment

**Files:**
- Modify: `assets/css/rubicon-maps-admin.css`

- [ ] **Step 1: Point admin at the shared tokens** — replace the admin-local `--rubicon-*` variables with the `--rtv-rm-*` semantic tokens (or alias them) so admin + frontend share one layer; keep admin's richer chrome.
- [ ] **Step 2: Visual verification** in wp-admin (settings, metabox, import/export) light + dark; contrast AA.
- [ ] **Step 3: Commit**
```bash
git add assets/css/rubicon-maps-admin.css
git commit -S -m "refactor: align admin styling with shared rtv_rm tokens"
```

---

## Task 9: Accessibility sweep + PR

**Files:** none new (verification + fixes)

- [ ] **Step 1:** axe + Lighthouse AA pass on a map+list page (light + dark); fix any contrast/name/role gaps.
- [ ] **Step 2:** Manual keyboard pass — list is the accessible index; focus visible and not obscured; `Esc` closes popups; target sizes ≥24px.
- [ ] **Step 3:** Screen-reader pass (VoiceOver) — loading/error/active announcements fire.
- [ ] **Step 4:** VB-preview parity — open the Divi 5 module in the Visual Builder; confirm tokens/components render there.
- [ ] **Step 5:** Full verification: `composer dump-autoload`; `find src divi-5 tests -name '*.php' -print0 | xargs -0 -n1 php -l`; run all `tests/php/*.php`; `cd divi-5/visual-builder && npm ci && npm run build`.
- [ ] **Step 6:** Push branch and open PR `Closes #10` (ask Dax before push/PR).

---

## Self-Review

- **Spec coverage:** §3.1 tokens → T1,T2,T6,T8; §3.2 states → T3,T4,T5,T6; §3.3 components → T5,T6,T7 + thumbnail T3; §3.4 a11y/i18n → T2,T3,T4,T5,T9; §3.5 delivery → T2 (enqueue) + T9 (VB parity); §5 verification → each task + T9.
- **Placeholders:** token/marker VALUES are generated in T1 (a real step), then referenced by defined semantic names — not placeholders. JS/CSS visual tasks use behavioral/visual verification, not fake unit tests, per the spec.
- **Type consistency:** `frontendStrings()` keys (T2) are the exact keys consumed in T5; `data-state` values and status-node classes are consistent across T3/T4/T5/T6.

## Open confirmations for the implementer

- The i18n bootstrap file (Task 2, Step 5) — confirm the current `load_plugin_textdomain` location before editing.
- If `MapInstanceConfigBuilderTest` isn't a fit for markup assertions, create `MapRendererMarkupTest.php` (Task 4).
