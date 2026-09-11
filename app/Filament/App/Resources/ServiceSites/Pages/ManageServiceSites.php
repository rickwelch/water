<?php

namespace App\Filament\App\Resources\ServiceSites\Pages;

use App\Filament\App\Resources\ServiceSites\ServiceSiteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageServiceSites extends ManageRecords
{
    protected static string $resource = ServiceSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
