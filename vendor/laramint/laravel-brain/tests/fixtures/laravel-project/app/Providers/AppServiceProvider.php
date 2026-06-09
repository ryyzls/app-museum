<?php

namespace App\Providers;

use App\Contracts\ThingRepositoryInterface;
use App\Repositories\SqlThingRepository;

class AppServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThingRepositoryInterface::class, SqlThingRepository::class);
    }
}
