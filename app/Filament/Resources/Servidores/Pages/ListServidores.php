<?php

namespace App\Filament\Resources\Servidores\Pages;

use App\Filament\Resources\Servidores\ServidoresResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServidores extends ListRecords
{
    protected static string $resource = ServidoresResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
