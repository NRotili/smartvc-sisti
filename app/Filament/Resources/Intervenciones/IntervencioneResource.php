<?php

namespace App\Filament\Resources\Intervenciones;

use App\Filament\Resources\Intervenciones\Pages\CreateIntervencione;
use App\Filament\Resources\Intervenciones\Pages\EditIntervencione;
use App\Filament\Resources\Intervenciones\Pages\ListIntervenciones;
use App\Filament\Resources\Intervenciones\Pages\ViewIntervencione;
use App\Filament\Resources\Intervenciones\Schemas\IntervencioneForm;
use App\Filament\Resources\Intervenciones\Tables\IntervencionesTable;
use App\Models\Intervencione;
use BackedEnum;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use Dom\Text;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section as ComponentsSection;
use Symfony\Component\ErrorHandler\Debug;

class IntervencioneResource extends Resource
{
    //Breadcrumbs
    protected static ?string $modelLabel = 'Intervención';
    protected static ?string $pluralModelLabel = 'Intervenciones';

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
            'view' => ViewIntervencione::route('/{record}'),
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


    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('Información de la intervención')
                    ->schema([
                        TextEntry::make('fecha_hora')
                            ->label('Fecha y Hora')
                            ->dateTime(),
                        TextEntry::make('user.name')
                            ->label('Usuario'),
                        TextEntry::make('descripcion')
                            ->label('Descripción')
                            ->html()
                            ->columnSpanFull(),

                    ])->columns(2)->columnSpanFull(),
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Interventores')
                            ->schema([
                                TextEntry::make('conocimientos.conocimiento.nombre')
                                    ->label('Toman conocimiento las siguientes fuerzas.')
                                    ->badge(),
                            ]),
                        Tab::make('Categorías')
                            ->schema([
                                TextEntry::make('categoria.nombre')
                                    ->label('La intervención se clasifica en la categoría.')
                                    ->badge(),
                            ]),
                        Tab::make('Cámaras')
                            ->schema([
                                RepeatableEntry::make('camaras')
                                    ->label('Cámaras relacionadas con la intervención')
                                    ->schema([
                                        TextEntry::make('camara.nombre')
                                            ->label('Cámara')
                                            ->badge(),

                                        TextEntry::make('fecha_hora')
                                            ->label('Fecha y hora')
                                            ->dateTime('d/m/Y H:i'),

                                        TextEntry::make('grabacion')
                                            ->label('Reproducción')
                                            ->state('Ver grabación')
                                            //url debe ser creada con parámetros de la cámara y fecha_hora
                                            ->url(function ($record) {
                                                // Debugbar::info('Generando URL para grabación de cámara', ['record' => $record]);
                                                $fecha = Carbon::parse($record->fecha_hora);
                                                $startDate = $fecha->format('Y.m.d');
                                                $startTime = $fecha->format('H.i.s');
                                                $end = $fecha->copy()->addSeconds(60);
                                                $endDate = $end->format('Y.m.d');
                                                $endTime = $end->format('H.i.s');
                                                Debugbar::info('digifort_USER y PASSWORD', ['DIGIFORT_USER' => env('DIGIFORT_USER'), 'DIGIFORT_PASSWORD' => env('DIGIFORT_PASSWORD')]);
                                                // Debugbar::info('Fecha y hora formateada', ['startDate' => $startDate, 'startTime' => $startTime]);
                                                return sprintf(
                                                    'http://%s:8601/Interface/Cameras/Playback/GetJPEGStream?' .
                                                        'Camera=%s&StartDate=%s&StartTime=%s&EndDate=%s&EndTime=%s' .
                                                        '&ResponseFormat=Text&AuthUser=%s&AuthPass=%s',

                                                    $record->camara->server->ip,
                                                    urlencode($record->camara->nombre),
                                                    $startDate,
                                                    $startTime,
                                                    $endDate,
                                                    $endTime,
                                                    config('services.digifort.user'),
                                                    config('services.digifort.password'),
                                                );
                                            })
                                            ->badge()
                                            ->icon(Heroicon::Play)
                                            ->openUrlInNewTab(),
                                    ])
                                    ->columns(3),
                            ])
                    ])
                    ->columnSpanFull()
            ]);
    }
}
