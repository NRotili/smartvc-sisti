<?php

namespace App\Console\Commands;

use App\Models\Camara;
use App\Models\DesperfectosCamara;
use App\Models\FallasCamara;
use App\Models\Servidores;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SincronizarEstadoCamaras extends Command
{
    protected $signature   = 'camaras:sincronizar-estado';
    protected $description = 'Sincroniza el estado de las cámaras contra Digifort y corrige discrepancias';

    public function handle(): void
    {
        $servidores = Servidores::where('descripcion', 'like', '%Monitoreo%')
            ->whereNull('deleted_at')
            ->get();

        $tipoFalla = FallasCamara::where('tipo_falla', 'Sin clasificar')->first();

        foreach ($servidores as $servidor) {
            $this->procesarServidor($servidor, $tipoFalla);
        }
    }

    private function procesarServidor(Servidores $servidor, ?FallasCamara $tipoFalla): void
    {
        try {
            $response = Http::timeout(5)->get(
                "http://{$servidor->ip}:8601/Interface/Cameras/GetStatus",
                [
                    'ResponseFormat' => 'JSON',
                    'AuthUser'       => config('services.digifort.user', env('DIGIFORT_USER')),
                    'AuthPass'       => config('services.digifort.password', env('DIGIFORT_PASSWORD')),
                ]
            )->json();

            $camarasApi = $response['Response']['Data']['Cameras'] ?? [];

        } catch (\Throwable $e) {
            Log::error("SincronizarEstadoCamaras: no se pudo conectar al servidor {$servidor->nombre} ({$servidor->ip}): {$e->getMessage()}");
            return;
        }

        foreach ($camarasApi as $datos) {
            $nombre  = $datos['Name'] ?? null;

            if ($nombre === null || ! isset($datos['Working'])) {
                continue;
            }

            $working = (bool) $datos['Working'];

            $camara = Camara::where('nombre', $nombre)->first();

            if (! $camara) {
                continue;
            }

            if ($camara->mantenimiento) {
                continue;
            }

            $estadoDB = (bool) $camara->status;

            if ($working === $estadoDB) {
                continue;
            }

            // Discrepancia detectada
            if (! $working && $estadoDB) {
                $this->registrarCaida($camara, $tipoFalla);
            } elseif ($working && ! $estadoDB) {
                $this->registrarRecuperacion($camara);
            }
        }
    }

    private function registrarCaida(Camara $camara, ?FallasCamara $tipoFalla): void
    {
        $fallaExistente = DesperfectosCamara::where('camara_id', $camara->id)
            ->whereNull('hora_solucion')
            ->exists();

        if ($fallaExistente) {
            $camara->update(['status' => 0, 'grabando' => 0]);
            return;
        }

        $ahora = now()->subMinutes(30);

        DesperfectosCamara::create([
            'camara_id'          => $camara->id,
            'fecha_desperfecto'  => $ahora->toDateString(),
            'hora_desperfecto'   => $ahora->toTimeString(),
            'falla_camara_id'    => $tipoFalla?->id,
            'observaciones'      => 'Detectado automáticamente por sincronización periódica (webhook no recibido)',
        ]);

        $camara->update(['status' => 0, 'grabando' => 0]);

        Log::warning("SincronizarEstadoCamaras: cámara '{$camara->nombre}' caída sin webhook — falla creada");
    }

    private function registrarRecuperacion(Camara $camara): void
    {
        $falla = DesperfectosCamara::where('camara_id', $camara->id)
            ->whereNull('hora_solucion')
            ->first();

        if ($falla) {
            $falla->update([
                'fecha_solucion' => now()->toDateString(),
                'hora_solucion'  => now()->toTimeString(),
            ]);
        }

        $camara->update(['status' => 1, 'grabando' => 1]);

        Log::info("SincronizarEstadoCamaras: cámara '{$camara->nombre}' recuperada sin webhook — falla cerrada");
    }
}
