<?php

namespace App\Filament\Widgets;

use App\Models\ConocimientoIntervencione;
use App\Models\Intervencione;
use App\Models\User;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Livewire\Attributes\On;

class EstadisticasConocimientosChart extends ApexChartWidget
{
    protected static ?string $chartId = 'estadisticasConocimientosChart';
    protected static ?int $contentHeight = 420;
    protected ?string $pollingInterval = null;

    public string $fechaInicio = '';
    public string $fechaFin    = '';
    public ?int   $operadorId  = null;

    public function mount(): void
    {
        $this->fechaInicio = now()->startOfMonth()->toDateString();
        $this->fechaFin    = now()->toDateString();
        parent::mount();
    }

    public function getHeading(): ?string
    {
        return 'Conocimientos notificados';
    }

    #[On('filtrarEstadisticas')]
    public function actualizarFiltro(string $fechaInicio, string $fechaFin, ?int $operadorId): void
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin    = $fechaFin;
        $this->operadorId  = $operadorId;
        $this->updateOptions();
    }

    protected function getOptions(): array
    {
        if (!$this->operadorId) {
            return [
                'chart'  => ['type' => 'bar', 'height' => 420, 'toolbar' => ['show' => false], 'fontFamily' => 'inherit'],
                'series' => [],
                'xaxis'  => ['categories' => []],
                'noData' => [
                    'text'  => 'Seleccioná un operador para ver los conocimientos',
                    'style' => ['fontSize' => '14px', 'color' => '#6b7280'],
                ],
            ];
        }

        $user = User::find($this->operadorId);

        $creadasIds = Intervencione::where('user_id', $user->id)
            ->whereDate('fecha_hora', '>=', $this->fechaInicio)
            ->whereDate('fecha_hora', '<=', $this->fechaFin)
            ->pluck('id');

        $participadasIds = $user->intervencionesMonitoreo()
            ->whereDate('fecha_hora', '>=', $this->fechaInicio)
            ->whereDate('fecha_hora', '<=', $this->fechaFin)
            ->pluck('intervenciones.id');

        $intervencioneIds = $creadasIds->merge($participadasIds)->unique();

        $conocimientos = ConocimientoIntervencione::whereIn('intervencione_id', $intervencioneIds)
            ->with('conocimiento')
            ->get()
            ->groupBy('conocimiento_id')
            ->map(fn($g) => [
                'nombre' => $g->first()->conocimiento?->nombre ?? 'Desconocido',
                'count'  => $g->count(),
            ])
            ->sortByDesc('count')
            ->values();

        return [
            'chart' => [
                'type'       => 'bar',
                'height'     => 420,
                'toolbar'    => ['show' => false],
                'fontFamily' => 'inherit',
            ],
            'series' => [
                ['name' => 'Llamados', 'data' => $conocimientos->pluck('count')->toArray()],
            ],
            'xaxis' => [
                'categories' => $conocimientos->pluck('nombre')->toArray(),
                'labels'     => ['style' => ['fontSize' => '12px']],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal'   => true,
                    'borderRadius' => 4,
                ],
            ],
            'dataLabels' => ['enabled' => true, 'style' => ['fontSize' => '11px']],
            'colors'     => ['#3b82f6'],
            'grid'       => ['borderColor' => '#e5e7eb'],
            'noData'     => [
                'text'  => 'Sin conocimientos en el período',
                'style' => ['fontSize' => '14px', 'color' => '#6b7280'],
            ],
        ];
    }
}
