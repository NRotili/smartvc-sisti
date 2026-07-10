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
            $uso = $digifort->usoServidor($servidor);
            $conexiones = $digifort->conexionesUsuarios($servidor);
            $camaras = $digifort->estadoCamaras($servidor);

            $total = $caidas = $sinGrabar = 0;
            $discoBytes = 0;
            if ($camaras !== null) {
                foreach ($camaras as $camara) {
                    $discoBytes += (int) ($camara['UsedDiskSpace'] ?? 0);
                    if (! ($camara['Active'] ?? true)) {
                        continue;
                    }
                    $total++;
                    if (! ($camara['Working'] ?? false)) {
                        $caidas++;
                    } elseif (($camara['ConfiguredToRecord'] ?? true) && ! ($camara['WrittingToDisk'] ?? true)) {
                        $sinGrabar++;
                    }
                }
            }

            // Usuarios humanos conectados (excluye conexiones internas de cámaras/servidores)
            $usuarios = collect($conexiones ?? [])
                ->whereIn('ConnectionType', ['SURVEILLANCE_CLIENT', 'ADMINISTRATION_CLIENT', 'WEB_LIVE_PLUGIN', 'WEB_PLAYBACK_PLUGIN'])
                ->pluck('Username')
                ->unique()
                ->values();

            return [
                'nombre' => $servidor->nombre,
                'ip' => $servidor->ip,
                'online' => $info !== null,
                'uptime' => $info ? $this->formatearUptime($info['uptime']) : null,
                'version' => $info['version'] ?? null,
                'licencias' => $licencias,
                'cpu' => $uso['procesador'] ?? null,
                'ram' => $uso ? round($uso['memoria_global'] / 1073741824, 1) : null,
                'clientes' => $uso['clientes'] ?? null,
                'trafico_entrada' => $uso ? round($uso['trafico_entrada'] / 1024, 1) : null,
                'trafico_salida' => $uso ? round($uso['trafico_salida'] / 1024, 1) : null,
                'disco_tb' => $camaras !== null ? round($discoBytes / 1099511627776, 2) : null,
                'usuarios' => $conexiones !== null ? $usuarios : null,
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
