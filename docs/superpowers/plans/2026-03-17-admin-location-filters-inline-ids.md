# Admin Location Filters And Inline IDs Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add native category/region filters and inline post IDs to the Locations admin list screen.

**Architecture:** Add one focused admin helper in `src/Admin/` that owns Locations list-table hooks. Keep query/filter logic WordPress-native and isolate the pure string/action transformation in a testable helper method.

**Tech Stack:** WordPress admin hooks, PHP, existing plugin loader/tests.

---

### Task 1: Add a focused admin list-screen helper

**Files:**
- Create: `src/Admin/LocationListFilters.php`
- Modify: `src/Loader.php`
- Test: `tests/php/LocationListFiltersTest.php`

- [ ] Step 1: Write the failing pure-PHP test for inline ID row actions.
- [ ] Step 2: Run the test and verify it fails for the missing helper.
- [ ] Step 3: Implement minimal helper methods for row-action injection and screen guards.
- [ ] Step 4: Run the test and verify it passes.

### Task 2: Wire native admin dropdown filters

**Files:**
- Modify: `src/Admin/LocationListFilters.php`

- [ ] Step 1: Add `restrict_manage_posts` output for category and region dropdowns on the Locations screen only.
- [ ] Step 2: Add `parse_query` handling to translate selected taxonomy terms into list-table filtering.
- [ ] Step 3: Keep the standard WordPress Filter button flow.

### Task 3: Verify and integrate

**Files:**
- Modify: `tests/php/LocationListFiltersTest.php` (if needed)

- [ ] Step 1: Run targeted PHP tests and lint for the changed files.
- [ ] Step 2: Sync changed runtime files into Local.
- [ ] Step 3: Verify the Locations admin screen shows category and region filters plus inline IDs.
- [ ] Step 4: Commit with an issue-scoped message.
