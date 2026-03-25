# Rubicon Maps Searchable Filter Pickers Design

Date: 2026-03-25
Issue: #19

## Summary

Replace the current comma-separated filter text fields in the Rubicon Maps Divi modules with searchable multi-select pickers that render selected items as removable pills.

The picker should work in both Divi 4 and Divi 5, while preserving compatibility with already-saved modules.

## Decisions

- Keep the existing filter field names as the persistence boundary:
  - `category`
  - `region`
  - `location_ids`
- New saves use structured JSON arrays inside those fields:
  - categories and regions save slug arrays
  - locations save integer ID arrays
- Existing comma-separated values remain supported as a read fallback.
- The shared PHP query layer is the normalization boundary for:
  - JSON arrays
  - PHP arrays
  - comma-separated legacy strings

## Divi 5

- Replace the three plain text fields with custom searchable multi-select controls.
- Each control keeps a hidden native Divi text field so Divi continues to own save behavior.
- The picker searches real WordPress records:
  - categories from the taxonomy REST endpoint
  - regions from the taxonomy REST endpoint
  - locations from the location post type REST endpoint
- Selected values render as pills and can be removed without editing raw text.
- Preview UI resolves human-readable labels from the saved structured values.

## Divi 4

- Keep the existing module field names for compatibility.
- Add stable field IDs so the builder enhancement script can find the correct inputs.
- Enhance those inputs in the builder with searchable multi-select UI that writes JSON arrays back into the native text fields.
- This preserves Divi 4 shortcode rendering while delivering the same saved-value model as Divi 5.

## Frontend Compatibility

- Frontend rendering behavior should remain unchanged.
- Module filters continue to drive the same query logic.
- The query builder must normalize:
  - JSON arrays stored in module fields
  - legacy comma-separated strings
  - direct array input from internal callers

## Acceptance Criteria

- Divi map module uses searchable pill-based filter controls for categories, regions, and explicit locations.
- Divi listing module uses the same controls when the listing is not synced.
- Divi 4 and Divi 5 both save structured filter values.
- Existing saved modules that still contain comma-separated values continue to work.
- Preview labels and counts continue to reflect the selected filter set.
