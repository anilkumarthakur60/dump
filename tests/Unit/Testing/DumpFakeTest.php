<?php

use Anil\Dump\Testing\DumpFake;
use Symfony\Component\VarDumper\VarDumper;

afterEach(fn () => VarDumper::setHandler(null));

it('records dumped values', function () {
    $fake = new DumpFake;
    $fake->dump('hello');
    $fake->dump(42);

    $fake->assertDumpedCount(2);
    $fake->assertDumped('hello');
    $fake->assertDumped(42);
});

it('asserts nothing was dumped', function () {
    $fake = new DumpFake;
    $fake->assertNothingDumped();
});

it('asserts a value was not dumped', function () {
    $fake = new DumpFake;
    $fake->dump('actual');
    $fake->assertNotDumped('other');
});

it('asserts dumped using a callback', function () {
    $fake = new DumpFake;
    $fake->dump(['key' => 'value']);
    $fake->assertDumpedUsing(fn ($v) => is_array($v) && ($v['key'] ?? null) === 'value');
});

it('intercepts VarDumper via fake()', function () {
    $fake = DumpFake::fake();

    dump('intercepted');

    $fake->assertDumped('intercepted');
    $fake->assertDumpedCount(1);
});

it('restores the original handler without affecting recorded dumps', function () {
    $fake = DumpFake::fake();
    $fake->dump('before');
    $fake->restore();

    expect($fake->getDumped())->toBe(['before']);
});
