<?php

namespace App\Filament\Widgets;

use App\Models\Servidores;
use App\Services\DigifortService;
use Filament\Widgets\Widget;

class TecnicoServidoresWidget extends Widget
{
    protected string $view = 'filament.widgets.tecnico-servidores';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $digifort = app(DigifortService::class);
        $servidores = Servidores::where('descripcion', 'like', '%Monitoreo%')->get();

        $data = $servidores->map(function (Servidores $servidor) use ($digifort) {
            $info = $digifort->infoServidor($servidor);
            $licencias = $digifort->licencias($servidor);
            $camaras = $digifort->estadoCamaras($servidor);

            $total = $caidas = $sinGrabar = 0;
            if ($camaras !== null) {
                foreach ($camaras as $camara) {
                    if (! ($camara['Active'] ?? true)) {
                        continue;
                    }
                    $total++;
                    if (! ($camara['Working'] ?? false)) {
                        $caidas++;
                    } elseif (! ($camara['WrittingToDisk'] ?? true)) {
                        $sinGrabar++;
                    }
                }
            }

            return [
                'nombre' => $servidor->nombre,
                'ip' => $servidor->ip,
                'online' => $info !== null,
                'uptime' => $info ? $this->formatearUptime($info['uptime']) : null,
                'version' => $info['version'] ?? null,
                'licencias' => $licencias,
                'camaras' => $camaras !== null
                    ? ['total' => $total, 'caidas' => $caidas, 'sin_grabar' => $sinGrabar]
                    : null,
            ];
        });

        return ['servidores' => $data];
    }

    private function formatearUptime(int $segundos): string
    {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$segundos");

        return $dtF->diff($dtT)->format('%a d, %h h, %i m');
    }
}
