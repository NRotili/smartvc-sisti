<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Support\Icons\Heroicon;

class MapaCamaras extends Page
{

    use HasPageShield;
    protected static ?string $title = 'Mapa de Cámaras';
    protected ?string $heading = '';
    protected static UnitEnum|string|null $navigationGroup = 'Monitoreo';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Map;
    // protected string $view = 'filament.pages.mapa-camaras';
    protected static ?int $navigationSort = 6;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Livewire\MapaCamaras::class,
        ];
    }
}
