<?php

namespace App\Repositories;

use App\Contracts\ThingRepositoryInterface;

final class SqlThingRepository implements ThingRepositoryInterface
{
    public function all(): array
    {
        return [];
    }
}
