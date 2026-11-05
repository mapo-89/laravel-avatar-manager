# Contributing

Discuss substantial changes in an issue before opening a pull request. Preserve
supported PHP and Laravel versions and keep CorePanel/Spatie optional at runtime.

## Checks

```bash
composer install
composer qa
node --test .release/release.test.mjs .release/changelog.test.mjs .release/branches.test.mjs .release/ci.test.mjs
node .release/release.mjs notes --version X.Y.Z
```

Tests use SQLite, fake storage and real Spatie Media Library records. Run all
resource-intensive checks through the configured host memory guard when required
by your development environment; do not add host-specific paths to package CI.

## Commits and releases

Follow the [shared release standard](.release/README.md). Use
`<emoji> <Type>: <imperative English summary>`. Feature branches start on `main`
and are individually squash-merged into an explicitly authorized `release/X.Y.Z`.
Direct categorized commits on the release branch are also allowed.

Ordinary commits do not change versions or the changelog. Keep the release PR
draft until the final implementation has passed every configured CI workflow.
Run preparation once, require fresh checks, then merge through a reviewed PR
with a `🚀 Release: X.Y.Z` merge commit. Existing tags remain immutable. Normal
commits, pushes and dependency PRs never authorize publication.

Report vulnerabilities privately to [info@postler.de](mailto:info@postler.de).
