<?php

use Anil\Dump\Commands\DumpServerCommand;

it('registers the dump-server command', function () {
    expect($this->app->bound('command.dump-server'))->toBeTrue();
});

it('resolves DumpServerCommand from the container', function () {
    expect($this->app->make('command.dump-server'))->toBeInstanceOf(DumpServerCommand::class);
});

it('has the correct default host configuration', function () {
    expect(config('dump-server.host'))->toBe('tcp://127.0.0.1:9912');
});

it('respects DUMP_SERVER_HOST environment variable', function () {
    $this->app['config']->set('dump-server.host', 'tcp://0.0.0.0:9912');

    expect(config('dump-server.host'))->toBe('tcp://0.0.0.0:9912');
});
