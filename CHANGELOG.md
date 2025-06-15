# Changelog

All notable changes to `laravel-avatar-manager` will be documented in this file

## [1.1.0] - 2025-06-15

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

## 1.0.1 - 2025-06-15

- 🐛 Fix: wrong config-path

## 1.0.0 - 2025-06-15

- initial release
