<?php

use LaraMint\LaravelBrain\Ai\MergedGraph;
use LaraMint\LaravelBrain\Storage\FileGraphStore;

function mergedGraphTmpDir(): string
{
    $dir = sys_get_temp_dir().'/lb-merged-'.uniqid('', true);
    mkdir($dir, 0777, true);

    return $dir;
}

function mergedGraphRmTree(string $dir): void
{
    foreach (scandir($dir) ?: [] as $entry) {
        if ($entry !== '.' && $entry !== '..') {
            @unlink($dir.'/'.$entry);
        }
    }
    @rmdir($dir);
}

it('merges split graph files, de-duplicating shared nodes and edges', function () {
    $dir = mergedGraphTmpDir();
    try {
        file_put_contents($dir.'/.graph-manifest.json', json_encode(['tabs' => []]));
        file_put_contents($dir.'/.graph-a.json', json_encode([
            'meta' => ['project' => 'demo', 'analyzedAt' => '2026-05-16', 'nodeCount' => 2, 'edgeCount' => 1],
            'nodes' => [
                ['id' => 'route::GET::/a', 'type' => 'route', 'label' => 'GET /a', 'data' => []],
                ['id' => 'action::C::a', 'type' => 'action', 'label' => 'C@a', 'data' => []],
            ],
            'edges' => [
                ['id' => 'e1', 'source' => 'route::GET::/a', 'target' => 'action::C::a', 'label' => '', 'type' => 'handles'],
            ],
        ]));
        file_put_contents($dir.'/.graph-b.json', json_encode([
            'meta' => ['project' => 'demo', 'analyzedAt' => '2026-05-16', 'nodeCount' => 2, 'edgeCount' => 1],
            'nodes' => [
                // Shared node also present in graph-a — must de-dupe.
                ['id' => 'action::C::a', 'type' => 'action', 'label' => 'C@a', 'data' => []],
                ['id' => 'route::GET::/b', 'type' => 'route', 'label' => 'GET /b', 'data' => []],
            ],
            'edges' => [
                ['id' => 'e1', 'source' => 'route::GET::/a', 'target' => 'action::C::a', 'label' => '', 'type' => 'handles'],
                ['id' => 'e2', 'source' => 'route::GET::/b', 'target' => 'action::C::a', 'label' => '', 'type' => 'handles'],
            ],
        ]));

        $graph = MergedGraph::load(new FileGraphStore($dir));

        expect($graph['nodes'])->toHaveCount(3)
            ->and($graph['edges'])->toHaveCount(2)
            ->and($graph['meta']['nodeCount'])->toBe(3)
            ->and($graph['meta']['edgeCount'])->toBe(2)
            ->and($graph['meta']['project'])->toBe('demo');

        $ids = array_map(fn ($n) => $n['id'], $graph['nodes']);
        sort($ids);
        expect($ids)->toBe(['action::C::a', 'route::GET::/a', 'route::GET::/b']);
    } finally {
        mergedGraphRmTree($dir);
    }
});

it('ignores the manifest and any stale .graph-all.json', function () {
    $dir = mergedGraphTmpDir();
    try {
        file_put_contents($dir.'/.graph-manifest.json', json_encode(['tabs' => []]));
        file_put_contents($dir.'/.graph-all.json', json_encode([
            'nodes' => [['id' => 'stale::node', 'type' => 'route', 'label' => 'stale', 'data' => []]],
            'edges' => [],
        ]));
        file_put_contents($dir.'/.graph-x.json', json_encode([
            'meta' => [],
            'nodes' => [['id' => 'route::GET::/x', 'type' => 'route', 'label' => 'GET /x', 'data' => []]],
            'edges' => [],
        ]));

        $graph = MergedGraph::load(new FileGraphStore($dir));
        $ids = array_map(fn ($n) => $n['id'], $graph['nodes']);

        expect($ids)->toBe(['route::GET::/x'])
            ->and($ids)->not->toContain('stale::node');
    } finally {
        mergedGraphRmTree($dir);
    }
});

it('throws when no scan data is present', function () {
    $dir = mergedGraphTmpDir();
    try {
        MergedGraph::load(new FileGraphStore($dir));
    } finally {
        mergedGraphRmTree($dir);
    }
})->throws(RuntimeException::class, 'No scan data found');
