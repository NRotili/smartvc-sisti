<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class FallasChart extends ChartWidget
{
    use HasWidgetShield;
    protected ?string $heading = 'Fallas de cámaras';
    protected static ?int $sort = 4;
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
        return $this->getFallasData($activeFilter);
    }

    protected function getFallasData($filter = 'mes')
    {

        $anioActual = Carbon::now()->year;
        $anioPasado = $anioActual - 1;

        $fallasActual = [];
        $fallasAnterior = [];
        $labels = [];

        switch ($filter) {
            case 'hoy':
                // Obtener datos por día del mes actual y del año anterior
                $fallasActual = DB::table('desperfectos_camaras')
                    ->selectRaw('DAY(fecha_desperfecto) as dia, COUNT(*) as total')
                    ->whereYear('fecha_desperfecto', $anioActual)
                    ->whereMonth('fecha_desperfecto', Carbon::now()->month)
                    ->groupBy('dia')
                    ->orderBy('dia')
                    ->pluck('total', 'dia')
                    ->toArray();

                $fallasAnterior = DB::table('desperfectos_camaras')
                    ->selectRaw('DAY(fecha_desperfecto) as dia, COUNT(*) as total')
                    ->whereYear('fecha_desperfecto', $anioPasado)
                    ->whereMonth('fecha_desperfecto', Carbon::now()->month)
                    ->groupBy('dia')
                    ->orderBy('dia')
                    ->pluck('total', 'dia')
                    ->toArray();

                $diasEnMes = Carbon::now()->daysInMonth;
                $labels = range(1, $diasEnMes); // Días del mes actual
                break;

            case 'mes':
                // Obtener datos por mes del año actual y anterior
                $fallasActual = DB::table('desperfectos_camaras')
                    ->selectRaw('MONTH(fecha_desperfecto) as mes, COUNT(*) as total')
                    ->whereYear('fecha_desperfecto', $anioActual)
                    ->groupBy('mes')
                    ->orderBy('mes')
                    ->pluck('total', 'mes')
                    ->toArray();

                $fallasAnterior = DB::table('desperfectos_camaras')
                    ->selectRaw('MONTH(fecha_desperfecto) as mes, COUNT(*) as total')
                    ->whereYear('fecha_desperfecto', $anioPasado)
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
            $datosAnioActual[] = $fallasActual[$key] ?? 0;
            $datosAnioAnterior[] = $fallasAnterior[$key] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => (string) $anioActual,
                    'data' => $datosAnioActual,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
                [
                    'label' => (string) $anioPasado,
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
