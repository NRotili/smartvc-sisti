<?php

namespace App\Filament\Widgets;

use App\Models\Servidores;
use Barryvdh\Debugbar\Facades\Debugbar;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Http;

class ServerStats extends StatsOverviewWidget
{
    use HasWidgetShield;
    protected static ?int $sort = 2;
    protected ?string $pollingInterval = null;
    protected ?string $heading = 'KPIs Servidores';
    protected function getStats(): array
    {

        $servidoresMonitoreo = Servidores::where('descripcion', 'Monitoreo')->get(); 
        return [
            Stat::make('Tiempo activo - Server 1', $this->timeServer($servidoresMonitoreo[0]) ?? 'No hay datos'),
            Stat::make('Tiempo activo - Server 2', $this->timeServer($servidoresMonitoreo[1]) ?? 'No hay datos'),
            Stat::make('Tiempo activo - Server 3', $this->timeServer($servidoresMonitoreo[2]) ?? 'No hay datos'),
        ];
    }

    
    protected function timeServer($server)
    {
        try {
            $response = Http::timeout(3)->get("http://$server->ip:8601/Interface/Server/GetInfo?ResponseFormat=JSON&AuthUser=" . env('DIGIFORT_USER') . "&AuthPass=" . env('DIGIFORT_PASSWORD'))->json();
            $time = $response['Response']['Data']['Info']['UpTime'];
            return $this->secondsToTime($time);
        } catch (\Throwable $th) {
            //throw $th;
            Debugbar::error("Error al obtener el tiempo activo del servidor $server->nombre: " . $th->getMessage());
            return "No hay datos";
        }
    }

    protected function secondsToTime($seconds)
    {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a d, %h h, %i m');
    }
}
