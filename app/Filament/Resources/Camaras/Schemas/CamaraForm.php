<?php

namespace App\Filament\Resources\Camaras\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CamaraForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required()
                    ->readOnly()
                    ->disabled(),
                TextInput::make('descripcion')
                    ->label('Descripción')
                    ->required()
                    ->readOnly()
                    ->disabled(),
                Select::make('tipo_id')
                    ->relationship('tipo', 'tipo')
                    ->required()
                    ->disabled(),
                TextInput::make('lat')
                    ->label('Latitud')
                    ->required()
                    ->readOnly()
                    ->disabled(),
                TextInput::make('lng')
                    ->label('Longitud')
                    ->required()
                    ->readOnly()
                    ->disabled(),
                TextInput::make('cantIntervenciones')
                    ->label('Cantidad de Intervenciones')
                    ->readOnly()
                    ->disabled()
                    ->numeric(),
                Select::make('server_id')
                    ->label('Servidor')
                    ->relationship('server', 'nombre')
                    ->required()
                    ->disabled(),
                Toggle::make('status')
                    ->required()
                    ->disabled(),
                Toggle::make('publicada')
                    ->required(),
                Toggle::make('grabando')
                    ->required()
                    ->disabled(),
                Toggle::make('mantenimiento')
                    ->required(),
                Toggle::make('activa')
                    ->required()
                    ->disabled(),
                TextInput::make('ip')
                    ->label('Dirección IP')
                    ->readOnly()
                    ->disabled()
                    ->required(),

            ]);
    }
}
