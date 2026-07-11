<?php

namespace App\Filament\Resources\Intervenciones\Tables;

use App\Filament\Resources\Intervenciones\IntervencioneResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class IntervencionesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // las ampliaciones no se listan como filas propias: viven en la línea histórica de su intervención raíz
            ->modifyQueryUsing(fn ($query) => $query->whereNull('intervencion_padre_id'))
            ->columns([
                TextColumn::make('fecha_hora')
                    ->label('Fecha y Hora')
                    ->dateTime()
                    ->formatStateUsing(fn ($state) => ucfirst(
                        \Carbon\Carbon::parse($state)->locale('es')->isoFormat('MMM D, YYYY HH:mm')
                    ))
                    ->sortable()
                    ->color(fn ($record): ?string => match ($record->fuera_de_plazo) {
                        'Es posible que no haya sido vista en tiempo real' => 'danger',
                        'Cargada fuera de plazo' => 'warning',
                        default => null,
                    })
                    ->tooltip(function ($record): ?string {
                        $alerta = $record->fuera_de_plazo;
                        if (! $alerta) {
                            return null;
                        }

                        $diff = \Carbon\Carbon::parse($record->fecha_hora)
                            ->locale('es')
                            ->diffForHumans(\Carbon\Carbon::parse($record->created_at), true);

                        return $alerta.' ('.$diff.' después)';
                    }),
                // Dscripción html y que no se escape
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->html()
                    ->wrap()
                    ->lineClamp(2),
                IconColumn::make('estado')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('categoria.nombre')
                    ->label('Categoría')
                    ->sortable(),
                TextColumn::make('ampliaciones_count')
                    ->label('Ampliaciones')
                    ->counts('ampliaciones')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state : null),
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable()
                    // visible solo para el permiso "VerUsuarios:Intervenciones",
                    ->visible(auth()->user()->can('VerUsuarios:Intervenciones')),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('fecha_hora', 'desc')
            ->filters([
                SelectFilter::make('categoria_id')
                    ->label('Categoría')
                    ->relationship('categoria', 'nombre'),
                // between fecha_hora

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('ampliar')
                        ->label('Ampliar')
                        ->icon('heroicon-o-plus-circle')
                        ->color('warning')
                        ->url(fn ($record) => IntervencioneResource::getUrl('create', ['padre' => $record->id]))
                        ->visible(fn () => IntervencioneResource::canCreate()),
                    EditAction::make()
                        ->authorize(fn ($record) => $record->canBeEditedBy(auth()->user())),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
