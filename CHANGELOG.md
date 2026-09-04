# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.2] - 2026-05-08

### Changed
- Updated README installation section to document both `--dev` and production (`require`) install paths

## [0.1.1] - 2026-05-07

### Changed
- Append source file path and line number to dump context entries

## [0.1.0] - 2026-05-07

### Added

#### Package structure
- PSR-4 namespace hierarchy: `Anil\Dump\Commands`, `Anil\Dump\Context`, `Anil\Dump\Testing`
- `DumpServerServiceProvider` with Laravel auto-discovery (`extra.laravel.providers`)

#### Commands
- `DumpServerCommand`  `php artisan dump:server` with `--host` option to override the TCP address
- `InstallCommand`  `php artisan dump:install`  publishes `config/dump-server.php` and appends `DUMP_SERVER_HOST` / `DUMP_SERVER_ENABLED` to `.env`

#### Core classes
- `Config`  typed value object built from the config repository via `Config::fromRepository()`
- `DumpHandler`  extracted dump + optional log-channel handler, invoked by the service provider
- `Dumper`  configurable `max_depth` / `max_items` via `VarCloner`

#### Context providers
- `RequestContextProvider`  attaches `uri`, `method`, `controller`, and per-request `identifier` to each dump
- `TraceContextProvider`  attaches an application stack trace (up to 5 frames) to each dump

#### Testing
- `DumpFake`  test double with `fake()`, `restore()`, `getDumped()`, `assertDumped()`, `assertNotDumped()`, `assertNothingDumped()`, `assertDumpedCount()`, `assertDumpedUsing()`

#### Helpers
- Global helper functions: `dump_if()`, `dump_unless()`, `dd_if()`, `dd_unless()`

#### Configuration (`config/dump-server.php`)
- `host`  TCP address, default `tcp://127.0.0.1:9912` (env `DUMP_SERVER_HOST`)
- `enabled`  per-environment toggle (env `DUMP_SERVER_ENABLED`)
- `log`  optional log-channel forwarding with `channel`, `level`, and `max_depth` / `max_items` keys
- Production guard  handler is not registered when `app()->isProduction()` returns `true`

#### Dependencies
- Granular `illuminate/*` packages (`config`, `console`, `http`, `log`, `support`) instead of the full framework  consumers get precise dependency declarations
- Supports Laravel 11, 12, and 13 on PHP 8.2, 8.3, and 8.4
- `symfony/var-dumper` `^7.0|^8.0`

#### CI / tooling
- PHPStan level 10 with Larastan; `phpVersion: 80200` ensures PHP 8.2 source compatibility is verified statically
- Pest 4 + PHPUnit 12 test suite
- GitHub Actions workflow with three jobs: `lint` (Pint format check + PHPStan), `php82` (install-only smoke test on PHP 8.2), `tests` (full matrix PHP 8.3 × 8.4 × L11/L12/L13 and PHP 8.5 × L12/L13)
- `fix-style.yml`  auto-commits Pint fixes on push
- `auto-merge.yml`  automatically approves and merges patch and minor Dependabot PRs
