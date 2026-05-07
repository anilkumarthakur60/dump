# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2025-05-06

### Added
- `DumpServerServiceProvider` with auto-discovery for Laravel
- `DumpServerCommand` (`php artisan dump-server`) with `--format=cli|html` option
- `Dumper` with configurable `max_depth` and `max_items` via `VarCloner`
- `RequestContextProvider` — attaches URI, method, controller, and request identifier to each dump
- `StackTraceContextProvider` — attaches application stack trace (up to 5 frames) to each dump
- `DumpFake` testing utility with `fake()`, `assertDumped()`, `assertNotDumped()`, `assertNothingDumped()`, `assertDumpedCount()`, `assertDumpedUsing()`
- `InstallCommand` (`php artisan dump:install`) — publishes config and appends env vars
- Global helper functions: `dump_if()`, `dump_unless()`, `dd_if()`, `dd_unless()`
- Production guard — VarDumper handler is not set when `app()->isProduction()` is true
- Optional log channel support via `dump-server.log.*` config
- `dump-server.enabled` config key to disable per environment
- PHPStan level 10 with Larastan
- Pest test suite
