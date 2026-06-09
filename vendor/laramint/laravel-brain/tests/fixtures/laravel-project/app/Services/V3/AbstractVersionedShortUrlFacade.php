<?php

namespace App\Services\V3;

use Illuminate\Support\Facades\Facade;

abstract class AbstractVersionedShortUrlFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ShortUrlV3Service::class;
    }

    public function recentForPanel(array $ids): array
    {
        return static::getFacadeRoot()->recentForPanel($ids);
    }

    public function createManagedLink(string $destination, ?string $customCode = null): object
    {
        return static::getFacadeRoot()->createManagedLink($destination, $customCode);
    }

    public function inspect(string $code): object
    {
        return static::getFacadeRoot()->inspect($code);
    }
}
