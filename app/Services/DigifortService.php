<?php

namespace App\Services;

use App\Models\Servidores;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente HTTP para la API de Digifort. Las respuestas se cachean 60 segundos
 * para que los widgets del panel no golpeen los NVR en cada render.
 */
class DigifortService
{
    private const CACHE_TTL = 60;

    /**
     * @return array{uptime: int, version: ?string}|null
     */
    public function infoServidor(Servidores $servidor): ?array
    {
        return Cache::remember("digifort.info.{$servidor->id}", self::CACHE_TTL, function () use ($servidor) {
            $data = $this->get($servidor, 'Server/GetInfo');

            if (! isset($data['Info'])) {
                return null;
            }

            return [
                'uptime' => (int) ($data['Info']['UpTime'] ?? 0),
                'version' => $data['Info']['Version'] ?? null,
            ];
        });
    }

    /**
     * @return array{total: int, usadas: int}|null
     */
    public function licencias(Servidores $servidor): ?array
    {
        return Cache::remember("digifort.licencias.{$servidor->id}", self::CACHE_TTL, function () use ($servidor) {
            $data = $this->get($servidor, 'Server/GetLicenses');

            if (! isset($data['Summary'])) {
                return null;
            }

            $total = 0;
            $usadas = 0;
            foreach ($data['Summary'] as $licencia) {
                $total += $licencia['TotalObjects'] ?? 0;
                $usadas += $licencia['UsedObjects'] ?? 0;
            }

            return ['total' => $total, 'usadas' => $usadas];
        });
    }

    /**
     * Estado de todas las cámaras del servidor. Cada elemento trae
     * Name, Working (bool), Active (bool), WrittingToDisk (bool),
     * RecordingFPS y RecordingHours.
     */
    public function estadoCamaras(Servidores $servidor): ?array
    {
        return Cache::remember("digifort.camaras.{$servidor->id}", self::CACHE_TTL, function () use ($servidor) {
            $data = $this->get($servidor, 'Cameras/GetStatus');

            return $data['Cameras'] ?? null;
        });
    }

    private function get(Servidores $servidor, string $endpoint): ?array
    {
        try {
            $response = Http::timeout(5)->get(
                "http://{$servidor->ip}:8601/Interface/{$endpoint}",
                [
                    'ResponseFormat' => 'JSON',
                    'AuthUser' => config('services.digifort.user', env('DIGIFORT_USER')),
                    'AuthPass' => config('services.digifort.password', env('DIGIFORT_PASSWORD')),
                ]
            )->json();

            return $response['Response']['Data'] ?? null;
        } catch (\Throwable $e) {
            Log::warning("DigifortService: fallo {$endpoint} en {$servidor->nombre} ({$servidor->ip}): {$e->getMessage()}");

            return null;
        }
    }
}
