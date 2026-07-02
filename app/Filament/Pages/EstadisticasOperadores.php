<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\EstadisticasConocimientosChart;
use App\Filament\Widgets\EstadisticasOperadoresChart;
use App\Models\Intervencione;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class EstadisticasOperadores extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBarSquare;
    protected static UnitEnum|string|null $navigationGroup = 'Sistema';
    protected static ?string $navigationLabel = 'Estadísticas de Operadores';
    protected static ?int $navigationSort = 11;
    protected string $view = 'filament.pages.estadisticas-operadores';

    public string $fechaInicio;
    public string $fechaFin;
    public ?int   $operadorId = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('ViewEstadisticas:Operadores') ?? false;
    }

    public function mount(): void
    {
        $this->fechaInicio = now()->startOfMonth()->toDateString();
        $this->fechaFin    = now()->toDateString();
    }

    public function actualizar(): void
    {
        $this->dispatch('filtrarEstadisticas',
            fechaInicio: $this->fechaInicio,
            fechaFin:    $this->fechaFin,
            operadorId:  $this->operadorId,
        );
    }

    protected function getFooterWidgets(): array
    {
        return [
            EstadisticasOperadoresChart::class,
            EstadisticasConocimientosChart::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 2;
    }

    protected function getViewData(): array
    {
        $operadores = User::role('Operador de Monitoreo')->orderBy('name')->get();
        $user       = $this->operadorId ? User::find($this->operadorId) : null;

        return [
            'operadores'     => $operadores,
            'tableData'      => $this->getTableData($operadores),
            'operadorNombre' => $user?->name,
        ];
    }

    private function getTableData($operadores): array
    {
        $lista  = $this->operadorId ? $operadores->where('id', $this->operadorId) : $operadores;
        $result = [];

        foreach ($lista as $user) {
            $creadasIds = Intervencione::where('user_id', $user->id)
                ->whereDate('fecha_hora', '>=', $this->fechaInicio)
                ->whereDate('fecha_hora', '<=', $this->fechaFin)
                ->pluck('id');

            $participadasIds = $user->intervencionesMonitoreo()
                ->whereDate('fecha_hora', '>=', $this->fechaInicio)
                ->whereDate('fecha_hora', '<=', $this->fechaFin)
                ->pluck('intervenciones.id');

            $result[] = [
                'nombre'       => $user->name,
                'creadas'      => $creadasIds->count(),
                'participadas' => $participadasIds->count(),
                'total'        => $creadasIds->merge($participadasIds)->unique()->count(),
            ];
        }

        usort($result, fn($a, $b) => $b['total'] <=> $a['total']);

        return $result;
    }
}
