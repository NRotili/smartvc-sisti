<?php

namespace App\Filament\Widgets;

use App\Models\Camara;
use App\Models\DesperfectosCamara;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TecnicoResumenStats extends StatsOverviewWidget
{
    protected static bool $isLazy = true;

    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Estado general';

    protected function getStats(): array
    {
        return [
            $this->statOperatividad(),
            $this->statFallasAbiertas(),
            $this->statMantenimiento(),
            $this->statTiempoReparacion(),
        ];
    }

    private function statOperatividad(): Stat
    {
        $activas = Camara::where('activa', 1)->count();
        $operativas = Camara::where('activa', 1)->where('status', 1)->count();
        $porcentaje = $activas > 0 ? round(($operativas / $activas) * 100) : 0;

        return Stat::make('Operatividad', "{$porcentaje}%")
            ->description("{$operativas} de {$activas} cámaras activas funcionando")
            ->color(match (true) {
                $porcentaje >= 95 => 'success',
                $porcentaje >= 85 => 'warning',
                default => 'danger',
            })
            ->icon('heroicon-o-video-camera');
    }

    private function statFallasAbiertas(): Stat
    {
        // La relación camara() usa withTrashed(): excluir fallas de cámaras eliminadas
        $abiertas = DesperfectosCamara::whereNull('hora_solucion')
            ->whereHas('camara', fn ($query) => $query->withoutTrashed())
            ->get();

        $descripcion = 'Sin fallas pendientes';
        if ($abiertas->isNotEmpty()) {
            $masAntigua = $abiertas
                ->map(fn ($falla) => $this->fechaHora($falla->fecha_desperfecto, $falla->hora_desperfecto))
                ->filter()
                ->min();

            if ($masAntigua) {
                $dias = (int) $masAntigua->diffInDays(now());
                $descripcion = "La más antigua lleva {$dias} día".($dias === 1 ? '' : 's').' abierta';
            }
        }

        return Stat::make('Fallas abiertas', $abiertas->count())
            ->description($descripcion)
            ->color($abiertas->isEmpty() ? 'success' : 'danger')
            ->icon('heroicon-o-exclamation-triangle');
    }

    private function statMantenimiento(): Stat
    {
        $mantenimiento = Camara::where('mantenimiento', 1)->count();

        return Stat::make('En mantenimiento', $mantenimiento)
            ->description('Excluidas del monitoreo automático')
            ->color($mantenimiento > 0 ? 'warning' : 'success')
            ->icon('heroicon-o-wrench-screwdriver');
    }

    private function statTiempoReparacion(): Stat
    {
        $resueltas = DesperfectosCamara::whereNotNull('hora_solucion')
            ->where('fecha_solucion', '>=', now()->subDays(30)->toDateString())
            ->get();

        $horas = $resueltas
            ->map(function ($falla) {
                $inicio = $this->fechaHora($falla->fecha_desperfecto, $falla->hora_desperfecto);
                $fin = $this->fechaHora($falla->fecha_solucion, $falla->hora_solucion);

                return ($inicio && $fin && $fin->greaterThan($inicio))
                    ? $inicio->diffInHours($fin)
                    : null;
            })
            ->filter(fn ($h) => $h !== null);

        if ($horas->isEmpty()) {
            return Stat::make('Tiempo medio de reparación', '—')
                ->description('Sin fallas resueltas en los últimos 30 días')
                ->icon('heroicon-o-clock');
        }

        $promedio = $horas->avg();
        $valor = $promedio < 48
            ? round($promedio, 1).' h'
            : round($promedio / 24, 1).' días';

        return Stat::make('Tiempo medio de reparación', $valor)
            ->description("Sobre {$horas->count()} fallas resueltas (últimos 30 días)")
            ->color($promedio <= 24 ? 'success' : ($promedio <= 72 ? 'warning' : 'danger'))
            ->icon('heroicon-o-clock');
    }

    private function fechaHora(?string $fecha, ?string $hora): ?Carbon
    {
        if (! $fecha) {
            return null;
        }

        try {
            return Carbon::parse(trim(substr($fecha, 0, 10).' '.($hora ?? '00:00:00')));
        } catch (\Throwable) {
            return null;
        }
    }
}
