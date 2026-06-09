<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Settings;
use App\Filament\Resources\PostResource;
use App\Filament\Widgets\PostStatsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Panel;
use Filament\PanelProvider;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('/admin')
            ->resources([
                PostResource::class,
            ])
            ->pages([
                Settings::class,
            ])
            ->widgets([
                PostStatsWidget::class,
            ])
            ->middleware([
                Authenticate::class,
            ]);
    }
}
