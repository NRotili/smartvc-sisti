<?php

namespace App\Filament\Resources\Intervenciones\Pages;

use App\Filament\Resources\Intervenciones\IntervencioneResource;
use App\Models\Intervencione;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateIntervencione extends CreateRecord
{
    protected static string $resource = IntervencioneResource::class;

    protected static bool $canCreateAnother = false;

    // id de la intervención raíz cuando se está creando una ampliación
    public ?int $padreId = null;

    public function mount(): void
    {
        parent::mount();

        $padre = Intervencione::find(request()->integer('padre'));

        if ($padre) {
            // las ampliaciones siempre cuelgan de la intervención raíz
            $this->padreId = $padre->intervencion_padre_id ?? $padre->id;
        }
    }

    public function getTitle(): string|Htmlable
    {
        return $this->padreId
            ? "Ampliar Intervención #{$this->padreId}"
            : parent::getTitle();
    }

    public function getSubheading(): string|Htmlable|null
    {
        return $this->padreId
            ? "La ampliación quedará registrada en la línea histórica de la intervención #{$this->padreId}."
            : null;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        if ($this->padreId) {
            $data['intervencion_padre_id'] = $this->padreId;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        if ($this->padreId) {
            return $this->getResource()::getUrl('view', ['record' => $this->padreId]);
        }

        return $this->getResource()::getUrl('index');
    }
}
