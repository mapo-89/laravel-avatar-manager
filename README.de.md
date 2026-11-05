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

Selbst gehostete Avatar-URLs auf Basis normalisierter E-Mail-Hashes, mit lokalen
oder entfernten Laravel-Dateisystemen, Jetstream-Profilbildern und optionaler
Anbindung an CorePanel / Spatie Media Library.

[English documentation](README.md)

## Voraussetzungen

- PHP 8.3+ mit Laravel 12 oder 13
- PHP 8.5+ bei der Anbindung an CorePanel
- Ein konfiguriertes Laravel-Dateisystem (Standard: `public`)
- Ein Eloquent-Benutzermodell in `auth.providers.users.model`

CorePanel und Spatie Media Library sind optional. Die Profilverwaltung übernimmt
die Anwendung; dieses Paket ergänzt den öffentlichen Abruf per Hash und einen
Upload-Endpunkt mit API-Schlüssel.

## Installation

```bash
composer require mapo-89/laravel-avatar-manager
```

Laravel erkennt Service Provider und den Alias `LaravelAvatarManager`
automatisch. Konfiguration und Standardbild funktionieren direkt aus dem Paket.

## Konfiguration

Nur benötigte Anpassungen veröffentlichen:

```bash
php artisan vendor:publish --tag=avatar-manager-config
php artisan vendor:publish --tag=avatar-manager-assets
```

| Option | Standard | Zweck |
| --- | --- | --- |
| `disk` | `public` | Dateisystem für API-Uploads. |
| `user_model` | `null` | Überschreibt das Modell aus `auth.providers.users.model`. |
| `profile_photo_disk` | `null` | Jetstream-Dateisystem verwenden, sonst `public`. |
| `media_collection` | `avatars` | Spatie-Mediensammlung für CorePanel. |
| `default_avatar` | `vendor/avatar-manager/default.png` | Pfad relativ zu `public`; fehlt die Datei, wird das mitgelieferte Bild verwendet. |
| `api_keys` | `[env('AVATAR_API_KEY')]` | Nicht leere Zeichenketten als Upload-Schlüssel. |

Der Hash-Endpunkt liefert Dateien über Laravel aus und benötigt keinen
Storage-Symlink. Anwendungen, die `/storage/...` direkt verwenden, benötigen bei
einem lokalen öffentlichen Dateisystem weiterhin `php artisan storage:link`.

## Verwendung

```php
use Mapo89\LaravelAvatarManager\Facades\AvatarManager;

$hash = AvatarManager::hash(' User@example.com ');
$url = AvatarManager::url('user@example.com');
```

Die erzeugte URL direkt einbinden:

```blade
<img src="{{ \Mapo89\LaravelAvatarManager\Facades\AvatarManager::url($user->email) }}"
     alt="{{ $user->name }}">
```

`GET /avatar/{hash}` akzeptiert 32 hexadezimale Zeichen, auch Großbuchstaben.
Berechnet wird `md5(strtolower(trim($email)))`. Die Reihenfolge beim Abruf ist:

1. Spatie-Profilbild des Benutzers, danach sein Jetstream-`profile_photo_path`.
2. API-Upload unter `avatars/{hash}.{jpg|jpeg|png|gif|webp}`.
3. Veröffentlichtes/eigenes Standardbild, danach das mitgelieferte Bild.

Verwaltete Profilbilder haben Vorrang vor älteren API-Uploads. Die Benutzersuche
verwendet das konfigurierte Modell und dessen globale Scopes, einschließlich
Soft Deletes. Der Hash ist ein öffentlicher Bezeichner, kein Zugriffsschutz und
keine Anonymisierung der E-Mail-Adresse.

### CorePanel-Anbindung

CorePanel verwaltet Profilbilder in der Spatie-Mediensammlung `avatars`. Der
Standardresolver liest diese Sammlung vom konfigurierten Benutzermodell und
liefert das Originalbild aus dem Dateisystem des Medieneintrags aus. Dafür sind
weder eine CorePanel-Abhängigkeit noch ein zweiter Profilbild-Upload nötig.
Das CorePanel-Modell bleibt in `auth.providers.users.model` konfiguriert oder
wird über `user_model` explizit ausgewählt.

Für andere Speichersysteme lässt sich
`Mapo89\LaravelAvatarManager\Contracts\ProfilePhotoProviderInterface` durch
einen eigenen Resolver ersetzen. `findForUser(object $user): ?array` liefert
`['disk' => 'disk-name', 'path' => 'relative/path.png']` oder `null`.
`UserProviderInterface` bleibt für eigene Hash-Suchverfahren verfügbar.

### API-Upload

Einen starken Schlüssel in der `.env` der Anwendung setzen:

```dotenv
AVATAR_API_KEY=replace-with-a-long-random-secret
```

Multipart-Anfrage aus einem vertrauenswürdigen Backend senden:

```bash
curl --request POST 'https://avatars.example.com/api/avatars/upload' \
  --header "X-API-KEY: $AVATAR_API_KEY" \
  --form 'email=user@example.com' \
  --form 'avatar=@profile.png'
```

Ein Schlüssel erlaubt Uploads für beliebige E-Mail-Adressen und gehört nicht in
Browser-Code. Erlaubt sind JPEG, PNG, GIF und WebP bis 2 MiB. SVG und andere Typen
werden abgewiesen. Dateien erhalten die zum erkannten Bildtyp passende Endung;
alte Endungen werden nach erfolgreichem Speichern entfernt.

| Status | Bedeutung |
| --- | --- |
| `200` | Avatar gespeichert. |
| `401` | Fehlender, ungültiger oder nicht konfigurierter Schlüssel. |
| `409` | Verwaltetes Profilbild vorhanden; Upload abgewiesen. |
| `422` | Ungültige E-Mail, Dateityp oder Bildgröße. |

Der Endpunkt registriert keinen Benutzer. Ohne verwaltetes Profilbild ersetzt
ein späterer API-Upload das bisherige API-Bild derselben E-Mail-Adresse.

## Bestehende Installationen aktualisieren

Laravel 11 und PHP 8.2 werden nicht mehr unterstützt. Vor dem Paketupdate die
Anwendung auf PHP 8.3+ und Laravel 12 oder 13 aktualisieren. Diese inkompatiblen
Änderungen erfordern den nächsten Major-Release des Pakets.

`/avatar/{hash}` und `/api/avatars/upload` bleiben erhalten; Jetstream wird
weiter unterstützt. Folgende korrigierte Verhaltensweisen beachten:

- Ungültige Hashes liefern `404`, statt einen Dateizugriff zu versuchen.
- Das dokumentierte Limit von 2 MiB wird umgesetzt; zuvor erlaubte der Code 20 MiB.
- PNG, GIF und WebP erhalten ihre tatsächliche Endung statt `.jpg`.
- Verwaltete Profilbilder haben Vorrang vor API-Uploads.
- Das konfigurierte Auth-Modell ersetzt die feste Bindung an `App\Models\User`.

Ein normales Update führt weder Migrationen noch Release-Vorbereitung oder eine
Veröffentlichung aus.

## Entwicklung

```bash
composer install
composer qa
```

Tests verwenden SQLite, simulierte Dateisysteme und echte Spatie-Medieneinträge.
Die CI prüft Laravel 12/13, unterstützte PHP-Versionen, Linux und Windows sowie
niedrigste und stabile Abhängigkeiten. Release-Tests benötigen Node.js 24+ und Git:

```bash
node --test .release/release.test.mjs .release/changelog.test.mjs .release/branches.test.mjs .release/ci.test.mjs
node .release/release.mjs notes --version X.Y.Z
```

[CONTRIBUTING](CONTRIBUTING.md) und der [gemeinsame Release-Leitfaden](.release/README.md)
beschreiben Commit-Regeln, Dependency-Updates und Releases. Der Ablauf lautet
`main → feature/* → release/X.Y.Z → main`; direkte kategorisierte Commits auf dem
Release-Branch sind ebenfalls möglich. Release-PRs bleiben Draft, bis der gesamte
Umfang auf dem exakten Commit durch die CI geprüft wurde. Danach einmal gesammelt
vorbereiten und erneut prüfen. Normale Commits und Pushes veröffentlichen nichts.
Bestehende Tags bleiben erhalten; Composer ermittelt Versionen aus unveränderlichen
`vX.Y.Z`-Tags auf geprüften Release-Merges. Veröffentlichte Notes müssen dem
vorbereiteten [CHANGELOG](CHANGELOG.md) entsprechen.

## Sicherheit

Sicherheitsprobleme an [info@postler.de](mailto:info@postler.de) melden.

## Mitwirkende und Lizenz

- [Manuel Postler](https://github.com/mapo-89)
- [Alle Mitwirkenden](../../contributors)

Veröffentlicht unter der [MIT-Lizenz](LICENSE.md).
