<?php

declare(strict_types=1);

namespace LaraMint\LaravelBrain\Graph;

class Node implements \JsonSerializable
{
    public function __construct(
        public string $id,
        public string $type,
        public string $label,
        public array $data = [],
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'label' => $this->label,
            'data' => $this->data,
        ];
    }
}
