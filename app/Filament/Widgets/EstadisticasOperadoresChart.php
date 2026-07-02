<?php

namespace App\Filament\Widgets;

use App\Models\Intervencione;
use App\Models\User;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Livewire\Attributes\On;

class EstadisticasOperadoresChart extends ApexChartWidget
{

    protected static ?string $chartId = 'estadisticasOperadoresChart';
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
        if ($this->operadorId) {
            $nombre = User::find($this->operadorId)?->name;
            return 'Categorías de intervenciones — ' . ($nombre ?? '');
        }
        return 'Intervenciones por operador';
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
        return $this->operadorId
            ? $this->getOpcionesPorOperador()
            : $this->getOpcionesTodosOperadores();
    }

    private function getOpcionesTodosOperadores(): array
    {
        $users        = User::role('Operador de Monitoreo')->orderBy('name')->get();
        $categories   = [];
        $creadas      = [];
        $participadas = [];

        foreach ($users as $user) {
            $categories[]   = $user->name;
            $creadas[]      = Intervencione::where('user_id', $user->id)
                ->whereDate('fecha_hora', '>=', $this->fechaInicio)
                ->whereDate('fecha_hora', '<=', $this->fechaFin)
                ->count();
            $participadas[] = $user->intervencionesMonitoreo()
                ->whereDate('fecha_hora', '>=', $this->fechaInicio)
                ->whereDate('fecha_hora', '<=', $this->fechaFin)
                ->count();
        }

        return [
            'chart' => [
                'type'       => 'bar',
                'height'     => 420,
                'toolbar'    => ['show' => false],
                'fontFamily' => 'inherit',
            ],
            'series' => [
                ['name' => 'Creó',            'data' => $creadas],
                ['name' => 'Estuvo presente', 'data' => $participadas],
            ],
            'xaxis' => [
                'categories' => $categories,
                'labels'     => ['style' => ['fontSize' => '12px']],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal'   => false,
                    'columnWidth'  => '50%',
                    'borderRadius' => 4,
                    'dataLabels'   => ['position' => 'top'],
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'offsetY' => -20,
                'style'   => ['fontSize' => '11px', 'colors' => ['#374151']],
            ],
            'colors' => ['#f59e0b', '#3b82f6'],
            'legend' => ['position' => 'top', 'horizontalAlign' => 'right'],
            'grid'   => ['borderColor' => '#e5e7eb'],
        ];
    }

    private function getOpcionesPorOperador(): array
    {
        $user = User::find($this->operadorId);

        $intervencioneIds = $this->getIntervencioneIdsDelOperador($user);

        $categorias = Intervencione::whereIn('id', $intervencioneIds)
            ->with('categoria')
            ->get()
            ->groupBy('categoria_id')
            ->map(fn($group) => [
                'nombre' => $group->first()->categoria?->nombre ?? 'Sin categoría',
                'count'  => $group->count(),
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
                ['name' => 'Intervenciones', 'data' => $categorias->pluck('count')->toArray()],
            ],
            'xaxis' => [
                'categories' => $categorias->pluck('nombre')->toArray(),
                'labels'     => ['style' => ['fontSize' => '12px']],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal'   => true,
                    'borderRadius' => 4,
                ],
            ],
            'dataLabels' => ['enabled' => true, 'style' => ['fontSize' => '11px']],
            'colors'     => ['#f59e0b'],
            'grid'       => ['borderColor' => '#e5e7eb'],
        ];
    }

    public function getIntervencioneIdsDelOperador(?User $user): \Illuminate\Support\Collection
    {
        if (!$user) return collect();

        $creadas = Intervencione::where('user_id', $user->id)
            ->whereDate('fecha_hora', '>=', $this->fechaInicio)
            ->whereDate('fecha_hora', '<=', $this->fechaFin)
            ->pluck('id');

        $participadas = $user->intervencionesMonitoreo()
            ->whereDate('fecha_hora', '>=', $this->fechaInicio)
            ->whereDate('fecha_hora', '<=', $this->fechaFin)
            ->pluck('intervenciones.id');

        return $creadas->merge($participadas)->unique();
    }
}
