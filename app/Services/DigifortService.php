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
     * Uso de recursos del servidor (GET /Interface/Server/GetUsage).
     *
     * @return array{procesador: int, memoria_global: int, memoria_servidor: int, conexiones: int, clientes: int, trafico_entrada: int, trafico_salida: int}|null
     */
    public function usoServidor(Servidores $servidor): ?array
    {
        return Cache::remember("digifort.uso.{$servidor->id}", self::CACHE_TTL, function () use ($servidor) {
            $data = $this->get($servidor, 'Server/GetUsage');

            if (! isset($data['Stats'])) {
                return null;
            }

            return [
                'procesador' => (int) ($data['Stats']['Processor'] ?? 0),
                'memoria_global' => (int) ($data['Stats']['GlobalMemory'] ?? 0),
                'memoria_servidor' => (int) ($data['Stats']['ServerMemory'] ?? 0),
                'conexiones' => (int) ($data['Stats']['Connections'] ?? 0),
                'clientes' => (int) ($data['Stats']['Clients'] ?? 0),
                'trafico_entrada' => (int) ($data['Stats']['InputTraffic'] ?? 0),
                'trafico_salida' => (int) ($data['Stats']['OutputTraffic'] ?? 0),
            ];
        });
    }

    /**
     * Conexiones de usuarios al servidor (GET /Interface/Users/GetConnections).
     * Cada elemento trae Username, IP, ConnectionTime (segundos) y ConnectionType.
     */
    public function conexionesUsuarios(Servidores $servidor): ?array
    {
        return Cache::remember("digifort.conexiones.{$servidor->id}", self::CACHE_TTL, function () use ($servidor) {
            $data = $this->get($servidor, 'Users/GetConnections');

            return $data['Connections'] ?? null;
        });
    }

    /**
     * Estado de todas las cámaras del servidor. Cada elemento trae
     * Name, Working (bool), Active (bool), WrittingToDisk (bool),
     * ConfiguredToRecord (bool), RecordingFPS, RecordingHours,
     * InactiveTime (segundos caída) y UsedDiskSpace (bytes).
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
