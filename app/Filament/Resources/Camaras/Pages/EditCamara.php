<?php

namespace App\Filament\Resources\Camaras\Pages;

use App\Filament\Resources\Camaras\CamaraResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCamara extends EditRecord
{
    protected static string $resource = CamaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
