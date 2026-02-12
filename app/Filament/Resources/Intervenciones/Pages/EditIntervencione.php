<?php

namespace App\Filament\Resources\Intervenciones\Pages;

use App\Filament\Resources\Intervenciones\IntervencioneResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditIntervencione extends EditRecord
{
    protected static string $resource = IntervencioneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(
            $this->record->canBeEditedBy(auth()->user()),
            403
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
