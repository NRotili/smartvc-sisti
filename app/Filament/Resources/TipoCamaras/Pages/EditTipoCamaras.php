<?php

namespace App\Filament\Resources\TipoCamaras\Pages;

use App\Filament\Resources\TipoCamaras\TipoCamarasResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTipoCamaras extends EditRecord
{
    protected static string $resource = TipoCamarasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
