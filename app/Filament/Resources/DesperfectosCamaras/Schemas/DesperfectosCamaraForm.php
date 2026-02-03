<?php

namespace App\Filament\Resources\DesperfectosCamaras\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class DesperfectosCamaraForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('fecha_desperfecto')
                    ->required(),
                TimePicker::make('hora_desperfecto')
                    ->required(),
                Select::make('falla_camara_id')
                    ->label('Tipo de Falla')
                    ->relationship('fallaCamara', 'tipo_falla')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->loadingMessage('Cargando tipos de falla...')
                    ->searchPrompt('Buscar tipo de falla...')
                    ->placeholder('Selecciona un tipo de falla')
                    ->createOptionForm([
                        TextInput::make('tipo_falla')
                            ->required()
                            ->maxLength(255),
                        TextArea::make('descripcion')
                            ->maxLength(5000),
                    ])
                    ->editOptionForm([
                        TextInput::make('tipo_falla')
                            ->required()
                            ->maxLength(255),
                        TextArea::make('descripcion')
                            ->maxLength(5000),
                    ]),
                Select::make('camara_id')
                    ->label('Cámara')
                    ->relationship('camara', 'nombre', fn ($query) => $query->orderBy('nombre'))
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nombre} - {$record->descripcion}")
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->loadingMessage('Cargando cámaras...')
                    ->searchPrompt('Buscar cámara...')
                    ->placeholder('Selecciona una cámara'),
                DatePicker::make('fecha_solucion'),
                TimePicker::make('hora_solucion'),
                Textarea::make('observaciones')
                    ->columnSpanFull(),
            ]);
    }
}
