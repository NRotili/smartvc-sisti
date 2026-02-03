<?php

namespace App\Filament\Resources\SensoresPresiones\Pages;

use App\Filament\Resources\SensoresPresiones\SensoresPresioneResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSensoresPresione extends ViewRecord
{
    protected static string $resource = SensoresPresioneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
