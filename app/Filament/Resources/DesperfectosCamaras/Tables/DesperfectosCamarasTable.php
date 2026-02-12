<?php

namespace App\Filament\Resources\DesperfectosCamaras\Tables;

use Dom\Text;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

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
            ])
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
                        ->action(fn (array $records) => redirect()->route('filament.resources.desperfectos-camaras.asignar-falla', ['record' => $records]))
                        ->icon('heroicon-o-wrench'),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
