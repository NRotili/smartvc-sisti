<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class IntervencioneChart extends ChartWidget
{
    use HasWidgetShield;
    protected static ?int $sort = 3;
    protected ?string $heading = 'Intervenciones';
    protected ?string $pollingInterval = null;

    protected function getFilters(): ?array
    {
        return [
            'hoy' => 'Diario',
            'mes' => 'Mensual',
        ];
    }


    protected function getData(): array
    {
        $activeFilter = $this->filter ?? 'hoy'; // Por defecto, filtrar por mes
        return $this->getInterventionsData($activeFilter);
    }

    protected function getInterventionsData($filter = 'mes')
    {
        $currentYear = Carbon::now()->year;
        $lastYear = $currentYear - 1;

        $intervencionesActual = [];
        $intervencionesAnterior = [];
        $labels = [];

        switch ($filter) {
            case 'hoy':
                // Obtener datos por día del mes actual y del año anterior
                $intervencionesActual = DB::table('intervenciones')
                    ->selectRaw('DAY(fecha_hora) as dia, COUNT(*) as total')
                    ->whereYear('fecha_hora', $currentYear)
                    ->whereMonth('fecha_hora', Carbon::now()->month)
                    ->groupBy('dia')
                    ->orderBy('dia')
                    ->pluck('total', 'dia')
                    ->toArray();

                $intervencionesAnterior = DB::table('intervenciones')
                    ->selectRaw('DAY(fecha_hora) as dia, COUNT(*) as total')
                    ->whereYear('fecha_hora', $lastYear)
                    ->whereMonth('fecha_hora', Carbon::now()->month)
                    ->groupBy('dia')
                    ->orderBy('dia')
                    ->pluck('total', 'dia')
                    ->toArray();

                $daysInMonth = Carbon::now()->daysInMonth;
                $labels = range(1, $daysInMonth); // Días del mes actual
                break;

            case 'mes':
                // Obtener datos por mes del año actual y anterior
                $intervencionesActual = DB::table('intervenciones')
                    ->selectRaw('MONTH(fecha_hora) as mes, COUNT(*) as total')
                    ->whereYear('fecha_hora', $currentYear)
                    ->groupBy('mes')
                    ->orderBy('mes')
                    ->pluck('total', 'mes')
                    ->toArray();

                $intervencionesAnterior = DB::table('intervenciones')
                    ->selectRaw('MONTH(fecha_hora) as mes, COUNT(*) as total')
                    ->whereYear('fecha_hora', $lastYear)
                    ->groupBy('mes')
                    ->orderBy('mes')
                    ->pluck('total', 'mes')
                    ->toArray();

                $labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']; // Meses del año
                break;
        }

        // Inicializar los datos en 0 según el filtro seleccionado
        $datosAnioActual = [];
        $datosAnioAnterior = [];

        foreach ($labels as $index => $label) {
            $key = $index + 1; // Para diario y mensual, el índice es 1-based
            $datosAnioActual[] = $intervencionesActual[$key] ?? 0;
            $datosAnioAnterior[] = $intervencionesAnterior[$key] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => (string) $currentYear,
                    'data' => $datosAnioActual,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
                [
                    'label' => (string) $lastYear,
                    'data' => $datosAnioAnterior,
                    'backgroundColor' => '#FF6384',
                    'borderColor' => '#FF9AA2',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
