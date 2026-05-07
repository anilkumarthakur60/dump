<?php

use Anil\Dump\Context\StackTraceContextProvider;

it('returns context with a stack key', function () {
    $provider = new StackTraceContextProvider;
    $context = $provider->getContext();

    expect($context)->toBeArray()->toHaveKey('stack');
});

it('stack frames are arrays with expected keys', function () {
    $provider = new StackTraceContextProvider;
    $context = $provider->getContext();

    expect($context['stack'])->toBeArray();

    if (count($context['stack']) > 0) {
        expect($context['stack'][0])->toHaveKeys(['file', 'line', 'function']);
    }
});

it('filters symfony var-dumper vendor frames', function () {
    $provider = new StackTraceContextProvider;
    $context = $provider->getContext();

    foreach ($context['stack'] as $frame) {
        expect($frame['file'])->not->toContain('/vendor/symfony/var-dumper/');
    }
});
