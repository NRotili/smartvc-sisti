<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ElectricidadDatacenterChart;
use App\Filament\Widgets\FallasChart;
use App\Filament\Widgets\IntervencioneChart;
use App\Filament\Widgets\MonitoreoStats;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Models\SensoresPresione;
use App\Filament\Widgets\PresionAguaChart;
use App\Filament\Widgets\PresionAguaCharts;
use App\Filament\Widgets\ServerStats;
use App\Filament\Widgets\TemperaturaServerChart;
use Barryvdh\Debugbar\Facades\Debugbar;
use Barryvdh\Debugbar\Twig\Extension\Debug;

class Dashboard extends BaseDashboard
{
    // protected string $view = 'filament.pages.dashboard';
    protected static ?string $title = 'Dashboard';


    public function getColumns(): int|array
    {
        Debugbar::info('Cargando columnas del dashboard personalizado');
        return parent::getColumns();
    }

    public function getWidgets(): array
    {
        
        // Generate dynamic widgets based on SensoresPresione topics
        $dynamicWidgets = SensoresPresione::pluck('topic_id')->map(
            fn($topic) => PresionAguaCharts::make(['topicId' => $topic])
        )->toArray();

        // Merge with static widgets
        return array_merge($dynamicWidgets, [
            MonitoreoStats::class,
            ServerStats::class,
            IntervencioneChart::class,
            FallasChart::class,
            ElectricidadDatacenterChart::class,
            TemperaturaServerChart::class,
        ]);
       
    }
}
