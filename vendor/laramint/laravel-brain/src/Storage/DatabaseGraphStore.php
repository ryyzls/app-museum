<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Storage;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Stores scan output in a database table (one row per tab, plus one
 * row for the manifest). Useful when storage/ is not writable or
 * persisted (read-only containers, multi-node deployments).
 */
final class DatabaseGraphStore implements GraphStore
{
    private const MANIFEST_KEY = '__manifest__';

    public function __construct(
        private readonly string $table = 'laravel_brain_graphs',
        private readonly ?string $connection = null,
    ) {}

    public function ensureSchema(): void
    {
        $schema = Schema::connection($this->connection);

        if ($schema->hasTable($this->table)) {
            return;
        }

        $schema->create($this->table, function (Blueprint $table): void {
            $table->id();
            $table->string('tab')->unique();
            $table->longText('payload');
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function hasManifest(): bool
    {
        return $this->ready()
            && $this->query()->where('tab', self::MANIFEST_KEY)->exists();
    }

    public function getManifest(): ?string
    {
        return $this->read(self::MANIFEST_KEY);
    }

    public function putManifest(string $json): void
    {
        $this->put(self::MANIFEST_KEY, $json);
    }

    public function getSubgraph(string $tabId): ?string
    {
        return $this->read($tabId);
    }

    public function putSubgraph(string $tabId, string $json): void
    {
        $this->put($tabId, $json);
    }

    public function subgraphIds(): array
    {
        if (! $this->ready()) {
            return [];
        }

        $ids = $this->query()
            ->where('tab', '!=', self::MANIFEST_KEY)
            ->pluck('tab')
            ->all();

        return array_map('strval', $ids);
    }

    private function read(string $tab): ?string
    {
        if (! $this->ready()) {
            return null;
        }

        $value = $this->query()->where('tab', $tab)->value('payload');

        return $value !== null ? (string) $value : null;
    }

    private function ready(): bool
    {
        return Schema::connection($this->connection)->hasTable($this->table);
    }

    private function put(string $tab, string $json): void
    {
        $this->query()->updateOrInsert(
            ['tab' => $tab],
            ['payload' => $json, 'updated_at' => now()],
        );
    }

    private function query(): Builder
    {
        return DB::connection($this->connection)->table($this->table);
    }
}
