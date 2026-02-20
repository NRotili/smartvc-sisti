<?php

namespace App\Livewire;

use App\Models\Camara;
use EduardoRibeiroDev\FilamentLeaflet\Support\Groups\MarkerCluster;
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
        $markers = Camara::whereNotNull('lat')
        ->whereNotNull('lng')
        ->get()
        ->map(function ($camara) {

            if (!$camara->lat || !$camara->lng) {
                return null;
            }

            if ($camara->tipo_id == 2) {
                $icon = $camara->status
                    ? asset('img/cctv.png')
                    : asset('img/cctvOut.png');
            } else {
                $icon = $camara->status
                    ? asset('img/dome.png')
                    : asset('img/domeOut.png');
            }

            return Marker::make($camara->lat, $camara->lng)
                ->title($camara->nombre)
                ->icon($icon, [32, 32])
                ->group($camara->tipo_id == 2 ? 'Fijas' : 'Domos');
        })
        ->filter()
        ->values()
        ->all();

    return [
        MarkerCluster::make($markers)
        // ->maxClusterRadius(80)
        // ->showCoverageOnHover()
        // ->spiderfyOnMaxZoom()
    ];
    }
}
