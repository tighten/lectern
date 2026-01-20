# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.1] - 2026-01-20

### Changed
- Migrations now auto-load from vendor directory using `loadMigrationsFrom()`
- Users can still publish migrations for customization via `php artisan vendor:publish --tag=lectern-migrations`

### Fixed
- Removed duplicate `is_admin_only` column migration that conflicted with the create table migration

## [1.0.0] - 2025-01-20

### Added
- Initial release
- Categories with full CRUD operations
- Threads with pinning, locking, and soft deletes
- Posts with reactions and mentions
- User subscriptions for threads and categories
- User bans and content reporting
- Full-text search with database and Scout drivers
- Configurable authentication middleware
- Event dispatching for all major actions
- Authorization policies for categories, threads, and posts
