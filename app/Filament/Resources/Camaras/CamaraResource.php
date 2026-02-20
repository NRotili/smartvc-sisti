<?php

namespace App\Filament\Resources\Camaras;

use App\Filament\Resources\Camaras\Pages\CreateCamara;
use App\Filament\Resources\Camaras\Pages\EditCamara;
use App\Filament\Resources\Camaras\Pages\ListCamaras;
use App\Filament\Resources\Camaras\Pages\ViewCamara;
use App\Filament\Resources\Camaras\Schemas\CamaraForm;
use App\Filament\Resources\Camaras\Tables\CamarasTable;
use App\Models\Camara;
use BackedEnum;
use Barryvdh\Debugbar\Facades\Debugbar;
use EduardoRibeiroDev\FilamentLeaflet\Infolists\MapEntry;
use EduardoRibeiroDev\FilamentLeaflet\Support\Markers\Marker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section as ComponentsSection;

class CamaraResource extends Resource
{
    protected static ?string $modelLabel = 'Cámara';
    protected static ?string $pluralModelLabel = 'Cámaras';
    protected static ?string $model = Camara::class;
    protected static UnitEnum|string|null $navigationGroup = 'Monitoreo';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::VideoCamera;
    protected static ?string $navigationLabel = 'Cámaras';
    protected static ?string $recordTitleAttribute = 'Cámaras';
    protected static ?int $navigationSort = 1;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'synchronize', // 👈 agregado
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return CamaraForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CamarasTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
            'index' => ListCamaras::route('/'),
            'create' => CreateCamara::route('/create'),
            'view' => ViewCamara::route('/{record}'),
            'edit' => EditCamara::route('/{record}/edit'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                ComponentsSection::make('Cámara en vivo')
                    ->schema([
                        ImageEntry::make('url_imagen')
                            ->hiddenLabel()
                            ->extraAttributes(['class' => 'justify-center'])
                            ->size('full'),
                    ])->columnSpan(1),
                ComponentsSection::make('Ubicación de la cámara')
                    ->schema([
                        MapEntry::make('location')
                            ->hiddenLabel()
                            ->latitudeFieldName('lat')
                            ->longitudeFieldName('lng')
                            ->height(284)
                            ->zoom(17)
                            ->pickMarker(fn(Marker $marker) => $marker->red())
                            ->static()    // Disable interactions (enabled by default)
                            ->columnSpanFull()
                    ])->columnSpan(1),

                ComponentsSection::make('Información de la cámara')
                    ->schema([
                        TextEntry::make('nombre')
                            ->label('Nombre'),
                        TextEntry::make('descripcion')
                            ->label('Descripción'),
                        TextEntry::make('tipo.tipo')
                            ->label('Tipo'),
                        TextEntry::make('cantIntervenciones')
                            ->label('Cantidad de intervenciones'),
                        TextEntry::make('server.nombre')
                            ->label('Servidor'),
                        TextEntry::make('ip')
                            ->label('Dirección IP'),
                        TextEntry::make('created_at')
                            ->label('Creado el'),
                        TextEntry::make('updated_at')
                            ->label('Actualizado el'),
                    ])->columns(4),
                ComponentsSection::make('Estados de la cámara')
                    ->schema([
                        IconEntry::make('status')
                            ->label('Estado')
                            ->boolean(),
                        IconEntry::make('publicada')
                            ->label('Publicada')
                            ->boolean(),
                        IconEntry::make('grabando')
                            ->label('Grabando')
                            ->boolean(),
                        IconEntry::make('mantenimiento')
                            ->label('Mantenimiento')
                            ->boolean(),
                        IconEntry::make('activa')
                            ->label('Activa')
                            ->boolean(),
                    ])->columns(5)->collapsible(),
            ]);
    }
}
