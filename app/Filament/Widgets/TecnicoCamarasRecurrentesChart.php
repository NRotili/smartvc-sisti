<?php

namespace App\Filament\Widgets;

use App\Models\DesperfectosCamara;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TecnicoCamarasRecurrentesChart extends ApexChartWidget
{
    protected static ?string $chartId = 'tecnicoCamarasRecurrentesChart';

    protected static ?string $heading = 'Cámaras con más fallas (últimos 90 días)';

    protected static ?int $contentHeight = 380;

    protected static bool $isLazy = true;

    protected ?string $pollingInterval = null;

    protected function getOptions(): array
    {
        $recurrentes = DesperfectosCamara::query()
            ->where('fecha_desperfecto', '>=', now()->subDays(90)->toDateString())
            ->select('camara_id', DB::raw('count(*) as total'))
            ->groupBy('camara_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('camara')
            ->get();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 380,
                'toolbar' => ['show' => false],
                'fontFamily' => 'inherit',
            ],
            'series' => [
                ['name' => 'Fallas', 'data' => $recurrentes->pluck('total')->toArray()],
            ],
            'xaxis' => [
                'categories' => $recurrentes
                    ->map(fn ($fila) => $fila->camara?->nombre ?? "Cámara #{$fila->camara_id}")
                    ->toArray(),
                'labels' => ['style' => ['fontSize' => '12px']],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => true,
                    'borderRadius' => 4,
                ],
            ],
            'dataLabels' => ['enabled' => true, 'style' => ['fontSize' => '11px']],
            'colors' => ['#ef4444'],
            'grid' => ['borderColor' => '#e5e7eb'],
        ];
    }
}
