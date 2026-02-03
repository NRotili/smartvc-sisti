<?php

namespace App\Filament\Resources\Camaras\Pages;

use App\Filament\Resources\Camaras\CamaraResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewCamara extends ViewRecord
{
    protected static string $resource = CamaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

      public function getTitle(): string | Htmlable
    {
        return $this->record->nombre . ' - ' . $this->record->descripcion;

    }
}
