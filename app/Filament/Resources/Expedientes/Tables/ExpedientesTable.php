<?php

namespace App\Filament\Resources\Expedientes\Tables;

use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ExpedientesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha_ingreso')
                    ->label('Fecha de Ingreso')
                    ->date()
                    ->sortable(),
                TextColumn::make('numero_expediente')
                    ->searchable()
                    ->label('Número de Expediente'),
                TextColumn::make('iniciadorExpediente.nombre')
                    ->label('Iniciador')
                    ->sortable(),
                TextColumn::make('numero_nota')
                    ->label('Número de Nota')
                    ->searchable(),
                TextColumn::make('fecha_hora_inicio_exportacion')
                    ->label('Fecha y Hora Inicio Exportación')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('fecha_hora_fin_exportacion')
                    ->label('Fecha y Hora Fin Exportación')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('material_adjunto')
                    ->label('Material Adjunto')
                    ->searchable(),
                TextColumn::make('fecha_entrega')
                    ->label('Fecha de Entrega')
                    ->date()
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
            ->defaultSort('fecha_ingreso', 'desc')
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('iniciador_expediente_id')
                    ->label('Iniciador')
                    ->relationship('iniciadorExpediente', 'nombre'),
                
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
