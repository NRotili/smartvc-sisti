<?php

namespace App\Filament\Resources\SensoresPresiones;

use App\Filament\Resources\SensoresPresiones\Pages\CreateSensoresPresione;
use App\Filament\Resources\SensoresPresiones\Pages\EditSensoresPresione;
use App\Filament\Resources\SensoresPresiones\Pages\ListSensoresPresiones;
use App\Filament\Resources\SensoresPresiones\Pages\ViewSensoresPresione;
use App\Filament\Resources\SensoresPresiones\Schemas\SensoresPresioneForm;
use App\Filament\Resources\SensoresPresiones\Schemas\SensoresPresioneInfolist;
use App\Filament\Resources\SensoresPresiones\Tables\SensoresPresionesTable;
use App\Models\SensoresPresione;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SensoresPresioneResource extends Resource
{
    protected static ?string $model = SensoresPresione::class;

       protected static UnitEnum|string|null $navigationGroup = 'SAyMA';

    protected static ?string $modelLabel = 'Sensor de Presión';
    protected static ?string $pluralModelLabel = 'Sensores de Presión';
    protected static ?string $navigationLabel = 'Sensores de Presión';

    //Icono de agua
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBarSquare;

    protected static ?string $recordTitleAttribute = 'Sensores de Presión';

    public static function form(Schema $schema): Schema
    {
        return SensoresPresioneForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SensoresPresioneInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SensoresPresionesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSensoresPresiones::route('/'),
            'create' => CreateSensoresPresione::route('/create'),
            'view' => ViewSensoresPresione::route('/{record}'),
            'edit' => EditSensoresPresione::route('/{record}/edit'),
        ];
    }
}
