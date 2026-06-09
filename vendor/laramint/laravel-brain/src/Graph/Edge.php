<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Graph;

class Edge implements \JsonSerializable
{
    public function __construct(
        public string $id,
        public string $source,
        public string $target,
        public string $label,
        public string $type,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'source' => $this->source,
            'target' => $this->target,
            'label' => $this->label,
            'type' => $this->type,
        ];
    }
}
