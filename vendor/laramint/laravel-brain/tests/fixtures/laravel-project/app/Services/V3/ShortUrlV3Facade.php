<?php

namespace App\Services\V3;

final class ShortUrlV3Facade extends AbstractVersionedShortUrlFacade
{
    public function versionSlug(): string
    {
        return 'v3';
    }
}
