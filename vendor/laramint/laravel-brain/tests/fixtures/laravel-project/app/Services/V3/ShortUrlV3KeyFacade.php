<?php

namespace App\Services\V3;

use Illuminate\Support\Facades\Facade;

class ShortUrlV3KeyFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'short-url-v3';
    }
}
