<?php

namespace App\Filament\Resources\DesperfectosCamaras\Tables;

use App\Models\DesperfectosCamara;
use Dom\Text;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class DesperfectosCamarasTable
{

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha_desperfecto')
                    ->label('Fecha de Desperfecto')
                    ->date()
                    ->sortable(),
                TextColumn::make('hora_desperfecto')
                    ->label('Hora de Desperfecto')
                    ->time()
                    ->sortable(),
                TextColumn::make('camara.nombre')
                    ->label('Cámara')
                    ->sortable(),
                TextColumn::make('fallaCamara.tipo_falla')
                    ->label('Tipo de Falla')
                    ->sortable(),
                TextColumn::make('fecha_solucion')
                    ->label('Fecha de Solución')
                    ->date()
                    ->sortable(),
                TextColumn::make('hora_solucion')
                    ->label('Hora de Solución')
                    ->time()
                    ->sortable(),
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
            ])->defaultSort('fecha_desperfecto', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('asignarFalla')
                        ->label('Asignar Falla')
                        ->action(fn(array $records) => redirect()->route('filament.resources.desperfectos-camaras.asignar-falla', ['record' => $records]))
                        ->icon('heroicon-o-wrench')
                        ->form([
                            Select::make('falla_id')
                                ->label('Falla')
                                ->relationship('fallaCamara', 'tipo_falla') // 👈 si tenés relación
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update(['falla_camara_id' => $data['falla_id']]);
                        })
                        ->successNotificationTitle("Falla asignada correctamente a los desperfectos seleccionados.")
                        ->failureNotificationTitle("Error al asignar la falla a los desperfectos seleccionados.")
                        ->deselectRecordsAfterCompletion()
                        ->authorize(fn() => Auth::user()->can('AsignarFalla:DesperfectoCamara')),
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
