<?php

namespace App\Filament\Resources\SensoresPresiones\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SensoresPresioneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('topic_id')
                    ->required(),
                TextInput::make('presion_minima')
                    ->required()
                    ->numeric(),
                TextInput::make('presion_maxima')
                    ->required()
                    ->numeric(),
                Textarea::make('observaciones')
                    ->columnSpanFull(),
            ]);
    }
}
