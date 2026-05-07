<?php

use Anil\Dump\Context\TraceContextProvider;

it('returns context with a stack key', function () {
    expect((new TraceContextProvider)->getContext())
        ->toBeArray()
        ->toHaveKey('stack');
});

it('stack frames are arrays with expected keys', function () {
    $context = (new TraceContextProvider)->getContext();

    expect($context['stack'])->toBeArray();

    if (count($context['stack']) > 0) {
        expect($context['stack'][0])->toHaveKeys(['file', 'line', 'function']);
    }
});

it('filters symfony var-dumper vendor frames', function () {
    $context = (new TraceContextProvider)->getContext();

    foreach ($context['stack'] as $frame) {
        expect($frame['file'])->not->toContain('/vendor/symfony/var-dumper/');
    }
});

it('honors the configured frame limit', function () {
    $context = (new TraceContextProvider(limit: 1))->getContext();

    expect($context['stack'])->toHaveCount(1);
});
