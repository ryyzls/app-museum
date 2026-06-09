<?php

namespace App\Services\V3;

class ShortUrlV3Service
{
    public function recentForPanel(array $ids): array
    {
        return [];
    }

    public function createManagedLink(string $destination, ?string $customCode = null): object
    {
        return new \stdClass;
    }

    public function inspect(string $code): object
    {
        return new \stdClass;
    }
}
