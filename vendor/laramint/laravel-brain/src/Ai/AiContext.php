<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Ai;

final class AiContext
{
    /**
     * @param  array<int, array<string, mixed>>  $nodes  BFS-ordered selected nodes (raw arrays from graph JSON)
     * @param  array<int, array<string, mixed>>  $edges  Edges between selected nodes
     */
    public function __construct(
        public readonly string $project,
        public readonly string $analyzedAt,
        public readonly string $focalLabel,
        public readonly int $tokenBudget,
        public readonly string $format,
        public readonly array $nodes,
        public readonly array $edges,
    ) {}
}
