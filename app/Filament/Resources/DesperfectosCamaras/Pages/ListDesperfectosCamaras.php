<?php

namespace App\Filament\Resources\DesperfectosCamaras\Pages;

use App\Filament\Resources\DesperfectosCamaras\DesperfectosCamaraResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDesperfectosCamaras extends ListRecords
{
    protected static string $resource = DesperfectosCamaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
