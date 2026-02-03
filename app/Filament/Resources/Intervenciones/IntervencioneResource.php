<?php

namespace App\Filament\Resources\Intervenciones;

use App\Filament\Resources\Intervenciones\Pages\CreateIntervencione;
use App\Filament\Resources\Intervenciones\Pages\EditIntervencione;
use App\Filament\Resources\Intervenciones\Pages\ListIntervenciones;
use App\Filament\Resources\Intervenciones\Schemas\IntervencioneForm;
use App\Filament\Resources\Intervenciones\Tables\IntervencionesTable;
use App\Models\Intervencione;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class IntervencioneResource extends Resource
{
    protected static ?string $model = Intervencione::class;
    protected static UnitEnum|string|null $navigationGroup = 'Monitoreo';
    protected static ?string $navigationLabel = 'Intervenciones';
    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ListBullet;

    protected static ?string $recordTitleAttribute = 'Intervenciones';

    public static function form(Schema $schema): Schema
    {
        return IntervencioneForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IntervencionesTable::configure($table);
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
            'index' => ListIntervenciones::route('/'),
            'create' => CreateIntervencione::route('/create'),
            'edit' => EditIntervencione::route('/{record}/edit'),
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
