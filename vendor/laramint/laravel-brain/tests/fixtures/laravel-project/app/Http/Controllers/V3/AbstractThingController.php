<?php

namespace App\Http\Controllers\V3;

use App\Contracts\ThingRepositoryInterface;
use App\Services\V3\FixtureV3Helper;
use Illuminate\Support\Facades\View;

abstract class AbstractThingController
{
    public function __construct(
        protected FixtureV3Helper $fixtureHelper,
        protected ThingRepositoryInterface $things,
    ) {}

    public function index()
    {
        $this->things->all();
        $this->fixtureHelper->ping();
        $this->warmPanelCache();

        return View::make('things.v3.index');
    }

    protected function warmPanelCache(): void
    {
        // helper invoked via $this from index()
    }
}
