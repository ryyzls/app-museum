<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Analysis;

/**
 * Maps facade FQCNs to their FacadeRecord.
 * Later registrations overwrite earlier ones for the same facade.
 */
final class FacadeRegistry
{
    /** @var array<string, FacadeRecord> */
    private array $facades = [];

    public function add(FacadeRecord $record): void
    {
        if ($record->facadeFqcn === '') {
            return;
        }
        $this->facades[$record->facadeFqcn] = $record;
    }

    public function get(string $facadeFqcn): ?FacadeRecord
    {
        return $this->facades[$facadeFqcn] ?? null;
    }

    /**
     * @return array<string, FacadeRecord>
     */
    public function all(): array
    {
        return $this->facades;
    }

    /**
     * Cross-reference string-key accessors against the container binding registry
     * to fill in concreteFqcn where only a container key was found.
     */
    public function resolveWith(ContainerBindingRegistry $bindings): void
    {
        foreach ($this->facades as $facadeFqcn => $record) {
            if ($record->concreteFqcn !== null) {
                continue;
            }
            $binding = $bindings->get($record->accessor);
            if ($binding !== null && $binding->concreteFqcn !== null) {
                $this->facades[$facadeFqcn] = new FacadeRecord(
                    $record->facadeFqcn,
                    $record->accessor,
                    $binding->concreteFqcn,
                );
            }
        }
    }
}
