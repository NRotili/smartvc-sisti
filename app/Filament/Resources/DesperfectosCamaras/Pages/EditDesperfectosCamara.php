<?php

namespace App\Filament\Resources\DesperfectosCamaras\Pages;

use App\Filament\Resources\DesperfectosCamaras\DesperfectosCamaraResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDesperfectosCamara extends EditRecord
{
    protected static string $resource = DesperfectosCamaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
