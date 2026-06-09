<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Analysis;

/**
 * Maps container abstract types (interfaces, etc.) to their registration metadata.
 * Later registrations overwrite earlier ones for the same abstract.
 */
final class ContainerBindingRegistry
{
    /** @var array<string, ContainerBindingRecord> */
    private array $bindings = [];

    public function add(ContainerBindingRecord $record): void
    {
        if ($record->abstractFqcn === '') {
            return;
        }
        $this->bindings[$record->abstractFqcn] = $record;
    }

    public function get(string $abstractFqcn): ?ContainerBindingRecord
    {
        return $this->bindings[$abstractFqcn] ?? null;
    }

    /**
     * @return array<string, ContainerBindingRecord>
     */
    public function all(): array
    {
        return $this->bindings;
    }
}
