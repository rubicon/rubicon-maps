# Contributing to Rubicon Maps

Thanks for your interest in contributing. This project follows a small, strict
workflow so `main` stays releasable.

By participating you agree to the [Code of Conduct](CODE_OF_CONDUCT.md).

## Workflow

1. **Open an issue first.** Describe the bug, feature, or change and its intent.
   Non-trivial work should have an issue before a pull request.
2. **Branch** from `main` using `dev/<issue-number>-<short-kebab-description>`
   (e.g. `dev/42-fix-marker-cluster-count`).
3. **Make focused commits** using
   [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/)
   (`feat`, `fix`, `docs`, `refactor`, `test`, `ci`, `build`, `chore`, …).
4. **Open a pull request** into `main`. Link the issue with `Closes #N`.
   CI must pass; `main` requires pull requests and passing checks.

## Development

Requirements: PHP 8.1+, Composer, Node.js 20+.

```bash
composer install                       # PHP autoload (no runtime deps)
cd divi-5/visual-builder && npm ci && npm run build   # Divi 5 Visual Builder assets
```

## Verification (run before opening a PR)

```bash
composer dump-autoload
find src divi-5 tests -name '*.php' -print0 | xargs -0 -n1 php -l
for f in tests/php/*.php; do php "$f"; done   # the full suite, not a subset
cd divi-5/visual-builder && npm ci && npm run build
```

CI (GitHub Actions) runs the same checks on every PR and is required to merge.

## Conventions

- **Divi 5 only** — no Divi 4 / `et_pb_*`.
- **Internal identifiers** use the `rtv_rm_` / `rtv-rm-` prefix. Public-facing
  surfaces stay unchanged: plugin slug/text-domain `rubicon-maps`, REST namespace
  `rubicon-maps/v1`, shortcodes.
- **Accessibility:** WCAG 2.2 AA is enforced. New user-facing strings are
  localized (PHP `__()` / `esc_html__()`; JS via `wp_localize_script`), never
  hardcoded.
- **Security:** unslash → sanitize → validate on input; escape on output; nonce
  and capability checks on every state-changing action.

## Architecture

See [ARCHITECTURE.md](ARCHITECTURE.md) for how the codebase is laid out.
