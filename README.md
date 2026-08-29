# Laravel Dump Server

[![Latest Version on Packagist](https://img.shields.io/packagist/v/anil/dump.svg?style=flat-square)](https://packagist.org/packages/anil/dump)
[![Total Downloads](https://img.shields.io/packagist/dt/anil/dump.svg?style=flat-square)](https://packagist.org/packages/anil/dump)
[![Tests](https://github.com/anilkumarthakur60/dump/actions/workflows/tests.yml/badge.svg)](https://github.com/anilkumarthakur60/dump/actions/workflows/tests.yml)
[![License](https://img.shields.io/packagist/l/anil/dump.svg?style=flat-square)](LICENSE)

A Laravel package that intercepts `dump()` and `dd()` calls and streams them to a dedicated terminal server  keeping your browser responses clean while you debug.

Inspired by Symfony's `var-dump-server`, built for the Laravel ecosystem.

---

## Features

- Intercepts all `dump()` / `dd()` calls and forwards them to a running terminal server
- Falls back to CLI or HTML output when the server is not running
- Attaches request context (URI, method, controller) and a stack trace to every dump
- Optional log channel support  write every dump to a Laravel log channel
- Global helpers: `dump_if()`, `dump_unless()`, `dd_if()`, `dd_unless()`
- `DumpFake` testing utility with rich PHPUnit assertions
- Production guard  the handler is never registered when `APP_ENV=production`
- Configurable clone depth and item limits via environment variables

---

## Requirements

| Dependency | Version |
|---|---|
| PHP | ^8.2 |
| Laravel | ^11.0 \| ^12.0 \| ^13.0 |
| symfony/var-dumper | ^7.0 \| ^8.0 |

---

## Installation

Install as a **dev dependency** (recommended  debug tooling should not ship to production):

```bash
composer require anil/dump --dev
```

If you need the dump server available in production (e.g. for staging environments or log-channel mirroring), install it as a regular dependency instead:

```bash
composer require anil/dump
```

The service provider is auto-discovered. Then run the install command to publish the config and add the required environment variables:

```bash
php artisan dump:install
```

This will:
- Publish `config/dump-server.php`
- Append `DUMP_SERVER_HOST`, `DUMP_SERVER_ENABLED`, and `DUMP_SERVER_LOG_ENABLED` to both `.env` and `.env.example`

---

## Usage

### 1. Start the dump server

In a separate terminal, start the server before making requests:

```bash
php artisan dump:server
```

Output HTML instead of CLI output:

```bash
php artisan dump:server --format=html
```

### 2. Call `dump()` or `dd()` as usual

```php
dump($user);
dump(['key' => 'value']);
dd($request->all());
```

When the server is running, output is captured and displayed in the server terminal  with request context and a stack trace  instead of inline in the browser.

When the server is **not** running, `dump()` falls back to normal CLI or HTML output.

---

## Helper Functions

Four conditional helpers are available globally:

```php
// Dump only when the condition is true
dump_if($user->isAdmin(), $user);

// Dump only when the condition is false
dump_unless($request->isJson(), $request->all());

// Die-and-dump only when the condition is true
dd_if(app()->isLocal(), $response);

// Die-and-dump only when the condition is false
dd_unless($feature->isEnabled(), $payload);
```

---

## Configuration

Publish the config file manually if needed:

```bash
php artisan vendor:publish --tag=dump-server-config
```

`config/dump-server.php`:

```php
return [
    // TCP host the server listens on
    'host' => env('DUMP_SERVER_HOST', 'tcp://127.0.0.1:9912'),

    // Set to false to disable the handler entirely in an environment
    'enabled' => env('DUMP_SERVER_ENABLED', true),

    // VarCloner limits  increase for deeply nested structures
    'max_depth' => env('DUMP_SERVER_MAX_DEPTH', 10),
    'max_items' => env('DUMP_SERVER_MAX_ITEMS', 2500),

    // Optionally mirror every dump to a log channel
    'log' => [
        'enabled' => env('DUMP_SERVER_LOG_ENABLED', false),
        'channel' => env('DUMP_SERVER_LOG_CHANNEL', 'stack'),
        'level'   => env('DUMP_SERVER_LOG_LEVEL', 'debug'),
    ],
];
```

### Environment variables

| Variable | Default | Description |
|---|---|---|
| `DUMP_SERVER_HOST` | `tcp://127.0.0.1:9912` | TCP address the server binds to |
| `DUMP_SERVER_ENABLED` | `true` | Set to `false` to disable the handler |
| `DUMP_SERVER_MAX_DEPTH` | `10` | Maximum clone depth for nested objects |
| `DUMP_SERVER_MAX_ITEMS` | `2500` | Maximum number of items cloned |
| `DUMP_SERVER_LOG_ENABLED` | `false` | Mirror dumps to a log channel |
| `DUMP_SERVER_LOG_CHANNEL` | `stack` | Log channel to write to |
| `DUMP_SERVER_LOG_LEVEL` | `debug` | PSR-3 log level |

---

## Testing

The package ships with `DumpFake`, a drop-in test helper that captures dumps and provides PHPUnit assertions.

```php
use Anil\Dump\Testing\DumpFake;

$fake = DumpFake::fake();

dump('hello');
dump(42);
dump(['a' => 1]);

// Assert a specific value was dumped
$fake->assertDumped('hello');

// Assert a value was NOT dumped
$fake->assertNotDumped('world');

// Assert nothing was dumped
$fake->assertNothingDumped();

// Assert the total number of dumps
$fake->assertDumpedCount(3);

// Assert using a custom callback
$fake->assertDumpedUsing(fn ($v) => is_array($v) && isset($v['a']));

// Retrieve all captured values
$dumped = $fake->getDumped();

// Restore the original handler when done
$fake->restore();
```

### Running the test suite

```bash
composer test
```

---

## Static Analysis

The package is analysed at PHPStan level 10 with Larastan:

```bash
composer analyse
```

---

## Code Style

Formatting is enforced with Laravel Pint:

```bash
composer format       # fix
composer format:check # check only
```

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for release history.

---

## License

The MIT License (MIT). See [LICENSE](LICENSE) for details.
