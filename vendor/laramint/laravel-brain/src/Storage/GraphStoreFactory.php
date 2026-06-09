<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Storage;

/**
 * Resolves the configured graph store. Driver is selected via
 * `laravel-brain.driver` (env LARAVEL_BRAIN_DRIVER): "file" | "database".
 */
final class GraphStoreFactory
{
    public static function make(): GraphStore
    {
        $driver = (string) config('laravel-brain.driver', 'file');

        return match ($driver) {
            'database' => new DatabaseGraphStore(
                (string) config('laravel-brain.database.table', 'laravel_brain_graphs'),
                config('laravel-brain.database.connection'),
            ),
            default => new FileGraphStore(storage_path('app/laravel-brain')),
        };
    }
}
