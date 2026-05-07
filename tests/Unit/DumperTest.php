<?php

use Anil\Dump\Dumper;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Server\Connection;

it('dumps a scalar value without throwing', function () {
    $dumper = new Dumper;
    $dumper->dump('hello');

    expect($dumper)->toBeInstanceOf(Dumper::class);
});

it('dumps a complex value without throwing', function () {
    $dumper = new Dumper;
    $dumper->dump(['key' => 'value', 'nested' => [1, 2, 3]]);

    expect($dumper)->toBeInstanceOf(Dumper::class);
});

it('writes to connection when available', function () {
    $connection = Mockery::mock(Connection::class);
    $connection->shouldReceive('write')
        ->once()
        ->with(Mockery::type(Data::class))
        ->andReturn(true);

    $dumper = new Dumper($connection);
    $dumper->dump('test');
});

it('falls back to local dumper when connection write fails', function () {
    $connection = Mockery::mock(Connection::class);
    $connection->shouldReceive('write')
        ->once()
        ->andReturn(false);

    $dumper = new Dumper($connection);
    $dumper->dump('fallback test');
});
