<?php

namespace App\Filament\Widgets;

use App\Models\DesperfectosCamara;
use App\Models\Intervencione;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ResumenMensualWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;
    protected static bool $isLazy = true;
    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['Operador de Monitoreo', 'Supervisor de Monitoreo']) ?? false;
    }

    protected function getHeading(): ?string
    {
        $mes = ucfirst(now()->translatedFormat('F'));
        return "Tu mes — {$mes} " . now()->year;
    }

    protected function getStats(): array
    {
        $user  = auth()->user();
        $inicio = now()->startOfMonth();
        $fin    = now();

        // Intervenciones creadas por el usuario este mes
        $creadas = Intervencione::where('user_id', $user->id)
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->count();

        // Intervenciones en las que estuvo presente (sin contar las que creó)
        $creadasIds = Intervencione::where('user_id', $user->id)
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->pluck('id');

        $participadas = $user->intervencionesMonitoreo()
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->whereNotIn('intervenciones.id', $creadasIds)
            ->count();

        // Total del equipo este mes (para calcular participación)
        $totalEquipo = Intervencione::whereBetween('fecha_hora', [$inicio, $fin])->count();

        $totalPropio  = $creadas + $participadas;
        $participacion = $totalEquipo > 0
            ? round(($totalPropio / $totalEquipo) * 100) . '%'
            : '—';

        $stats = [
            Stat::make('Intervenciones creadas', $creadas)
                ->description('Este mes')
                ->color('warning')
                ->icon('heroicon-o-pencil-square'),

            Stat::make('Intervenciones en las que participé', $participadas)
                ->description('Como operador presente')
                ->color('info')
                ->icon('heroicon-o-user-group'),

            Stat::make('Participación en el equipo', $participacion)
                ->description("{$totalPropio} de {$totalEquipo} intervenciones del mes")
                ->color('success')
                ->icon('heroicon-o-chart-pie'),
        ];

        // Para supervisores: agrego fallas gestionadas por el equipo
        if ($user->hasRole('Supervisor de Monitoreo')) {
            $fallasAbiertas = DesperfectosCamara::whereNull('hora_solucion')
                ->count();

            $fallasResueltasMes = DesperfectosCamara::whereNotNull('hora_solucion')
                ->whereBetween('fecha_solucion', [$inicio->toDateString(), $fin->toDateString()])
                ->count();

            $stats[] = Stat::make('Fallas activas', $fallasAbiertas)
                ->description("{$fallasResueltasMes} resueltas este mes")
                ->color($fallasAbiertas > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-exclamation-triangle');
        }

        return $stats;
    }
}
