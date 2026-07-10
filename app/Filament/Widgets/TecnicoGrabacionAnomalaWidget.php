<?php

namespace App\Filament\Widgets;

use App\Models\Camara;
use App\Models\Servidores;
use App\Services\DigifortService;
use Filament\Widgets\Widget;

/**
 * Cámaras que Digifort reporta funcionando pero que no están escribiendo a
 * disco (o graban a 0 FPS): fallas silenciosas que el webhook no detecta.
 */
class TecnicoGrabacionAnomalaWidget extends Widget
{
    protected string $view = 'filament.widgets.tecnico-grabacion-anomala';

    protected function getViewData(): array
    {
        $digifort = app(DigifortService::class);
        $servidores = Servidores::where('descripcion', 'like', '%Monitoreo%')->get();

        $enMantenimiento = Camara::where('mantenimiento', 1)->pluck('nombre')
            ->map(fn ($nombre) => mb_strtolower($nombre))
            ->flip();

        $anomalas = [];
        $servidoresCaidos = [];

        foreach ($servidores as $servidor) {
            $camaras = $digifort->estadoCamaras($servidor);

            if ($camaras === null) {
                $servidoresCaidos[] = $servidor->nombre;

                continue;
            }

            foreach ($camaras as $camara) {
                $nombre = $camara['Name'] ?? null;

                if ($nombre === null || isset($enMantenimiento[mb_strtolower($nombre)])) {
                    continue;
                }

                $activa = (bool) ($camara['Active'] ?? false);
                $working = (bool) ($camara['Working'] ?? false);
                $grabando = (bool) ($camara['WrittingToDisk'] ?? false);
                $fps = (float) ($camara['RecordingFPS'] ?? 0);

                if ($activa && $working && (! $grabando || $fps <= 0)) {
                    $anomalas[] = [
                        'nombre' => $nombre,
                        'servidor' => $servidor->nombre,
                        'grabando' => $grabando,
                        'fps' => $fps,
                        'retencion' => $camara['RecordingHours'] ?? null,
                    ];
                }
            }
        }

        return [
            'anomalas' => $anomalas,
            'servidoresCaidos' => $servidoresCaidos,
        ];
    }
}
