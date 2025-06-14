# Laravel Avatar Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mapo-89/laravel-avatar-manager.svg?style=flat-square)](https://packagist.org/packages/mapo-89/laravel-avatar-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/mapo-89/laravel-avatar-manager.svg?style=flat-square)](https://packagist.org/packages/mapo-89/laravel-avatar-manager)
![GitHub Actions](https://github.com/mapo-89/laravel-avatar-manager/actions/workflows/main.yml/badge.svg)

**LaravelAvatarManager** ist ein leichtgewichtiges, self-hosted Laravel-Package zum Verwalten von Benutzer-Avataren – mit Unterstützung für Gravatar-kompatible Hashes, lokale Speicherung und einfache Integration in bestehende Projekte.

📖 Diese README ist auch auf [🇬🇧 Englisch](README.md) verfügbar.

---

## ✨ Features

- Avatar-URLs auf Basis von MD5(Email), wie bei Gravatar
- Fallback auf Default-Avatare
- Nahtlose Integration mit dem Laravel-User-Model
- SQLite-kompatibel ✅


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
Du kannst auch gezielt veröffentlichen:

```bash
# Nur Konfiguration
php artisan vendor:publish --tag=avatar-manager-config

# Nur Assets (z. B. Standard-Bilder, CSS)
php artisan vendor:publish --tag=avatar-manager-assets

```

### Testen

```bash
composer test
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