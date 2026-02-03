<?php

namespace App\Filament\Resources\Servidores;

use App\Filament\Resources\Servidores\Pages\CreateServidores;
use App\Filament\Resources\Servidores\Pages\EditServidores;
use App\Filament\Resources\Servidores\Pages\ListServidores;
use App\Filament\Resources\Servidores\Schemas\ServidoresForm;
use App\Filament\Resources\Servidores\Tables\ServidoresTable;
use App\Models\Servidores;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;
class ServidoresResource extends Resource
{
    protected static ?string $model = Servidores::class;
    protected static UnitEnum|string|null $navigationGroup = 'Informática';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Servidores';

    public static function form(Schema $schema): Schema
    {
        return ServidoresForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServidoresTable::configure($table);
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
            'index' => ListServidores::route('/'),
            'create' => CreateServidores::route('/create'),
            'edit' => EditServidores::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
