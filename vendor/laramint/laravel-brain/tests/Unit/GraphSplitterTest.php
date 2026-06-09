<?php

use LaraMint\LaravelBrain\Analysis\RouteAnalyzer;
use LaraMint\LaravelBrain\Analysis\RouteDefinition;
use LaraMint\LaravelBrain\Graph\Graph;
use LaraMint\LaravelBrain\Graph\GraphSplitter;
use LaraMint\LaravelBrain\Graph\Node;

// RouteDefinition is declared alongside RouteAnalyzer in RouteAnalyzer.php;
// reference RouteAnalyzer so PSR-4 autoloading pulls in that file.
class_exists(RouteAnalyzer::class);

function splitterRouteNode(string $method, string $uri, ?array $security = null): Node
{
    $data = ['method' => $method, 'uri' => $uri];
    if ($security !== null) {
        $data['security'] = $security;
    }

    return new Node("route::{$method}::{$uri}", 'route', "{$method} {$uri}", $data);
}

function splitterRoute(string $method, string $uri, string $file): RouteDefinition
{
    return new RouteDefinition(
        method: $method,
        uri: $uri,
        controller: '',
        action: '',
        middlewares: [],
        name: '',
        file: $file,
        line: 1,
        tabGroup: "{$method} {$uri}",
    );
}

it('aggregates n+1, fat method and fat class issues from the lifecycle subgraph', function () {
    $graph = new Graph;
    $graph->addNode(splitterRouteNode('GET', '/reports', null));
    // Lifecycle node seeded via action::{controller}::{action}; carries structural issues.
    $graph->addNode(new Node(
        'action::App\\Http\\Controllers\\ReportController::index',
        'action',
        'ReportController@index',
        ['hasN1' => true, 'fatMethod' => true, 'fatClass' => true],
    ));

    $route = new RouteDefinition(
        method: 'GET',
        uri: '/reports',
        controller: 'App\\Http\\Controllers\\ReportController',
        action: 'index',
        middlewares: [],
        name: '',
        file: '/app/routes/api.php',
        line: 1,
        tabGroup: 'GET /reports',
    );

    $splitter = new GraphSplitter;
    $split = $splitter->split($graph, [$route], [], [], [], 'proj', '2026-05-16T00:00:00Z');
    $entry = $split['manifest'][0];

    expect($entry->issueCount)->toBe(3)
        ->and($entry->securityCount)->toBe(0)
        ->and($entry->n1Count)->toBe(1)
        ->and($entry->fatMethodCount)->toBe(1)
        ->and($entry->fatClassCount)->toBe(1)
        ->and($entry->riskLevel)->toBe('none');

    $json = $splitter->buildManifestJson($split['manifest'], $graph, 'proj', '2026-05-16T00:00:00Z', 1);
    $tab = json_decode($json, true)['tabs'][0];
    expect($tab['issueCount'])->toBe(3)
        ->and($tab['n1Count'])->toBe(1)
        ->and($tab['fatMethodCount'])->toBe(1)
        ->and($tab['fatClassCount'])->toBe(1);
});

it('aggregates route security issues into the manifest entry', function () {
    $graph = new Graph;
    $graph->addNode(splitterRouteNode('GET', '/password/forgot', [
        'exposure' => 'public',
        'riskLevel' => 'high',
        'issues' => [
            ['type' => 'MISSING_THROTTLE', 'severity' => 'high', 'message' => 'x', 'file' => null, 'line' => null],
        ],
    ]));
    $graph->addNode(splitterRouteNode('GET', '/safe', null));

    $routes = [
        splitterRoute('GET', '/password/forgot', '/app/routes/api.php'),
        splitterRoute('GET', '/safe', '/app/routes/api.php'),
    ];

    $result = (new GraphSplitter)->split($graph, $routes, [], [], [], 'proj', '2026-05-16T00:00:00Z');

    $byLabel = [];
    foreach ($result['manifest'] as $entry) {
        $byLabel[$entry->label] = $entry;
    }

    expect($byLabel['GET /password/forgot']->issueCount)->toBe(1)
        ->and($byLabel['GET /password/forgot']->riskLevel)->toBe('high')
        ->and($byLabel['GET /safe']->issueCount)->toBe(0)
        ->and($byLabel['GET /safe']->riskLevel)->toBe('none');
});

it('emits issueCount and riskLevel in the manifest JSON only when there are issues', function () {
    $graph = new Graph;
    $graph->addNode(splitterRouteNode('POST', '/login', [
        'exposure' => 'public',
        'riskLevel' => 'medium',
        'issues' => [
            ['type' => 'PUBLIC_WRITE', 'severity' => 'medium', 'message' => 'x', 'file' => null, 'line' => null],
            ['type' => 'MISSING_THROTTLE', 'severity' => 'medium', 'message' => 'y', 'file' => null, 'line' => null],
        ],
    ]));
    $graph->addNode(splitterRouteNode('GET', '/ping', null));

    $routes = [
        splitterRoute('POST', '/login', '/app/routes/api.php'),
        splitterRoute('GET', '/ping', '/app/routes/api.php'),
    ];

    $splitter = new GraphSplitter;
    $split = $splitter->split($graph, $routes, [], [], [], 'proj', '2026-05-16T00:00:00Z');
    $json = $splitter->buildManifestJson($split['manifest'], $graph, 'proj', '2026-05-16T00:00:00Z', count($routes));

    $decoded = json_decode($json, true);
    $tabs = [];
    foreach ($decoded['tabs'] as $tab) {
        $tabs[$tab['label']] = $tab;
    }

    expect($tabs['POST /login']['issueCount'])->toBe(2)
        ->and($tabs['POST /login']['riskLevel'])->toBe('medium')
        ->and($tabs['GET /ping'])->not->toHaveKey('issueCount')
        ->and($tabs['GET /ping'])->not->toHaveKey('riskLevel');
});
