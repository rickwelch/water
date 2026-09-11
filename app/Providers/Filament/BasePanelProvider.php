<?php

namespace App\Providers\Filament;

use Filament\PanelProvider;
use Filament\Panel;

abstract class BasePanelProvider extends PanelProvider
{

    public function basePanel(Panel $panel): Panel
    {
        return $panel
            ->login()
            ->registration()
            ->passwordReset()
            ->profile()
            ->brandName('Your Water System')
            ->viteTheme('resources/css/filament/theme.css')
            ;
    }
}
