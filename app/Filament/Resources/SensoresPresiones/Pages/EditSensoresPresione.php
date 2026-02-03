<?php

namespace App\Filament\Resources\SensoresPresiones\Pages;

use App\Filament\Resources\SensoresPresiones\SensoresPresioneResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSensoresPresione extends EditRecord
{
    protected static string $resource = SensoresPresioneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
