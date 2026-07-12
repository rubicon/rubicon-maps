# Rubicon Maps — Frontend Design System

**Project:** Rubicon Maps (Divi 5 WordPress map plugin)
**Scope:** Frontend map + location-list chrome only (marker, cluster, popup, list surfaces, focus states). This is **not** a full site theme — Rubicon Maps inherits host type/color for visitor-facing text and only supplies its own accent, surfaces, and state colors ("chrome accents on top of the host site").
**Generated:** 2026-07-12

## Source generation

Palette/token source material was generated via the ui-ux-pro-max skill:

```
python3 /Users/daxdavis/.claude/plugins/cache/ui-ux-pro-max-skill/ui-ux-pro-max/2.5.0/src/ui-ux-pro-max/scripts/search.py \
  "wordpress divi map plugin admin+frontend, theme-deferential, accent identity, dark mode" \
  --design-system --persist -p "Rubicon Maps" -o .
```

The generator's closest style match was "Dark Mode (OLED) / Data-Dense Dashboard," which returned a dark-only palette anchored on a navy neutral scale (`#0F172A`/`#1E293B`/`#334155`) with an accent of `#2563EB` ("Map dark + route blue" — the generator's own note associating this blue with map/route UI). That output is **dark-only** and assumes a full-page reskin, neither of which fits a theme-deferential Divi plugin, so it is used here as raw palette material rather than copied verbatim: the accent (`#2563EB`, Tailwind `blue-600`) and its neutral navy scale are kept and extended into a matching **light** mode, and every token is re-scoped to component chrome (`.rubicon-maps`, `.rubicon-location-list`) instead of `:root`/full-page classes. No typography, spacing, or shadow tokens from the generator are adopted — the plugin does not own type or spacing; the host theme does.

## Design principles

1. **Theme-deferential.** `--rtv-rm-text` is `currentColor` in every mode — the plugin never sets its own text color; it always inherits the host theme's/Divi's type color. Only surfaces, borders, accent, focus, cluster, and popup chrome are plugin-owned.
2. **One accent identity.** A single blue accent (`route/map blue`) carries brand identity across light and dark, adjusted per-mode for contrast rather than swapped for an unrelated hue.
3. **Dark mode via three mechanisms**, per the task brief: `@media (prefers-color-scheme: dark)` for OS-level preference, plus explicit `:root[data-rubicon-theme="dark"]` / `:root[data-rubicon-theme="light"]` manual overrides so a host page or user toggle can force a mode regardless of OS setting.
4. **WCAG 2.2 AA everywhere.** Every foreground/background token pairing that can render text or a required UI component boundary was checked with the standard WCAG relative-luminance contrast formula (see Contrast validation below) and adjusted until it passed — nothing here was recorded as passing without being measured.

## Palette

| Token | Light | Dark | Role |
|---|---|---|---|
| `--rtv-rm-accent` | `#2563EB` | `#60A5FA` | Brand/interactive accent (buttons, active states, links, default marker fill) |
| `--rtv-rm-accent-contrast` | `#FFFFFF` | `#0F172A` | Text/icon color for content placed directly on `--rtv-rm-accent` |
| `--rtv-rm-surface` | `#FFFFFF` | `#1E293B` | Base chrome surface (list rows, controls, cards) |
| `--rtv-rm-surface-hover` | `#F1F5F9` | `#303A4B` | Hover state surface |
| `--rtv-rm-surface-active` | `#E2E8F0` | `#3E4756` | Active/pressed state surface |
| `--rtv-rm-border` | `#64748B` | `#8FA0B8` | Dividers, input/panel borders |
| `--rtv-rm-text` | `currentColor` | `currentColor` | Inherits host theme's text color (theme-deferential by design) |
| `--rtv-rm-text-muted` | `#475569` | `#A8B8CB` | Secondary/meta text (addresses, captions) |
| `--rtv-rm-focus-ring` | `#2563EB` | `#60A5FA` | Keyboard focus indicator (matches accent) |
| `--rtv-rm-cluster-sm` | `#3670D6` | `#93C5FD` | Marker cluster badge, small count tier |
| `--rtv-rm-cluster-md` | `#2563EB` | `#60A5FA` | Marker cluster badge, medium count tier |
| `--rtv-rm-cluster-lg` | `#1E3A8A` | `#4E93F0` | Marker cluster badge, large count tier |
| `--rtv-rm-popup-surface` | `#F8FAFC` | `#1C2636` | Leaflet popup background |
| `--rtv-rm-skeleton` | `#E2E8F0` | `#334155` | Loading-state skeleton fill |

Cluster badge foreground text: light mode uses white text on all three cluster tiers; dark mode uses `#0F172A` (dark navy) text on all three cluster tiers, because the dark-mode cluster tier colors are deliberately lighter blues (to read against the map/dark chrome) and require a dark foreground to hit AA rather than white.

## Contrast validation (WCAG 2.2 AA)

Method: standard WCAG relative-luminance contrast ratio, `(L1 + 0.05) / (L2 + 0.05)`, computed with sRGB → linear conversion per the WCAG 2.x formula (this is the same formula axe-core / most AA contrast checkers use). Thresholds applied: **4.5:1** for text-bearing pairs (body/label text, including "large text" pairs — all held to the stricter 4.5:1 here rather than relying on the 3:1 large-text allowance), **3:1** for non-text UI component/boundary pairs (borders, focus rings) per WCAG 1.4.11.

### Light mode

| Pair | Ratio | Threshold | Result |
|---|---|---|---|
| `--rtv-rm-text-muted` (#475569) on `--rtv-rm-surface` (#FFFFFF) | 7.58:1 | 4.5:1 | PASS |
| `--rtv-rm-text-muted` (#475569) on `--rtv-rm-popup-surface` (#F8FAFC) | 7.24:1 | 4.5:1 | PASS |
| `--rtv-rm-accent-contrast` (#FFFFFF) on `--rtv-rm-accent` (#2563EB) | 5.17:1 | 4.5:1 | PASS |
| `--rtv-rm-accent` (#2563EB) on `--rtv-rm-surface` (#FFFFFF) — UI/icon use | 5.17:1 | 3:1 | PASS |
| `--rtv-rm-border` (#64748B) on `--rtv-rm-surface` (#FFFFFF) | 4.76:1 | 3:1 | PASS |
| `--rtv-rm-border` (#64748B) on `--rtv-rm-surface-active` (#E2E8F0) | 3.58:1 | 3:1 | PASS |
| `--rtv-rm-border` (#64748B) on `--rtv-rm-surface-hover` (#F1F5F9) | 4.00:1 | 3:1 | PASS |
| `--rtv-rm-focus-ring` (#2563EB) on `--rtv-rm-surface` (#FFFFFF) | 5.17:1 | 3:1 | PASS |
| cluster text (#FFFFFF) on `--rtv-rm-cluster-sm` (#3670D6) | 4.72:1 | 4.5:1 | PASS |
| cluster text (#FFFFFF) on `--rtv-rm-cluster-md` (#2563EB) | 5.17:1 | 4.5:1 | PASS |
| cluster text (#FFFFFF) on `--rtv-rm-cluster-lg` (#1E3A8A) | 10.36:1 | 4.5:1 | PASS |

### Dark mode

| Pair | Ratio | Threshold | Result |
|---|---|---|---|
| `--rtv-rm-text-muted` (#A8B8CB) on `--rtv-rm-surface` (#1E293B) | 7.23:1 | 4.5:1 | PASS |
| `--rtv-rm-text-muted` (#A8B8CB) on `--rtv-rm-popup-surface` (#1C2636) | 7.52:1 | 4.5:1 | PASS |
| `--rtv-rm-accent-contrast` (#0F172A) on `--rtv-rm-accent` (#60A5FA) | 7.02:1 | 4.5:1 | PASS |
| `--rtv-rm-accent` (#60A5FA) on `--rtv-rm-surface` (#1E293B) — UI/icon use | 5.75:1 | 3:1 | PASS |
| `--rtv-rm-border` (#8FA0B8) on `--rtv-rm-surface` (#1E293B) | 5.50:1 | 3:1 | PASS |
| `--rtv-rm-border` (#8FA0B8) on `--rtv-rm-surface-active` (#3E4756) | 3.23:1 | 3:1 | PASS |
| `--rtv-rm-border` (#8FA0B8) on `--rtv-rm-surface-hover` (#303A4B) | 3.86:1 | 3:1 | PASS |
| `--rtv-rm-focus-ring` (#60A5FA) on `--rtv-rm-surface` (#1E293B) | 5.75:1 | 3:1 | PASS |
| cluster text (#0F172A) on `--rtv-rm-cluster-sm` (#93C5FD) | 9.90:1 | 4.5:1 | PASS |
| cluster text (#0F172A) on `--rtv-rm-cluster-md` (#60A5FA) | 7.02:1 | 4.5:1 | PASS |
| cluster text (#0F172A) on `--rtv-rm-cluster-lg` (#4E93F0) | 5.74:1 | 4.5:1 | PASS |

`--rtv-rm-text` is intentionally excluded from this table: it is `currentColor` in both modes, so its contrast is fully determined by the host theme's own text-color choice against the host's own background, which is outside this plugin's control by design (theme-deferential). `--rtv-rm-skeleton` is excluded because it never carries foreground content (it is a loading placeholder fill, not a text/UI-component background).

Two candidate values were adjusted during validation because they initially failed: the light-mode `--rtv-rm-border` value (`#94A3B8`, 2.56:1 against white — failed 3:1) was darkened to `#64748B`; the dark-mode `--rtv-rm-border` value (`#7B8CA6`, 2.74:1 against `--rtv-rm-surface-active` — failed 3:1) was lightened to `#8FA0B8`; and the light-mode cluster-sm background (`#60A5FA`, 2.54:1 with white text — failed both thresholds) was darkened to `#3670D6`. All three were re-measured after adjustment and are recorded as PASS above.

## Marker

`assets/img/rtv-rm-marker.svg` — 36×36 viewBox pin, matching the existing Leaflet `iconSize: [36, 36]` / `iconAnchor: [18, 36]` / `popupAnchor: [0, -32]` convention in `assets/js/rubicon-maps-frontend.js`. Fill is the literal light-mode accent hex `#2563EB` (SVGs referenced via `<img src>` cannot read CSS custom properties, so the value is baked in rather than referencing `--rtv-rm-accent`).

**Sync note:** if `--rtv-rm-accent` (light mode) is ever changed in `assets/css/rubicon-maps-tokens.css`, the `fill` value in `assets/img/rtv-rm-marker.svg` must be updated to match by hand — there is no build step wiring these together in this task.
