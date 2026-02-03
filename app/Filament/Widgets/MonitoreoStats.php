<?php

namespace App\Filament\Widgets;

use App\Models\Camara;
use App\Models\Servidores;
use Barryvdh\Debugbar\Facades\Debugbar;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Http;
use Symfony\Component\ErrorHandler\Debug;

class MonitoreoStats extends StatsOverviewWidget
{
    use HasWidgetShield;
    protected ?string $pollingInterval = null;
    protected static ?int $sort = 1;
    protected ?string $heading = 'KPIs Monitoreo';

    protected function getStats(): array
    {
        $totalCamaras = Camara::count();
        
        $fueraDeServicioCount = Camara::where('status', 0)->count();
        $fueraDeServicio = Stat::make('Fuera de servicio', $fueraDeServicioCount)
        ->description($fueraDeServicioCount > 0 ? 'ATENCIÓN!' : null)
        ->color($fueraDeServicioCount > 0 ? 'danger' : null);
        $desactivadasCount = Camara::where('activa', 0)->count();
        $desactivadas = Stat::make('Cámaras desactivadas', $desactivadasCount)
        ->description($desactivadasCount > 0 ? 'ATENCIÓN!' : null)
        ->color($desactivadasCount > 0 ? 'danger' : null);
        
        $sinGrabarCount = Camara::where('grabando', 0)->count();
        $sinGrabar = Stat::make('Cámaras sin grabar', $sinGrabarCount)
        ->description($sinGrabarCount > 0 ? 'ATENCIÓN!' : null)
        ->color($sinGrabarCount > 0 ? 'danger' : null);

        $mantenimientoCount = Camara::where('mantenimiento', 1)->count();
        $mantenimiento = Stat::make('Cámaras en mantenimiento', $mantenimientoCount)
        ->description($mantenimientoCount > 0 ? 'ATENCIÓN!' : null)
        ->color($mantenimientoCount > 0 ? 'danger' : null);

        $servidoresMonitoreo = Servidores::where('descripcion', 'Monitoreo')
            ->whereNull('deleted_at')->get();

        $total = 0;
        foreach ($servidoresMonitoreo as $servidor) {
            try {
                $responses = Http::timeout(3)->get("http://$servidor->ip:8601/Interface/Server/GetLicenses?ResponseFormat=JSON&AuthUser=" . env('DIGIFORT_USER') . "&AuthPass=" . env('DIGIFORT_PASSWORD'))->json();
                Debugbar::info("Licencias servidor $servidor->nombre: " . json_encode($responses));
                foreach ($responses['Response']['Data']['Summary'] as $License) {
                    $total += $License['TotalObjects'] - $License['UsedObjects'];
                }
                
            } catch (\Throwable $th) {
                //throw $th;
                Debugbar::error("Error al obtener las licencias del servidor $servidor->nombre: " . $th->getMessage());
                $total = "No hay datos";
            }
        }

        return [
            Stat::make('Cámaras totales', $totalCamaras),
            $fueraDeServicio,
            $desactivadas,
            $sinGrabar,
            $mantenimiento,
            Stat::make('Licencias disponibles', $total ?? 'No hay datos'),
 
        ];
    }
}
