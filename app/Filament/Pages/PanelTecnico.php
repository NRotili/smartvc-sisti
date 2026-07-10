<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TecnicoCamarasRecurrentesChart;
use App\Filament\Widgets\TecnicoFallasAbiertasTable;
use App\Filament\Widgets\TecnicoGrabacionAnomalaWidget;
use App\Filament\Widgets\TecnicoResumenStats;
use App\Filament\Widgets\TecnicoServidoresWidget;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class PanelTecnico extends Page
{
    use HasPageShield;

    protected static ?string $title = 'Panel Técnico';

    protected static UnitEnum|string|null $navigationGroup = 'Monitoreo';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::WrenchScrewdriver;

    protected static ?int $navigationSort = 12;

    protected function getHeaderWidgets(): array
    {
        return [
            TecnicoResumenStats::class,
            TecnicoServidoresWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getFooterWidgets(): array
    {
        return [
            TecnicoGrabacionAnomalaWidget::class,
            TecnicoCamarasRecurrentesChart::class,
            TecnicoFallasAbiertasTable::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 2;
    }
}
