<?php

namespace App\Filament\Resources\Intervenciones\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class IntervencioneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Detalles de la Intervención')
                    ->schema([
                        Select::make('categoria_id')
                            ->relationship('categoria', 'nombre')
                            ->label('Categoría')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->loadingMessage('Cargando categorías...')
                            ->searchPrompt('Buscar categoría...')
                            ->placeholder('Selecciona una categoría')
                            ->createOptionForm([
                                TextInput::make('nombre')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->editOptionForm([
                                TextInput::make('nombre')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        DateTimePicker::make('fecha_hora')
                            ->label('Fecha y Hora')
                            ->required(),
                        RichEditor::make('descripcion')
                            ->label('Descripción')
                            ->required()
                            ->disableToolbarButtons([
                                'attachFiles',
                                'codeBlock',
                                'h2',
                                'h3',
                                'link',
                                'strike',
                                'subscript',
                                'superscript',
                            ])->columnSpanFull(),
                        Repeater::make('camaras')
                            ->label('Cámaras Involucradas')
                            ->relationship('camaras')
                            ->schema([
                                Select::make('camara_id')
                                    ->relationship('camara', 'nombre')
                                    ->label('Cámara')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->loadingMessage('Cargando cámaras...')
                                    ->searchPrompt('Buscar cámara...')
                                    ->placeholder('Selecciona una cámara'),
                                DateTimePicker::make('fecha_hora')
                                    ->label('Fecha y hora de grabación')
                                    ->required(),
                            ])->columns(2)
                            ->reorderable(true)
                            ->addActionLabel('Agregar Cámara'),
                        Repeater::make('conocimientos')
                            ->label('Interventores Relacionados')
                            ->relationship('conocimientos')
                            ->schema([
                                Select::make('conocimiento_id')
                                    ->relationship('conocimiento', 'nombre')
                                    ->label('Interventor')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->loadingMessage('Cargando interventores...')
                                    ->searchPrompt('Buscar interventor...')
                                    ->placeholder('Selecciona un interventor')
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->required()
                                            ->maxLength(255),
                                    ])
                                    ->editOptionForm([
                                        TextInput::make('nombre')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                            ])->columns(1)
                            ->reorderable(true)
                            ->addActionLabel('Agregar Interventor'),
                    ])->columnSpan(6),
            ]);
    }
}
