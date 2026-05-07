<?php

use Anil\Dump\Config;
use Illuminate\Config\Repository;

it('builds from a repository with all values present', function () {
    $config = Config::fromRepository(new Repository(['dump-server' => [
        'enabled' => false,
        'host' => 'tcp://0.0.0.0:1234',
        'max_depth' => 3,
        'max_items' => 10,
        'log' => ['enabled' => true, 'channel' => 'single', 'level' => 'info'],
    ]]));

    expect($config->enabled)->toBeFalse()
        ->and($config->host)->toBe('tcp://0.0.0.0:1234')
        ->and($config->maxDepth)->toBe(3)
        ->and($config->maxItems)->toBe(10)
        ->and($config->logEnabled)->toBeTrue()
        ->and($config->logChannel)->toBe('single')
        ->and($config->logLevel)->toBe('info');
});

it('falls back to defaults when values are missing or wrongly typed', function () {
    $config = Config::fromRepository(new Repository(['dump-server' => [
        'host' => '',
        'max_depth' => 'not-a-number',
        'max_items' => '99',
    ]]));

    expect($config->enabled)->toBeTrue()
        ->and($config->host)->toBe('tcp://127.0.0.1:9912')
        ->and($config->maxDepth)->toBe(10)
        ->and($config->maxItems)->toBe(99)
        ->and($config->logEnabled)->toBeFalse()
        ->and($config->logChannel)->toBe('stack')
        ->and($config->logLevel)->toBe('debug');
});
