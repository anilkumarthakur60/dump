<?php

use Anil\Dump\Dumper;
use Anil\Dump\DumpHandler;
use Psr\Log\LoggerInterface;

it('forwards the value to the dumper', function () {
    $dumper = Mockery::mock(Dumper::class);
    $dumper->shouldReceive('dump')->once()->with('payload');

    (new DumpHandler($dumper))('payload');

    expect(true)->toBeTrue();
});

it('logs the dumped value when a logger is provided', function () {
    $dumper = Mockery::mock(Dumper::class);
    $dumper->shouldReceive('dump')->once();

    $logger = Mockery::mock(LoggerInterface::class);
    $logger->shouldReceive('log')
        ->once()
        ->with('warning', 'dump: string', ['value' => 'payload']);

    (new DumpHandler($dumper, $logger, 'warning'))('payload');

    expect(true)->toBeTrue();
});
