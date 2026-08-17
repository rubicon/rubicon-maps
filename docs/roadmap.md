# Rubicon Maps — Roadmap (post-2.0.0)

**Status:** v2.0.0 shipped — public, open-source (GPL-2.0), Divi-5-only, with a GitHub-Releases auto-updater, CI, and the phase-1 frontend UI foundation. This document sequences the remaining product work. It was developed with an independent second-opinion pass (Codex) and verified against the codebase.

## Strategic direction

Rubicon Maps targets **both Divi 5 and the native Gutenberg block editor**. Divi 5 is the current runtime; **native Gutenberg block support is a committed future major version** (see #8), built *on top of* the existing builder-agnostic renderer core, not a rewrite of it.

**Core principle:** the `src/Frontend` renderers (`MapRenderer`, `LocationListRenderer`, `MapInstanceConfigBuilder`, `LocationRepository`) are the single source of truth for render, config, and data. Every surface — Divi 5 module, WordPress shortcode, and the future Gutenberg block — adapts attributes into those renderers and never forks rendering. WCAG 2.2 AA is maintained across all changes.

## Sequence

1. **#9 Test modernization — Phase A** (foundation)
2. **#6 Phase-2 UX** — embedded list + cross-page detail links (on the current architecture)
3. **#5 Phase-3** — search / filter / category legend
4. **#8 Native Gutenberg block core** — future major (gated by #9 Phase B)

## Detail

### 1. #9 Test modernization — Phase A now (size: L)

PHPCS (WordPress Coding Standards) + PHPStan (WP stubs, baseline `src/`) + PHPUnit with the WP integration test suite, porting the existing pure-PHP tests. Do this first: #6 touches frontend markup and accessibility, and the current pure-PHP tests are stub-heavy — good porting material, not sufficient coverage for a public plugin. **Phase B (Playwright E2E against the Divi 5 Visual Builder) is deferred until #8**; the Divi Developer License is available for CI runners when that time comes.

### 2. #6 Phase-2 UX — on the current architecture (size: M)

Build on the existing shared renderers; **do not gate on #8.** Two sub-parts:

- **#6a — embedded list (the real work):** the Map module gains an optional list (position left / right / above / below, responsive side-by-side/stacked), reusing `LocationListRenderer` and the existing hydrate-from-map sync. Achieve setting parity by **extracting the shared list settings into one place**, not by duplicating the standalone list's settings.
- **#6b — cross-page detail links (small):** the location CPT is already `public` with `rewrite: locations` and `show_in_rest`, and `LocationRepository::serialize()` already returns `permalink`. So this is "wire the detail link + polish a single-location template," not the "make the CPT viewable" effort the original issue implied.

### 3. #5 Phase-3 — search / filter / legend (size: M)

After #6's layout and list-item modes settle. Extends the existing frontend JS / REST / list-sync runtime; needs careful accessibility and filter-state handling.

### 4. #8 Native Gutenberg block core — future major (size: L)

Add native WordPress block support (`block.json` + Interactivity API + Script Modules) so the map/list work in the Gutenberg block editor, block themes, and other builders — delivering the "both Gutenberg and Divi" goal. This is a **future major** (target a `3.0` line), not part of the 2.x cycle.

- Do not rebuild the core. The existing `src/Frontend` boundary already provides the portability foundation; the block is a thin native surface over it.
- **Spike first:** prototype one native block behind the existing renderers, confirm it coexists with the Divi module and shortcodes, then commit.

## Test gating

- **Before #6:** PHPUnit regression coverage for `MapRenderer`, `LocationListRenderer`, `MapInstanceConfigBuilder`, `LocationRepository`, and REST filtering; PHPCS enforcing escaping/i18n; PHPStan baseline on `src/`.
- **Before #8:** Phase B Playwright E2E (Divi map/list render + sync, shortcode render, REST marker load, keyboard list activation, reduced-motion) and a migration-compatibility test proving existing Divi/shortcode content still renders through the same core.

## Top risks

- **Renderer divergence** — keep `src/Frontend` the single source; surfaces adapt attributes only.
- **Accessibility regressions** — the list uses `role="button"`, keyboard activation, `aria-live`, focus styles, and reduced-motion handling; test before turning list items into links (#6b).
- **Embedded-list setting sprawl** — extract shared list settings; do not copy-paste.
- **Native-block churn** — Interactivity API + Script Modules change asset loading and editor/runtime assumptions; spike before committing (#8).
- **False confidence from current tests** — the pure-PHP scripts are useful but stub-heavy; treat them as porting source, not coverage.
