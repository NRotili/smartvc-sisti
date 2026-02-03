<?php

namespace App\Filament\Resources\TipoCamaras;

use App\Filament\Resources\TipoCamaras\Pages\CreateTipoCamaras;
use App\Filament\Resources\TipoCamaras\Pages\EditTipoCamaras;
use App\Filament\Resources\TipoCamaras\Pages\ListTipoCamaras;
use App\Filament\Resources\TipoCamaras\Schemas\TipoCamarasForm;
use App\Filament\Resources\TipoCamaras\Tables\TipoCamarasTable;
use App\Models\TipoCamaras;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TipoCamarasResource extends Resource
{
    protected static ?string $model = TipoCamaras::class;
    protected static UnitEnum|string|null $navigationGroup = 'Monitoreo';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Tipos de Cámaras';
    protected static ?int $navigationSort = 5;
    protected static ?string $recordTitleAttribute = 'TipoCamaras';

    public static function form(Schema $schema): Schema
    {
        return TipoCamarasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TipoCamarasTable::configure($table);
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
            'index' => ListTipoCamaras::route('/'),
            'create' => CreateTipoCamaras::route('/create'),
            'edit' => EditTipoCamaras::route('/{record}/edit'),
        ];
    }
}
