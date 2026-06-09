<?php

declare(strict_types=1);

use LaraMint\LaravelBrain\Analysis\ContainerBindingRecord;
use LaraMint\LaravelBrain\Analysis\ContainerBindingRegistry;
use LaraMint\LaravelBrain\Analysis\FacadeAnalyzer;
use LaraMint\LaravelBrain\Analysis\FacadeRecord;

it('discovers a facade via multi-level inheritance (class → abstract parent → Facade)', function () {
    $registry = (new FacadeAnalyzer)->analyze(fixture('laravel-project'));

    // ShortUrlV3Facade extends AbstractVersionedShortUrlFacade extends Facade.
    // getFacadeAccessor() is defined on the abstract parent, not on the concrete class.
    $record = $registry->get('App\Services\V3\ShortUrlV3Facade');

    expect($record)
        ->toBeInstanceOf(FacadeRecord::class)
        ->accessor->toBe('App\Services\V3\ShortUrlV3Service')
        ->concreteFqcn->toBe('App\Services\V3\ShortUrlV3Service');
});

it('does not register abstract intermediate facade classes', function () {
    $registry = (new FacadeAnalyzer)->analyze(fixture('laravel-project'));

    // AbstractVersionedShortUrlFacade is abstract and must not appear in the registry.
    expect($registry)
        ->get('App\Services\V3\AbstractVersionedShortUrlFacade')
        ->toBeNull();
});

it('discovers a facade that returns a string key accessor', function () {
    $registry = (new FacadeAnalyzer)->analyze(fixture('laravel-project'));

    $record = $registry->get('App\Services\V3\ShortUrlV3KeyFacade');

    expect($record)->toBeInstanceOf(FacadeRecord::class)
        ->accessor->toBe('short-url-v3')
        ->concreteFqcn->toBeNull();
});

it('resolves string-key accessor via container binding registry', function () {
    $facadeRegistry = (new FacadeAnalyzer)->analyze(fixture('laravel-project'));

    $bindings = new ContainerBindingRegistry;
    $bindings->add(new ContainerBindingRecord(
        abstractFqcn: 'short-url-v3',
        concreteFqcn: 'App\Services\V3\ShortUrlV3Service',
        providerFqcn: 'App\Providers\AppServiceProvider',
        kind: 'singleton',
    ));

    $facadeRegistry->resolveWith($bindings);

    $record = $facadeRegistry->get('App\Services\V3\ShortUrlV3KeyFacade');
    expect($record?->concreteFqcn)->toBe('App\Services\V3\ShortUrlV3Service');
});

it('returns an empty registry for a project without an app/ directory', function () {
    $registry = (new FacadeAnalyzer)->analyze('/nonexistent/path');
    expect($registry->all())->toBeEmpty();
});

it('does not register non-facade classes', function () {
    $registry = (new FacadeAnalyzer)->analyze(fixture('laravel-project'));

    expect($registry->get('App\Services\V3\ShortUrlV3Service'))->toBeNull();
});

it('resolveWith does not overwrite an already-resolved concreteFqcn', function () {
    $facadeRegistry = (new FacadeAnalyzer)->analyze(fixture('laravel-project'));

    $bindings = new ContainerBindingRegistry;
    $bindings->add(new ContainerBindingRecord(
        abstractFqcn: 'App\Services\V3\ShortUrlV3Service',
        concreteFqcn: 'App\Services\V3\SomeOtherService',
        providerFqcn: 'App\Providers\AppServiceProvider',
        kind: 'singleton',
    ));

    $facadeRegistry->resolveWith($bindings);

    // The ::class accessor was already resolved — must not be overwritten.
    $record = $facadeRegistry->get('App\Services\V3\ShortUrlV3Facade');
    expect($record?->concreteFqcn)->toBe('App\Services\V3\ShortUrlV3Service');
});
