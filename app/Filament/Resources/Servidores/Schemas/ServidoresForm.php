<?php

namespace App\Filament\Resources\Servidores\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServidoresForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('descripcion')
                    ->required(),
                TextInput::make('ip')
                    ->required(),
            ]);
    }
}
