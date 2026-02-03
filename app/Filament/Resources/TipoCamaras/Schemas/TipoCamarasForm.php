<?php

namespace App\Filament\Resources\TipoCamaras\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TipoCamarasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tipo')
                    ->required(),
            ]);
    }
}
