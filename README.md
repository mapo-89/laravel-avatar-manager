# Laravel Avatar Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mapo-89/laravel-avatar-manager.svg?style=flat-square)](https://packagist.org/packages/mapo-89/laravel-avatar-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/mapo-89/laravel-avatar-manager.svg?style=flat-square)](https://packagist.org/packages/mapo-89/laravel-avatar-manager)
![GitHub Actions](https://github.com/mapo-89/laravel-avatar-manager/actions/workflows/main.yml/badge.svg)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

**Laravel Avatar Manager** is a lightweight, self-hosted Laravel package for managing user avatars - with support for Gravatar-compatible hashes, local storage and easy integration into existing projects.

📖 This README is also available in [🇩🇪 German](README.de.md).

---

## ✨ Features

- Avatar URLs based on MD5(Email), as with Gravatar
- Fallback to default avatars
- Seamless integration with the Laravel user model
- Local storage of uploaded avatars
- API-based avatar upload using email + API key (no login required)
- SQLite compatible ✅

## 🚀 API Avatar Upload Endpoint

The package provides an optional upload endpoint without requiring user registration. 

### `POST /api/avatars/upload`

**Headers:**
- `X-API-KEY`: A valid API key defined in `config/avatar-manager.php`

**Body Parameters:**
- `email` (string, required) – The email address used to compute the avatar hash
- `avatar` (image, required) – The uploaded avatar image (max 2MB)

**Response:**

- `200 OK`: Avatar successfully uploaded  
- `401 Unauthorized`: Missing or invalid API key  
- `422 Unprocessable Entity`: Validation failed (e.g. invalid email or image)  
- `409 Conflict`: Upload aborted – user already has an existing profile photo  

> ℹ️ If a user exists in your system and already has a `profile_photo_path` set, the API will reject a new avatar upload via the endpoint to avoid unintended overrides.

The uploaded image will be saved to:

```bash
storage/app/public/avatars/{md5(email)}.jpg
```

### Configuration

Add one or more API keys to your config:

```php
// config/avatar-manager.php
'api_keys' => [
    env('AVATAR_API_KEY'),
],
```

And in your .env file:

```php
AVATAR_API_KEY=your-api-key
```

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

To keep the package flexible and testable, the AvatarManager uses an interface for accessing user data:
`Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface`

For tests, the test user class is bound via `TestUserProvider`. 

#### Production operation

In the service provider, the package binds the real user class (e.g. App\Models\User) by default:

```php
public function register()
{
 $this->app->bind(
 \Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface::class,
 \Mapo89\LaravelAvatarManager\Services\UserProvider::class
 );
}
```
The UserProvider class implements the logic to find users via email hash.

#### Execute tests

The tests can be executed with various commands:

```bash
composer test

./vendor/bin/phpunit --testdox --stderr

./vendor/bin/pest
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
