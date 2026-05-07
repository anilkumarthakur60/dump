<?php

use Anil\Dump\Commands\DumpServerCommand;
use Anil\Dump\Commands\InstallCommand;
use Anil\Dump\Config;
use Anil\Dump\Dumper;

it('registers the dump-server commands', function () {
    expect($this->app->make(DumpServerCommand::class))->toBeInstanceOf(DumpServerCommand::class)
        ->and($this->app->make(InstallCommand::class))->toBeInstanceOf(InstallCommand::class);
});

it('binds the typed config and dumper', function () {
    expect($this->app->make(Config::class))->toBeInstanceOf(Config::class)
        ->and($this->app->make(Dumper::class))->toBeInstanceOf(Dumper::class);
});

it('has the correct default host configuration', function () {
    expect(config('dump-server.host'))->toBe('tcp://127.0.0.1:9912');
});

it('respects DUMP_SERVER_HOST environment variable', function () {
    $this->app['config']->set('dump-server.host', 'tcp://0.0.0.0:9912');

    expect(config('dump-server.host'))->toBe('tcp://0.0.0.0:9912');
});
