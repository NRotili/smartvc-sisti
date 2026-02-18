<?php

namespace App\Livewire;

use App\Models\Camara;
use EduardoRibeiroDev\FilamentLeaflet\Widgets\MapWidget;
use EduardoRibeiroDev\FilamentLeaflet\Support\Markers\Marker;

class MapaCamaras extends MapWidget
{
    protected ?string $heading = "Mapa de Cámaras";

    protected array $mapCenter = [-33.233425, -60.324238];
    protected int $defaultZoom = 14;
    protected int|string|array $columnSpan = 'full';

    // protected int $mapHeight = 800;

    protected function getMarkers(): array
    {
        return Camara::whereNotNull('lat')
            ->whereNotNull('lng')
            ->get()
            ->map(function ($camara) {

                if (!$camara->lat || !$camara->lng) {
                    return null;
                }

                if ($camara->tipo_id == 2) {
                    if ($camara->status) {
                        $icon = asset('img/cctv.png');
                    } else {
                        $icon = asset('img/cctvOut.png');
                    }
                } else {
                    if ($camara->status) {
                        $icon = asset('img/dome.png');
                    } else {
                        $icon = asset('img/domeOut.png');
                    }
                }
                return Marker::make($camara->lat, $camara->lng)
                    ->title($camara->nombre)
                    ->icon($icon, [32, 32])
                    ->group($camara->tipo_id == 2 ? 'Fijas' : 'Domos');
            })
            ->filter()   // elimina nulls
            ->values()
            ->all();     // 👈 IMPORTANTE
    }
}
