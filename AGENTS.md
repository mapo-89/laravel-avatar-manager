# Package development

Keep Laravel 12 and 13 support and the PHP 8.3 minimum. CorePanel and Spatie Media Library are
optional host integrations, not mandatory production dependencies.

Run `composer qa` for package changes. Integration tests use SQLite and fake
storage, including real Spatie Media Library records. Run the release-tooling
tests and preview notes when changing `.release` or publishing workflows.
Resource-intensive commands must follow the inherited host memory rules.

## Shared commit and release policy

Use `.release/README.md` and `.release/config.json`. The authorized workflow is
`main → feature/* → release/X.Y.Z → main`, without a develop branch. Squash each
reviewed feature into the release branch with a categorized English commit.
Direct categorized commits on release/X.Y.Z are equally allowed and do not need
to be squashed. Both kinds of work may be mixed. Ordinary work commits do not
update version metadata or CHANGELOG.md. Once the intended release scope is
complete, validate the exact implementation commit through a draft release PR.
Only after all configured CI workflows are green, run
`node .release/branches.mjs prepare` once. Ready PRs still require a prepared tip
and fresh checks; do not create version commits during CI troubleshooting.
If more commits follow preparation, defer the refresh until the release scope is
complete again, then rerun prepare and checks before merging the release PR.
Do not run prepare after every commit or merely to leave the branch prepared. Merge the prepared release into main with
`--no-ff` and subject `🚀 Release: X.Y.Z`; tag/publish only that merge SHA after
the required checks. Retarget dependency PRs to the active release branch.

Keep existing tags, history, publisher/build/deployment approvals and user work.
Never rewrite main to regroup commits from before the transition. Normal changes
and this workflow implementation do not authorize commits, pushes, releases or
deployments. A user-requested patch/minor/major release determines the explicit
version used for the release branch. Manual publication must validate the same
prepared merge; it cannot generate a version successor on main.

Use `<emoji> <Type>: <imperative English summary>`. Reserve `✨ Feature:` for an
independent new capability. Run the affected release-tooling tests and preview
notes before committing release-tooling changes. Published notes must match the
prepared changelog at the immutable tag. The changelog publisher verifies this
without adding another main commit. See `.release/README.md` for commands.

## Dependency update policy

Follow the dependency-update section in `.release/README.md`. Bots open weekly
update PRs on the default branch; select and retarget reviewed updates to the
explicitly authorized release branch, rerun CI, and squash through their PRs.
Never auto-merge dependency PRs or change supported runtime/framework versions
just to satisfy an update. Compatibility breaks require an explicit major release.
Collect dependency updates with the other work; prepare once when the release
scope is complete, refreshing only at finalization if later commits were added.
No bot commit authorizes a release.
