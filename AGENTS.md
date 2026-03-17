# AGENTS.md

## Purpose

Defines repository-specific collaboration rules for Rubicon Maps.

## Workflow

- Use Forgejo as the canonical host unless a repo overlay explicitly says otherwise.
- Start meaningful work from an issue when possible.
- Use `dev/<issue-or-scope>-<slug>` branches.
- Merge through PRs rather than pushing directly to `main`.

## Engineering Expectations

- Keep Divi 4 and Divi 5 runtimes separate.
- Keep shared plugin logic in `src/`.
- Preserve WordPress-native content fields for locations: title, excerpt, content, thumbnail, ordering.
- Add or update tests whenever behavior changes in a testable pure-PHP path.

## Verification

- `composer dump-autoload`
- `php tests/php/DiviBootstrapDeciderTest.php`
- `php tests/php/LocationQueryArgsBuilderTest.php`
- `php tests/php/LocationAddressFormatterTest.php`
- `php tests/php/LocationCsvTransformerTest.php`
- `php tests/php/MapInstanceConfigBuilderTest.php`
- `find src divi-4 divi-5 tests -name '*.php' -print0 | xargs -0 -n1 php -l`
- `cd divi-5/visual-builder && npm ci && npm run build`

## Release Discipline

- Tags use `vX.Y.Z`.
- Release titles use `X.Y.Z`.
- Release notes begin with `Rubicon Maps vX.Y.Z (YYYY-MM-DD)`.
- Ship installable zip artifacts, not just source snapshots.
