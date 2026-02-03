<?php

namespace App\Filament\Resources\SensoresPresiones\Pages;

use App\Filament\Resources\SensoresPresiones\SensoresPresioneResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSensoresPresiones extends ListRecords
{
    protected static string $resource = SensoresPresioneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
