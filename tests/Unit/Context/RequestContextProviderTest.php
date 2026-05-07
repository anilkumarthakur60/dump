<?php

use Anil\Dump\Context\RequestContextProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\VarDumper\Cloner\Data;

it('returns null when no request is provided', function () {
    expect((new RequestContextProvider)->getContext())->toBeNull();
});

it('returns context array for a given request', function () {
    $request = Request::create('/test-uri', 'GET');
    $context = (new RequestContextProvider($request))->getContext();

    expect($context)
        ->toBeArray()
        ->toHaveKeys(['uri', 'method', 'controller', 'identifier'])
        ->and($context['uri'])->toContain('/test-uri')
        ->and($context['method'])->toBe('GET');
});

it('returns a unique identifier per request instance', function () {
    $reqA = Request::create('/a', 'GET');
    $reqB = Request::create('/b', 'POST');

    $a = (new RequestContextProvider($reqA))->getContext();
    $b = (new RequestContextProvider($reqB))->getContext();

    expect($a['identifier'])->not->toBe($b['identifier']);
});

it('resolves the controller name from a string-form Controller@method action', function () {
    $request = Request::create('/x', 'GET');
    $route = new Route(['GET'], '/x', ['uses' => 'App\\Http\\Controllers\\HomeController@index']);
    $request->setRouteResolver(fn () => $route);

    $context = (new RequestContextProvider($request))->getContext();

    expect($context['controller'])->toBeInstanceOf(Data::class)
        ->and((string) $context['controller'])->toContain('HomeController');
});

it('resolves the controller name from a closure action', function () {
    $request = Request::create('/y', 'GET');
    $route = new Route(['GET'], '/y', ['uses' => fn () => 'ok']);
    $request->setRouteResolver(fn () => $route);

    $context = (new RequestContextProvider($request))->getContext();

    expect($context['controller'])->toBeInstanceOf(Data::class);
});
