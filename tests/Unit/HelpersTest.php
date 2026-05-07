<?php

use Anil\Dump\Testing\DumpFake;

it('dump_if dumps when condition is true', function () {
    $fake = DumpFake::fake();

    dump_if(true, 'dumped');

    $fake->assertDumped('dumped');
    $fake->restore();
});

it('dump_if does not dump when condition is false', function () {
    $fake = DumpFake::fake();

    dump_if(false, 'skipped');

    $fake->assertNothingDumped();
    $fake->restore();
});

it('dump_unless dumps when condition is false', function () {
    $fake = DumpFake::fake();

    dump_unless(false, 'dumped');

    $fake->assertDumped('dumped');
    $fake->restore();
});

it('dump_unless does not dump when condition is true', function () {
    $fake = DumpFake::fake();

    dump_unless(true, 'skipped');

    $fake->assertNothingDumped();
    $fake->restore();
});
