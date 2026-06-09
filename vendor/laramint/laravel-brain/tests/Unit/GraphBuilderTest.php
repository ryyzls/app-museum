<?php

use LaraMint\LaravelBrain\Analysis\ContainerBindingAnalyzer;
use LaraMint\LaravelBrain\Analysis\ControllerAnalyzer;
use LaraMint\LaravelBrain\Analysis\MethodTracer;
use LaraMint\LaravelBrain\Analysis\MiddlewareRegistry;
use LaraMint\LaravelBrain\Analysis\ModelAnalyzer;
use LaraMint\LaravelBrain\Analysis\RouteAnalyzer;
use LaraMint\LaravelBrain\Graph\Edge;
use LaraMint\LaravelBrain\Graph\GraphBuilder;
use LaraMint\LaravelBrain\Graph\Node;

it('builds a graph with nodes and edges from fixture project', function () {
    $routes = (new RouteAnalyzer)->analyze(fixture('laravel-project'));
    $middlewareRegistry = new MiddlewareRegistry([], [], []);
    $controllers = (new ControllerAnalyzer)->analyze(fixture('laravel-project'), $routes);
    $traces = (new MethodTracer)->trace($controllers);
    $modelFqcns = array_map(fn ($t) => $t->calleeFqcn, array_filter($traces, fn ($t) => $t->type === 'model'));
    $models = (new ModelAnalyzer)->analyze(fixture('laravel-project'), $modelFqcns);

    $graph = (new GraphBuilder)->build('test', $routes, $middlewareRegistry, $controllers, $traces, $models);

    expect($graph->nodeCount())->toBeGreaterThan(0);
    expect($graph->edgeCount())->toBeGreaterThan(0);
});

it('produces valid JSON output', function () {
    $routes = (new RouteAnalyzer)->analyze(fixture('laravel-project'));
    $middlewareRegistry = new MiddlewareRegistry([], [], []);
    $controllers = (new ControllerAnalyzer)->analyze(fixture('laravel-project'), $routes);
    $traces = (new MethodTracer)->trace($controllers);

    $modelFqcns = array_map(fn ($t) => $t->calleeFqcn, array_filter($traces, fn ($t) => $t->type === 'model'));
    $models = (new ModelAnalyzer)->analyze(fixture('laravel-project'), $modelFqcns);

    $graph = (new GraphBuilder)->build('test', $routes, $middlewareRegistry, $controllers, $traces, $models);

    expect($graph->toJson())
        ->json()
        ->toHaveKeys(['meta', 'nodes', 'edges'])
        ->meta->toBeArray()->toHaveKey('project')
        ->nodes->toBeNonEmptyArray()
        ->edges->toBeNonEmptyArray();
});

it('creates route nodes for each route', function () {
    $routes = (new RouteAnalyzer)->analyze(fixture('laravel-project'));
    $middlewareRegistry = new MiddlewareRegistry([], [], []);
    $controllers = (new ControllerAnalyzer)->analyze(fixture('laravel-project'), $routes);
    $traces = (new MethodTracer)->trace($controllers);
    $models = [];

    $graph = (new GraphBuilder)->build('test', $routes, $middlewareRegistry, $controllers, $traces, $models);

    $routeNodes = array_filter($graph->nodes(), fn ($n) => $n->type === 'route');

    expect($routeNodes)->toHavecount(count($routes));
});

it('exposes parent controller nodes and extends edges for inherited actions', function () {
    $routes = (new RouteAnalyzer)->analyze(fixture('laravel-project'));
    $middlewareRegistry = new MiddlewareRegistry([], [], []);
    $controllers = (new ControllerAnalyzer)->analyze(fixture('laravel-project'), $routes);
    $traces = (new MethodTracer)->trace($controllers);
    $models = [];

    $graph = (new GraphBuilder)->build('test', $routes, $middlewareRegistry, $controllers, $traces, $models, fixture('laravel-project'));

    $extends = array_values(array_filter($graph->edges(), fn ($e) => $e->type === 'controller-extends'));

    expect($extends)
        ->toBeArray()
        ->toHaveCount(1)
        ->andArrayFirstElement()
        ->toBeInstanceOf(Edge::class);

    $ids = array_map(fn ($n) => $n->id, $graph->nodes());

    expect($ids)
        ->toBeArray()
        ->toHaveCount(52)
        ->toContain('controller::App\\Http\\Controllers\\V3\\AbstractThingController');

    $handlesFromParent = array_filter(
        $graph->edges(),
        fn ($e) => $e->type === 'controller-to-action'
            && $e->source === 'controller::App\\Http\\Controllers\\V3\\AbstractThingController'
    );

    expect($handlesFromParent)
        ->toBeArray()
        ->andArrayFirstElement()
        ->toBeInstanceOf(Edge::class);
});

it('wires form request validated nodes and exposes validationRules on graph nodes', function () {
    $routes = (new RouteAnalyzer)->analyze(fixture('laravel-project'));
    $middlewareRegistry = new MiddlewareRegistry([], [], []);
    $analyzer = new ControllerAnalyzer;
    $controllers = $analyzer->analyze(fixture('laravel-project'), $routes);
    $traces = (new MethodTracer)->trace($controllers, $analyzer->getPsr4Map(), fixture('laravel-project'));
    $models = [];

    $graph = (new GraphBuilder)->build('test', $routes, $middlewareRegistry, $controllers, $traces, $models, fixture('laravel-project'));

    $frEdges = array_values(array_filter($graph->edges(), fn ($e) => $e->type === 'action-to-form-request'));

    expect($frEdges)
        ->toBeArray()
        ->andArrayFirstElement()
        ->toBeInstanceOf(Edge::class);

    $formRequestNodes = array_values(array_filter(
        $graph->nodes(),
        fn ($n) => ($n->data['fqcn'] ?? '') === 'App\\Http\\Requests\\ProfileStoreRequest'
            && ($n->data['method'] ?? '') === 'validated'
    ));

    expect($formRequestNodes)
        ->toBeArray()
        ->andArrayFirstElement()
        ->toBeInstanceOf(Node::class)
        ->data->validationRules->toBeNonEmptyArray()
        ->type->toBe('validation_request');
});

it('adds IoC binding edges from service providers to interfaces and implementations', function () {
    $routes = (new RouteAnalyzer)->analyze(fixture('laravel-project'));
    $middlewareRegistry = new MiddlewareRegistry([], [], []);
    $analyzer = new ControllerAnalyzer;
    $controllers = $analyzer->analyze(fixture('laravel-project'), $routes);
    $traces = (new MethodTracer)->trace($controllers, $analyzer->getPsr4Map(), fixture('laravel-project'));
    $models = [];
    $bindings = (new ContainerBindingAnalyzer)->analyze(fixture('laravel-project'));

    $graph = (new GraphBuilder)->build('test', $routes, $middlewareRegistry, $controllers, $traces, $models, fixture('laravel-project'), [], $bindings);

    $types = array_map(fn ($e) => $e->type, $graph->edges());

    expect($types)
        ->toBeArray()
        ->toHaveCount(63)
        ->toContain('binding-resolution', 'binding-registered-in');

    $resolution = array_values(array_filter($graph->edges(), fn ($e) => $e->type === 'binding-resolution'));

    expect($resolution)
        ->toBeArray()
        ->andArrayFirstElement()
        ->toBeInstanceOf(Edge::class)
        ->label->toContain('SqlThingRepository');
});
