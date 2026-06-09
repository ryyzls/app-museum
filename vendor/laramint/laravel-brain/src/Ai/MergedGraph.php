<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Ai;

use LaraMint\LaravelBrain\Storage\GraphStore;

/**
 * Reconstructs a single full-graph structure from the per-tab
 * subgraphs written by a scan.
 *
 * The scan no longer persists a monolithic graph; the split subgraphs
 * are the source of truth. Each subgraph is a forward-only slice of
 * the lifecycle, so the same node/edge can appear in many of them — they
 * are de-duplicated by id here so counts and traversal stay correct.
 */
final class MergedGraph
{
    /**
     * @return array{meta: array<string, mixed>, nodes: list<array<string, mixed>>, edges: list<array<string, mixed>>}
     *
     * @throws \RuntimeException when no scan data is present
     */
    public static function load(GraphStore $store): array
    {
        if (! $store->hasManifest()) {
            throw new \RuntimeException('No scan data found — run php artisan brain:scan first');
        }

        /** @var array<string, array<string, mixed>> $nodes */
        $nodes = [];
        /** @var array<string, array<string, mixed>> $edges */
        $edges = [];
        /** @var array<string, mixed> $meta */
        $meta = [];

        foreach ($store->subgraphIds() as $tabId) {
            $json = $store->getSubgraph($tabId);
            if ($json === null) {
                continue;
            }

            $data = json_decode($json, true);
            if (! is_array($data)) {
                continue;
            }

            if ($meta === [] && isset($data['meta']) && is_array($data['meta'])) {
                $meta = $data['meta'];
            }

            foreach ($data['nodes'] ?? [] as $node) {
                $id = (string) ($node['id'] ?? '');
                if ($id !== '') {
                    $nodes[$id] = $node;
                }
            }

            foreach ($data['edges'] ?? [] as $edge) {
                $eid = (string) ($edge['id'] ?? '');
                if ($eid === '') {
                    $eid = ($edge['source'] ?? '').'|'.($edge['target'] ?? '').'|'.($edge['type'] ?? '');
                }
                $edges[$eid] = $edge;
            }
        }

        if ($nodes === []) {
            throw new \RuntimeException('No scan data found — run php artisan brain:scan first');
        }

        // Per-subgraph counts are meaningless once merged; recompute.
        unset($meta['nodeCount'], $meta['edgeCount']);
        $meta['nodeCount'] = count($nodes);
        $meta['edgeCount'] = count($edges);

        return [
            'meta' => $meta,
            'nodes' => array_values($nodes),
            'edges' => array_values($edges),
        ];
    }
}
