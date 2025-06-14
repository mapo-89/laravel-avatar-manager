# Laravel Avatar Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mapo-89/laravel-avatar-manager.svg?style=flat-square)](https://packagist.org/packages/mapo-89/laravel-avatar-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/mapo-89/laravel-avatar-manager.svg?style=flat-square)](https://packagist.org/packages/mapo-89/laravel-avatar-manager)
![GitHub Actions](https://github.com/mapo-89/laravel-avatar-manager/actions/workflows/main.yml/badge.svg)

**Laravel Avatar Manager** is a lightweight, self-hosted Laravel package for managing user avatars - with support for Gravatar-compatible hashes, local storage and easy integration into existing projects.

📖 This README is also available in [🇩🇪 German](README.de.md).

---

## ✨ Features

- Avatar URLs based on MD5(Email), as with Gravatar
- Fallback to default avatars
- Seamless integration with the Laravel user model
- SQLite compatible ✅


## 🛠️ Installation

You can install the package via composer:

```bash
composer require mapo-89/laravel-avatar-manager
```

## ⚙️ Configuration

```bash
php artisan vendor:publish --provider="Mapo89\LaravelAvatarManager\AvatarManagerServiceProvider"
php artisan storage:link
```
You can also publish specifically:

```bash
# Configuration only
php artisan vendor:publish --tag=avatar-manager-config

# Only assets (e.g. standard images, CSS)
php artisan vendor:publish --tag=avatar-manager-assets

```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email info@postler.de instead of using the issue tracker.

## Credits

-   [Manuel Postler](https://github.com/mapo-89)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
