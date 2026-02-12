<?php

namespace App\Filament\Resources\Intervenciones\Pages;

use App\Filament\Resources\Intervenciones\IntervencioneResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIntervencione extends CreateRecord
{
    protected static string $resource = IntervencioneResource::class;
    protected static bool $canCreateAnother = false;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
