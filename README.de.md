# Laravel Avatar Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mapo-89/laravel-avatar-manager.svg?style=flat-square)](https://packagist.org/packages/mapo-89/laravel-avatar-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/mapo-89/laravel-avatar-manager.svg?style=flat-square)](https://packagist.org/packages/mapo-89/laravel-avatar-manager)
![GitHub Actions](https://github.com/mapo-89/laravel-avatar-manager/actions/workflows/main.yml/badge.svg)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

**Laravel Avatar Manager** ist ein leichtgewichtiges, self-hosted Laravel-Package zum Verwalten von Benutzer-Avataren – mit Unterstützung für Gravatar-kompatible Hashes, lokale Speicherung und einfache Integration in bestehende Projekte.

📖 Diese README ist auch auf [🇬🇧 Englisch](README.md) verfügbar.

---

## ✨ Features

- Avatar-URLs basierend auf MD5(Email), wie bei Gravatar
- Fallback auf Standard-Avatare
- Nahtlose Integration in das Laravel-Benutzermodell
- Lokale Speicherung von hochgeladenen Avataren
- API-basierter Avatar-Upload mit E-Mail + API-Schlüssel (keine Anmeldung erforderlich)
- SQLite-kompatibel ✅.

## 🚀 API-Avatar-Upload

Es ist jetzt möglich, Avatare über eine öffentliche API hochzuladen, ohne dass eine Benutzerregistrierung erforderlich ist. Nützlich für:

- CLI-Skripte
- Externe Systeme
- Dienste von Drittanbietern

### Endpunkt

```http
POST /api/avatars/upload
```

#### Headers

```http
X-API-KEY: ihr-api-key
```

#### Payload

```json
{
 "email": "user@example.com",
 "avatar": (Bilddatei)
}
```

Das hochgeladene Bild wird gespeichert in:

```bash
storage/app/public/avatars/{md5(email)}.jpg
```

### Konfiguration

Einen oder mehrere API-Schlüssel zur Konfigurationsdatei hinzufügen:

```php
// config/avatar-manager.php
'api_keys' => [
 env('AVATAR_API_KEY'),
],
```

Und in der .env Datei:

```php
AVATAR_API_KEY=your-api-key
```

## 🛠️ Installation

Das Paket kann über Composer installiert werden:

```bash
composer require mapo-89/laravel-avatar-manager
```


## ⚙️ Konfiguration

```bash
php artisan vendor:publish --provider="Mapo89\LaravelAvatarManager\AvatarManagerServiceProvider"
php artisan storage:link
```
Es kann auch gezielt veröffentlicht werden:

```bash
# Nur Konfiguration
php artisan vendor:publish --tag=avatar-manager-config

# Nur Assets (z. B. Standard-Bilder, CSS)
php artisan vendor:publish --tag=avatar-manager-assets

```

### Testing

Um das Paket flexibel und testbar zu halten, verwendet der AvatarManager ein Interface für den Zugriff auf User-Daten:
`Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface`

Für Tests wird die Test-User-Klasse über `TestUserProvider` gebunden. 

#### Produktivbetrieb

Im Service Provider bindet das Paket standardmäßig die echte User-Klasse (z.B. App\Models\User):

```php
public function register()
{
    $this->app->bind(
        \Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface::class,
        \Mapo89\LaravelAvatarManager\Services\UserProvider::class
    );
}
```
Die UserProvider-Klasse implementiert die Logik, um User per Email-Hash zu finden.

#### Tests ausführen

Die Tests können mit verschiedenen Befehlen ausgeführt werden:

```bash
composer test

./vendor/bin/phpunit --testdox --stderr

./vendor/bin/pest
```

### Changelog

Bitte für weitere Informationen über die letzten Änderungen [CHANGELOG](CHANGELOG.md) lesen .

## Mitwirken

Bitte für Details [CONTRIBUTING](CONTRIBUTING.md) lesen.

### Sicherheit

Wenn du sicherheitsrelevante Probleme entdeckst, sende bitte eine E-Mail an info@postler.de, anstatt den Issue Tracker zu benutzen.

## Credits

- [Manuel Postler](https://github.com/mapo-89)
- [Alle Mitwirkenden](../../contributors)

## Lizenz

Die MIT-Lizenz (MIT). Bitte für weitere Informationen die [Lizenzdatei](LICENSE.md) lesen.

## Laravel-Paket Boilerplate

Dieses Paket wurde unter Verwendung der [Laravel Package Boilerplate](https://laravelpackageboilerplate.com) erstellt.