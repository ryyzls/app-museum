<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Storage;

/**
 * Persistence backend for scan output.
 *
 * A scan produces one manifest (the tab index) plus one subgraph JSON blob
 * per tab. Implementations decide where those blobs live — the filesystem
 * (default) or a database table.
 */
interface GraphStore
{
    /**
     * Prepare the backend so a scan can write to it (create the directory
     * or the database table when missing). A no-op when already set up.
     */
    public function ensureSchema(): void;

    public function hasManifest(): bool;

    public function getManifest(): ?string;

    public function putManifest(string $json): void;

    public function getSubgraph(string $tabId): ?string;

    public function putSubgraph(string $tabId, string $json): void;

    /**
     * Tab ids of every stored subgraph (manifest excluded).
     *
     * @return list<string>
     */
    public function subgraphIds(): array;
}
