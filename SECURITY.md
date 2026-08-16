# Security Policy

## Supported versions

Rubicon Maps is in its `1.0.x` line. Security fixes are provided for the latest
released version. Please upgrade to the newest release before reporting an issue.

| Version | Supported |
| ------- | --------- |
| 1.0.x   | ✅        |
| < 1.0   | ❌        |

## Reporting a vulnerability

**Do not open a public issue for security problems.**

Report vulnerabilities privately through GitHub's built-in
**Private Vulnerability Reporting**:

1. Go to the repository's **Security** tab.
2. Click **Report a vulnerability**.
3. Describe the issue, affected version(s), and reproduction steps.

We aim to acknowledge a report within a few business days, keep you updated as we
investigate, and credit you in the release notes when a fix ships (unless you ask
to remain anonymous).

## Scope

Rubicon Maps is a WordPress plugin. The most relevant classes of issue are:
output escaping, input sanitization and validation, nonce/capability checks on
state-changing actions, and the plugin's REST endpoints. Please include the
WordPress and PHP versions and the plugin version in your report.
