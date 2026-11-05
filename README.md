# Laravel Avatar Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mapo-89/laravel-avatar-manager.svg)](https://packagist.org/packages/mapo-89/laravel-avatar-manager)
[![License: MIT](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE.md)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4.svg?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-12%20%7C%2013-FF2D20.svg?logo=laravel&logoColor=white)](https://laravel.com/)
[![Total Downloads](https://img.shields.io/packagist/dt/mapo-89/laravel-avatar-manager.svg)](https://packagist.org/packages/mapo-89/laravel-avatar-manager)
[![GitHub Actions](https://github.com/mapo-89/laravel-avatar-manager/actions/workflows/main.yml/badge.svg)](https://github.com/mapo-89/laravel-avatar-manager/actions/workflows/main.yml)
[![GitHub issues](https://img.shields.io/github/issues/mapo-89/laravel-avatar-manager.svg)](https://github.com/mapo-89/laravel-avatar-manager/issues)
[![GitHub stars](https://img.shields.io/github/stars/mapo-89/laravel-avatar-manager.svg?style=flat)](https://github.com/mapo-89/laravel-avatar-manager/stargazers)
[![Last commit](https://img.shields.io/github/last-commit/mapo-89/laravel-avatar-manager.svg)](https://github.com/mapo-89/laravel-avatar-manager/commits/main)

<a href="https://buymeacoffee.com/mapo"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me a Coffee" height="60"></a>

Self-hosted avatar URLs based on normalized email hashes, with local or remote
Laravel filesystem storage, Jetstream profile photos, and optional CorePanel /
Spatie Media Library integration.

[Deutsche Dokumentation](README.de.md)

## Requirements

- PHP 8.3+ with Laravel 12 or 13
- PHP 8.5+ when integrating CorePanel
- A configured Laravel filesystem disk (default: `public`)
- An Eloquent user model configured in `auth.providers.users.model`

CorePanel and Spatie Media Library are optional. Ordinary profile management is
provided by your application; this package adds public hash-based retrieval and
an API-key-protected upload endpoint.

## Installation

```bash
composer require mapo-89/laravel-avatar-manager
```

Laravel discovers the service provider and `LaravelAvatarManager` facade alias
automatically. Configuration and assets work directly from the package.

## Configuration

Publish only the overrides your application needs:

```bash
php artisan vendor:publish --tag=avatar-manager-config
php artisan vendor:publish --tag=avatar-manager-assets
```

| Option | Default | Purpose |
| --- | --- | --- |
| `disk` | `public` | Disk for API-uploaded avatars. |
| `user_model` | `null` | Override the model from `auth.providers.users.model`. |
| `profile_photo_disk` | `null` | Use Jetstream's configured disk, falling back to `public`. |
| `media_collection` | `avatars` | Spatie Media Library collection used by CorePanel. |
| `default_avatar` | `vendor/avatar-manager/default.png` | Path relative to `public`; missing files fall back to the bundled image. |
| `api_keys` | `[env('AVATAR_API_KEY')]` | Nonempty string keys accepted by the upload endpoint. |

The hash endpoint streams through Laravel, so `storage:link` is unnecessary for
that endpoint. Applications serving `/storage/...` URLs directly still need
`php artisan storage:link` on a public local disk.

## Usage

```php
use Mapo89\LaravelAvatarManager\Facades\AvatarManager;

$hash = AvatarManager::hash(' User@example.com ');
$url = AvatarManager::url('user@example.com');
```

Embed the generated URL directly:

```blade
<img src="{{ \Mapo89\LaravelAvatarManager\Facades\AvatarManager::url($user->email) }}"
     alt="{{ $user->name }}">
```

`GET /avatar/{hash}` accepts 32 hexadecimal characters, including uppercase. The
hash is `md5(strtolower(trim($email)))`. Images are resolved in this order:

1. The user's Spatie media image, then their Jetstream `profile_photo_path`.
2. An API-uploaded file at `avatars/{hash}.{jpg|jpeg|png|gif|webp}`.
3. A published/custom default image, then the bundled default.

Managed profile photos take precedence over older API uploads. User lookup uses
the configured model and respects its global scopes, including soft deletion.
A hash is a public identifier, not access control or email anonymization.

### CorePanel integration

CorePanel stores profile images in Spatie Media Library's `avatars` collection.
The default resolver reads this collection from the configured user model and
streams the original image from the media record's disk. No CorePanel dependency
or duplicated profile upload is needed in this package. Keep the CorePanel user
model configured in `auth.providers.users.model`, or set `user_model` explicitly.

For another profile-storage system, bind
`Mapo89\LaravelAvatarManager\Contracts\ProfilePhotoProviderInterface` to your
own resolver. Its `findForUser(object $user): ?array` returns
`['disk' => 'disk-name', 'path' => 'relative/path.png']` or `null`.
`UserProviderInterface` remains available for custom email-hash lookup.

### API uploads

Set a strong API key in the host application's `.env`:

```dotenv
AVATAR_API_KEY=replace-with-a-long-random-secret
```

Send a multipart request from a trusted backend:

```bash
curl --request POST 'https://avatars.example.com/api/avatars/upload' \
  --header "X-API-KEY: $AVATAR_API_KEY" \
  --form 'email=user@example.com' \
  --form 'avatar=@profile.png'
```

Keys authorize uploads for any email; keep them out of browser code. Images must
be JPEG, PNG, GIF or WebP, at most 2 MiB. SVG and other file types are rejected.
Files keep the extension matching their detected image type; superseded
extensions are removed after a successful write.

| Status | Meaning |
| --- | --- |
| `200` | Avatar stored successfully. |
| `401` | Missing, invalid or unconfigured API key. |
| `409` | A managed profile photo already exists; upload rejected. |
| `422` | Invalid email, file type or image size. |

This endpoint does not register a user. Without a managed profile image, a later
API upload replaces the previous API image for the same email.

## Updating existing installations

Laravel 11 and PHP 8.2 are no longer supported. Upgrade the host application
to PHP 8.3+ and Laravel 12 or 13 before updating this package. These compatibility
changes require the next major package release.

Existing `/avatar/{hash}` and `/api/avatars/upload` URLs remain available, and
Jetstream integration stays supported. Review these corrected behaviors:

- Invalid hashes now return `404` instead of attempting file lookup.
- The documented 2 MiB upload limit is enforced (previously 20 MiB in code).
- PNG, GIF and WebP uploads keep their real extension instead of `.jpg`.
- Managed profile photos take precedence over API uploads.
- User lookup follows the configured auth model instead of hard-coding `App\Models\User`.

No migration, version preparation or release is performed by an ordinary update.

## Development

```bash
composer install
composer qa
```

Tests use SQLite, fake filesystems and real Spatie Media Library records. CI
covers Laravel 12/13, supported PHP runtimes, Linux and Windows, and both
lowest and stable dependencies. Node.js 24+ and Git are needed for release tests:

```bash
node --test .release/release.test.mjs .release/changelog.test.mjs .release/branches.test.mjs .release/ci.test.mjs
node .release/release.mjs notes --version X.Y.Z
```

See [CONTRIBUTING](CONTRIBUTING.md) and the [shared release guide](.release/README.md)
for commit conventions, dependency updates and releases. The workflow is
`main → feature/* → release/X.Y.Z → main`; direct categorized commits on the
release branch are also supported. Keep release PRs draft until the final
implementation passes all configured CI, then prepare once and rerun checks.
Ordinary commits and pushes do not publish releases. Existing tags stay unchanged;
Composer derives versions from immutable `vX.Y.Z` tags on tested release merges.
Published notes must match the prepared [CHANGELOG](CHANGELOG.md).

## Security

Report security issues to [info@postler.de](mailto:info@postler.de).

## Credits and license

- [Manuel Postler](https://github.com/mapo-89)
- [All contributors](../../contributors)

Licensed under the [MIT License](LICENSE.md).
