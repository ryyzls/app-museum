<?php

use App\Http\Controllers\V3\AbstractThingController;
use LaraMint\LaravelBrain\Analysis\CallChainEdge;
use LaraMint\LaravelBrain\Analysis\ControllerAnalyzer;
use LaraMint\LaravelBrain\Analysis\ControllerDefinition;
use LaraMint\LaravelBrain\Analysis\ControllerMiddleware;
use LaraMint\LaravelBrain\Analysis\MethodTracer;
use LaraMint\LaravelBrain\Analysis\RouteAnalyzer;

test('HasMiddleware::appliesToAction() respects only/except parameters', function () {

    (new RouteAnalyzer)->analyze(fixture('laravel-project'));

    expect(new ControllerMiddleware('auth'))
        ->appliesToAction('index')->toBeTrue()
        ->appliesToAction('destroy')->toBeTrue();

    expect(new ControllerMiddleware('log', only: ['index', 'show']))
        ->appliesToAction('index')->toBeTrue()
        ->appliesToAction('show')->toBeTrue()
        ->appliesToAction('store')->toBeFalse();

    expect(new ControllerMiddleware('subscribed', except: ['index']))->appliesToAction('index')->toBeFalse()
        ->appliesToAction('store')->toBeTrue();
});

it('resolves controller files from routes', function () {
    $routes = (new RouteAnalyzer)->analyze(fixture('laravel-project'));
    $controllers = (new ControllerAnalyzer)->analyze(fixture('laravel-project'), $routes);

    expect($controllers)
        ->ToBeArray()
        ->each->toBeInstanceOf(ControllerDefinition::class);
});

it('extracts constructor dependencies', function () {
    $routes = (new RouteAnalyzer)->analyze(fixture('laravel-project'));
    $controllers = (new ControllerAnalyzer)->analyze(fixture('laravel-project'), $routes);

    $authController = null;
    foreach ($controllers as $c) {
        if (str_contains($c->fqcn, 'AuthController')) {
            $authController = $c;
            break;
        }
    }
    expect($authController)
        ->toBeInstanceOf(ControllerDefinition::class)
        ->constructorDeps->toHaveKey('authService');
});

it('extracts HasMiddleware static middleware() declarations', function () {
    $routes = (new RouteAnalyzer)->analyze(fixture('laravel-project'));
    $controllers = (new ControllerAnalyzer)->analyze(fixture('laravel-project'), $routes);

    $userController = null;
    foreach ($controllers as $c) {
        if (str_contains($c->fqcn, 'UserController')) {
            $userController = $c;
            break;
        }
    }

    expect($userController)
        ->toBeInstanceOf(ControllerDefinition::class);

    $middlewareNames = array_map(fn ($m) => $m->middleware, $userController->middlewares);

    expect($middlewareNames)
        ->toBe([
            'auth',
            'log',
            'subscribed',
        ]);

    // 'log' applies only to index + show
    $logMw = collect($userController->middlewares)->first(fn ($m) => $m->middleware === 'log');

    expect($logMw)
        ->only->toBe(['index', 'show'])
        ->except->toBeNull();

    // 'subscribed' applies to all except index
    $subscribedMw = collect($userController->middlewares)->first(fn ($m) => $m->middleware === 'subscribed');

    expect($subscribedMw)
        ->only->toBeNull()
        ->except->toBe(['index']);
});

it('extracts $this->middleware() calls from constructor', function () {
    $routes = (new RouteAnalyzer)->analyze(fixture('laravel-project'));
    $controllers = (new ControllerAnalyzer)->analyze(fixture('laravel-project'), $routes);

    $profileController = null;
    foreach ($controllers as $c) {
        if (str_contains($c->fqcn, 'ProfileController')) {
            $profileController = $c;
            break;
        }
    }

    expect($profileController)->toBeInstanceOf(ControllerDefinition::class);

    $middlewareNames = array_map(fn ($m) => $m->middleware, $profileController->middlewares);

    expect($middlewareNames)->toBe(['auth', 'verified', 'log']);

    // 'verified' only for store
    $verifiedMw = collect($profileController->middlewares)->first(fn ($m) => $m->middleware === 'verified');

    expect($verifiedMw)
        ->toBeInstanceOf(ControllerMiddleware::class)
        ->only->toBe(['store']);

    // 'log' except destroy (fluent chain)
    $logMw = collect($profileController->middlewares)->first(fn ($m) => $m->middleware === 'log');

    expect($logMw)
        ->toBeInstanceOf(ControllerMiddleware::class)
        ->except->toBe(['destroy']);
});

it('resolves same-namespace extends to FQCN and merges inherited actions', function () {
    $routes = (new RouteAnalyzer)->analyze(fixture('laravel-project'));
    $controllers = (new ControllerAnalyzer)->analyze(fixture('laravel-project'), $routes);

    $v3 = $controllers['App\\Http\\Controllers\\V3\\ThingV3Controller'] ?? null;

    expect($v3)->toBeInstanceOf(ControllerDefinition::class)
        ->parent->toBe(AbstractThingController::class)
        ->ancestorFqcns->toBe(['App\\Http\\Controllers\\V3\\AbstractThingController'])
        ->constructorDeps->toBe([
            'fixtureHelper' => "App\Services\V3\FixtureV3Helper",
            'things' => "App\Contracts\ThingRepositoryInterface",
        ]);

    $names = array_map(fn ($m) => $m->name, $v3->methods);

    expect($names)->toBe([
        'index',
        'warmPanelCache',
        'label',
    ]);
});

it('resolves $this inside inherited methods against the declaring class', function () {
    $routes = (new RouteAnalyzer)->analyze(fixture('laravel-project'));
    $analyzer = new ControllerAnalyzer;
    $controllers = $analyzer->analyze(fixture('laravel-project'), $routes);
    $edges = (new MethodTracer)->trace($controllers, $analyzer->getPsr4Map(), fixture('laravel-project'));

    $ping = array_filter(
        $edges,
        fn ($e) => $e->callerMethod === 'index'
            && $e->callerFqcn === 'App\\Http\\Controllers\\V3\\ThingV3Controller'
            && $e->calleeFqcn === 'App\\Services\\V3\\FixtureV3Helper'
            && $e->calleeMethod === 'ping'
    );

    expect($ping)
        ->toBeArray()
        ->andArrayFirstElement()
        ->toBeInstanceOf(CallChainEdge::class)
        ->calleeFqcn->toBe('App\Services\V3\FixtureV3Helper');

    $wrong = array_filter($edges, fn ($e) => $e->calleeMethod === 'warmPanelCache'
        && $e->calleeFqcn === 'App\\Http\\Controllers\\V3\\ThingV3Controller');

    expect($wrong)->toBe([]);

    $right = array_filter($edges, fn ($e) => $e->calleeMethod === 'warmPanelCache'
        && $e->calleeFqcn === 'App\\Http\\Controllers\\V3\\AbstractThingController');

    expect($right)
        ->toBeArray()
        ->andArrayFirstElement()
        ->toBeInstanceOf(CallChainEdge::class)
        ->calleeFqcn->toBe('App\Http\Controllers\V3\AbstractThingController');
});

it('traces call chains for actions declared only on abstract parents', function () {
    $routes = (new RouteAnalyzer)->analyze(fixture('laravel-project'));
    $analyzer = new ControllerAnalyzer;
    $controllers = $analyzer->analyze(fixture('laravel-project'), $routes);
    $edges = (new MethodTracer)->trace($controllers, $analyzer->getPsr4Map(), fixture('laravel-project'));

    $fromInherited = array_filter(
        $edges,
        fn ($e) => $e->callerFqcn === 'App\\Http\\Controllers\\V3\\ThingV3Controller'
            && $e->callerMethod === 'index'
            && $e->type === 'view'
    );

    expect($fromInherited)
        ->toBeArray()
        ->andArrayFirstElement()
        ->toBeInstanceOf(CallChainEdge::class)
        ->calleeFqcn->toBe('blade::things.v3.index');
});

it('finds methods on controllers', function () {
    $routes = (new RouteAnalyzer)->analyze(fixture('laravel-project'));
    $controllers = (new ControllerAnalyzer)->analyze(fixture('laravel-project'), $routes);

    $orderController = null;
    foreach ($controllers as $c) {
        if (str_contains($c->fqcn, 'OrderController')) {
            $orderController = $c;
            break;
        }
    }
    expect($orderController)->toBeInstanceOf(ControllerDefinition::class);

    $methodNames = array_map(fn ($m) => $m->name, $orderController->methods);

    expect($methodNames)->toBe([
        'index',
        'store',
        'show',
        'destroy',
    ]);
});
