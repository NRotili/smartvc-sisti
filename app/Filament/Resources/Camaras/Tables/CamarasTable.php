<?php

namespace App\Filament\Resources\Camaras\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CamarasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable(),
                    //tipo de cámara
                TextColumn::make('tipo.tipo')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lat')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lng')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cantIntervenciones')
                    ->label('Intervenciones')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('server.nombre')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('status')
                    ->boolean(),
                IconColumn::make('publicada')
                    ->boolean(),
                IconColumn::make('grabando')
                    ->boolean(),
                IconColumn::make('mantenimiento')
                    ->boolean(),
                IconColumn::make('activa')
                    ->boolean(),
                TextColumn::make('ip')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('url_imagen')
                //     ->searchable(),
                // TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('nombre')
            ->filters([
                //filtrar por tipo
                SelectFilter::make('tipo_id')
                    ->label('Tipo de Cámara')
                    ->relationship('tipo', 'tipo'),
                SelectFilter::make('server_id')
                    ->label('Servidor')
                    ->relationship('server', 'nombre'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    BulkAction::make('cambiarMantenimiento')
                        ->label('Mantenimiento')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(fn(Model $record) => $record->update(['mantenimiento' => !$record->mantenimiento]));
                        }),
                    BulkAction::make('cambiarPublicada')
                        ->label('Publicada')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(fn(Model $record) => $record->update(['publicada' => !$record->publicada]));
                        }),
                ]),
            ]);
    }
}
