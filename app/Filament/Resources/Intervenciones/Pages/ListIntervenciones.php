<?php

namespace App\Filament\Resources\Intervenciones\Pages;

use App\Filament\Resources\Intervenciones\IntervencioneResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIntervenciones extends ListRecords
{
    protected static string $resource = IntervencioneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Crear Intervención'),
        ];
    }
}
