<?php

namespace App\Filament\Resources\Servidores\Pages;

use App\Filament\Resources\Servidores\ServidoresResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditServidores extends EditRecord
{
    protected static string $resource = ServidoresResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
