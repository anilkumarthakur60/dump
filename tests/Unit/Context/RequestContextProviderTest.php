<?php

use Anil\Dump\Context\RequestContextProvider;
use Illuminate\Http\Request;

it('returns null when no request is provided', function () {
    $provider = new RequestContextProvider;

    expect($provider->getContext())->toBeNull();
});

it('returns context array for a given request', function () {
    $request = Request::create('/test-uri', 'GET');
    $provider = new RequestContextProvider($request);

    $context = $provider->getContext();

    expect($context)
        ->toBeArray()
        ->toHaveKeys(['uri', 'method', 'controller', 'identifier'])
        ->and($context['uri'])->toContain('/test-uri')
        ->and($context['method'])->toBe('GET');
});

it('returns a unique identifier per request instance', function () {
    $requestA = Request::create('/a', 'GET');
    $requestB = Request::create('/b', 'POST');

    $contextA = (new RequestContextProvider($requestA))->getContext();
    $contextB = (new RequestContextProvider($requestB))->getContext();

    expect($contextA['identifier'])->not->toBe($contextB['identifier']);
});
