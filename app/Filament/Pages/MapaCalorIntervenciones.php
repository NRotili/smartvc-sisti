<?php

namespace App\Filament\Pages;

use App\Models\Camara;
use App\Models\CamaraIntervencione;
use App\Models\CategoriasIntervencione;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class MapaCalorIntervenciones extends Page
{
    use HasPageShield;

    protected static ?string $title = 'Mapa de Calor de Intervenciones';

    protected static UnitEnum|string|null $navigationGroup = 'Monitoreo';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Fire;

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.mapa-calor-intervenciones';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'fechaInicio' => now()->subMonths(6)->toDateString(),
            'fechaFin' => now()->toDateString(),
            'categoriaIds' => [],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('fechaInicio')
                    ->label('Desde')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->maxDate(now()),
                DatePicker::make('fechaFin')
                    ->label('Hasta')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->maxDate(now()),
                Select::make('categoriaIds')
                    ->label('Categorías')
                    ->multiple()
                    ->options(CategoriasIntervencione::orderBy('nombre')->pluck('nombre', 'id'))
                    ->searchable()
                    ->native(false)
                    ->placeholder('Todas las categorías'),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function actualizar(): void
    {
        $this->dispatch('heatmap-actualizado', puntos: $this->getPuntos());
    }

    protected function getViewData(): array
    {
        $puntos = $this->getPuntos();

        return [
            'puntos' => $puntos,
            'topCamaras' => collect($puntos)->sortByDesc('total')->take(10)->values(),
        ];
    }

    /**
     * Intervenciones agrupadas por cámara (con coordenadas) según los filtros.
     *
     * @return array<int, array{lat: float, lng: float, nombre: string, total: int}>
     */
    public function getPuntos(): array
    {
        $fechaInicio = $this->data['fechaInicio'] ?? now()->subMonths(6)->toDateString();
        $fechaFin = $this->data['fechaFin'] ?? now()->toDateString();
        $categoriaIds = $this->data['categoriaIds'] ?? [];

        $conteos = CamaraIntervencione::query()
            ->join('intervenciones', 'intervenciones.id', '=', 'camara_intervencione.intervencione_id')
            ->whereNull('intervenciones.deleted_at')
            ->whereDate('intervenciones.fecha_hora', '>=', $fechaInicio)
            ->whereDate('intervenciones.fecha_hora', '<=', $fechaFin)
            ->when($categoriaIds !== [], fn ($query) => $query->whereIn('intervenciones.categoria_id', $categoriaIds))
            ->groupBy('camara_intervencione.camara_id')
            ->selectRaw('camara_intervencione.camara_id, count(distinct camara_intervencione.intervencione_id) as total')
            ->pluck('total', 'camara_id');

        if ($conteos->isEmpty()) {
            return [];
        }

        return Camara::whereIn('id', $conteos->keys())
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get()
            ->map(fn (Camara $camara) => [
                'lat' => (float) $camara->lat,
                'lng' => (float) $camara->lng,
                'nombre' => $camara->nombre,
                'total' => (int) $conteos[$camara->id],
            ])
            ->values()
            ->all();
    }
}
