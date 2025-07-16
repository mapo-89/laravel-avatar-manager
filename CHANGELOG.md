# Changelog

All notable changes to `laravel-avatar-manager` will be documented in this file

## 1.2.0 - 2025-07-16

### Added
- New `POST /api/avatars/upload` endpoint for uploading avatars via API key
- Middleware `avatar-manager.api_key` for validating `X-API-KEY` headers
- Config option `avatar-manager.api_keys` for whitelisting upload keys
- Support for dynamically resolving avatar files by hash from `storage/app/public/avatars/`
- Integration tests for avatar upload and avatar resolution flow
- Controller tests for image fallback handling
- Curl example and Postman test instructions in README

### Changed
- `AvatarController@show` now first checks the `avatars/{hash}.{ext}` path before falling back to profile photos or default images

### Fixed
- Return proper `image/*` Content-Type for avatar files
- Prevent error when `App\Models\User` is not present during test runs

--- 

## 1.1.0 - 2025-06-15

### Added
- `UserProviderInterface` for flexible implementation of user-defined user accesses
- `UserProvider` as standard class for App\Models\User
- `TestUserProvider` for controlled tests without real users
- Tests for avatar routing, file existence and fallback logic
- Documentation for test setup & interface binding

### Changed
- Avatar route no longer works directly with App\Models\User, but uses the interface

### Fixed
- Improved error handling for missing avatar files

---

## 1.0.1 - 2025-06-15

- 🐛 Fix: wrong config-path

---

## 1.0.0 - 2025-06-15

- initial release
