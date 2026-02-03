<?php

namespace App\Filament\Resources\TipoCamaras\Pages;

use App\Filament\Resources\TipoCamaras\TipoCamarasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipoCamaras extends ListRecords
{
    protected static string $resource = TipoCamarasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
