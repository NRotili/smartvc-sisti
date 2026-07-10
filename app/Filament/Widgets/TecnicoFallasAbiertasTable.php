<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\DesperfectosCamaras\DesperfectosCamaraResource;
use App\Models\DesperfectosCamara;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TecnicoFallasAbiertasTable extends TableWidget
{
    protected static ?string $heading = 'Fallas abiertas';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = true;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DesperfectosCamara::query()
                    ->whereNull('hora_solucion')
                    // La relación camara() usa withTrashed(): excluir fallas de cámaras eliminadas
                    ->whereHas('camara', fn ($query) => $query->withoutTrashed())
                    ->with(['camara', 'fallaCamara'])
            )
            ->columns([
                TextColumn::make('camara.nombre')
                    ->label('Cámara')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fallaCamara.tipo_falla')
                    ->label('Tipo de falla')
                    ->placeholder('Sin clasificar'),
                TextColumn::make('fecha_desperfecto')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('hora_desperfecto')
                    ->label('Hora')
                    ->time(),
                TextColumn::make('antiguedad')
                    ->label('Antigüedad')
                    ->badge()
                    ->getStateUsing(function (DesperfectosCamara $record): string {
                        $dias = self::diasAbierta($record);

                        return $dias === null
                            ? '—'
                            : ($dias === 0 ? 'Hoy' : "{$dias} día".($dias === 1 ? '' : 's'));
                    })
                    ->color(fn (DesperfectosCamara $record): string => match (true) {
                        self::diasAbierta($record) === null => 'gray',
                        self::diasAbierta($record) >= 7 => 'danger',
                        self::diasAbierta($record) >= 2 => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('observaciones')
                    ->label('Observaciones')
                    ->limit(60)
                    ->placeholder('—'),
            ])
            ->defaultSort('fecha_desperfecto', 'asc')
            ->recordUrl(fn (DesperfectosCamara $record): string => DesperfectosCamaraResource::getUrl('edit', ['record' => $record]))
            ->paginated([5, 10, 25])
            ->emptyStateHeading('Sin fallas abiertas')
            ->emptyStateDescription('Todas las fallas registradas fueron resueltas.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    private static function diasAbierta(DesperfectosCamara $record): ?int
    {
        if (! $record->fecha_desperfecto) {
            return null;
        }

        try {
            $inicio = Carbon::parse(trim(substr($record->fecha_desperfecto, 0, 10).' '.($record->hora_desperfecto ?? '00:00:00')));

            return (int) $inicio->diffInDays(now());
        } catch (\Throwable) {
            return null;
        }
    }
}
