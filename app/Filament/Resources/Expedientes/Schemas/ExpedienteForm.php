<?php

namespace App\Filament\Resources\Expedientes\Schemas;

use Dom\Text;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ExpedienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('fecha_ingreso')
                    ->label('Fecha de Ingreso')
                    ->required(),
                TextInput::make('numero_expediente')
                    ->label('Número de Expediente')
                    ->required(),
                Select::make('iniciador_expediente_id')
                    ->relationship('iniciadorExpediente', 'nombre')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->loadingMessage('Cargando iniciadores...')
                    ->searchPrompt('Buscar iniciador...')
                    ->placeholder('Selecciona un iniciador')
                    ->createOptionForm([
                        TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('contacto')
                            ->maxLength(500),
                        TextInput::make('observaciones')
                            ->maxLength(1000),
                    ])
                    ->editOptionForm([
                        TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('contacto')
                            ->maxLength(500),
                        TextInput::make('observaciones')
                            ->maxLength(1000),
                    ]),
                TextInput::make('numero_nota'),
                Select::make('camaras')
                    ->label('Cámaras solicitadas')
                    ->relationship('camaras', 'nombre')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->loadingMessage('Cargando cámaras...')
                    ->searchPrompt('Buscar cámara...')
                    ->placeholder('Selecciona una cámara')
                    ->columnSpanFull()
                    ->required(),
                DateTimePicker::make('fecha_hora_inicio_exportacion')
                    ->label('Fecha y hora de inicio de exportación')
                    ->required(),
                DateTimePicker::make('fecha_hora_fin_exportacion')
                    ->label('Fecha y hora de fin de exportación')
                    ->required(),
                TextInput::make('material_adjunto'),
                DatePicker::make('fecha_entrega')
                    ->label('Fecha de Entrega'),
                Textarea::make('observaciones')
                    ->columnSpanFull(),
            ])->columns(2);
    }
}
