<?php

namespace App\Filament\Resources\SensoresPresiones\Schemas;

use App\Models\SensoresPresione;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SensoresPresioneInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nombre'),
                TextEntry::make('topic_id'),
                TextEntry::make('presion_minima')
                    ->numeric(),
                TextEntry::make('presion_maxima')
                    ->numeric(),
                TextEntry::make('observaciones')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (SensoresPresione $record): bool => $record->trashed()),
            ]);
    }
}
