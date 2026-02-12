<?php

namespace App\Filament\Resources\DesperfectosCamaras;

use App\Filament\Resources\DesperfectosCamaras\Pages\CreateDesperfectosCamara;
use App\Filament\Resources\DesperfectosCamaras\Pages\EditDesperfectosCamara;
use App\Filament\Resources\DesperfectosCamaras\Pages\ListDesperfectosCamaras;
use App\Filament\Resources\DesperfectosCamaras\Schemas\DesperfectosCamaraForm;
use App\Filament\Resources\DesperfectosCamaras\Tables\DesperfectosCamarasTable;
use App\Models\DesperfectosCamara;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DesperfectosCamaraResource extends Resource
{

protected static ?string $modelLabel = 'Falla de Cámara';
protected static ?string $pluralModelLabel = 'Fallas de Cámaras';
    protected static ?string $model = DesperfectosCamara::class;
    protected static UnitEnum|string|null $navigationGroup = 'Monitoreo';
    
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ExclamationTriangle;
    protected static ?string $navigationLabel = 'Desperfectos de Cámaras';
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'Desperfectos';

    public static function form(Schema $schema): Schema
    {
        return DesperfectosCamaraForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DesperfectosCamarasTable::configure($table);
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
            'index' => ListDesperfectosCamaras::route('/'),
            'create' => CreateDesperfectosCamara::route('/create'),
            'edit' => EditDesperfectosCamara::route('/{record}/edit'),
        ];
    }
}
