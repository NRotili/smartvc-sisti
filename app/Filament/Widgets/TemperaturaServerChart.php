<?php

namespace App\Filament\Widgets;

use Barryvdh\Debugbar\Facades\Debugbar;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\Assets\Js;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Ndum\Laravel\Snmp;

class TemperaturaServerChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    use HasWidgetShield;
    protected static ?string $chartId = 'temperaturaServerChart';
    protected static ?int $sort = 50;


    protected ?string $pollingInterval = '60s';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Temperatura del DataCenter';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */


    protected function colorPorTemperatura(float $temp): string
    {
        return match (true) {
            $temp < 16 => '#3b82f6', // frío (azul)
            $temp < 26 => '#22c55e', // normal (verde)
            default    => '#ef4444', // crítico (rojo)
        };
    }


    protected function getOptions(): array
    {

        $snmp= new Snmp();
        try {
            $snmp->newClient(env('SNMP_HOST'), env('SNMP_VERSION'), env('SNMP_COMMUNITY'));
            $temp = $snmp->getValue(env('SNMP_OID_TEMP_SERVER'));
            $temp = round($temp / 10, 2);

        } catch (\Throwable $th) {
            $temp = 0;
            Debugbar::error("Error al obtener la temperatura del servidor: " . $th->getMessage());
        }

        $max = 42;
        $percentage = min(max(($temp / $max) * 100, 0), 100);

        return [
            'series' => [$percentage],
            'chart' => [
                'type' => 'radialBar',
                'offsetY' => -20,
                'sparkline' => [
                    'enabled' => true,
                ],
            ],
            'plotOptions' => [
                'radialBar' => [
                    'startAngle' => -90,
                    'endAngle' => 90,
                    'track' => [
                        'background' => '#ffffff00',
                        'strokeWidth' => '97%',
                        'margin' => 5,
                        'dropShadow' => [
                            'enabled' => true,
                            'top' => 2,
                            'left' => 0,
                            'color' => '#444',
                            'blur' => 2,
                            'opacity' => 1,
                        ],
                    ],
                    'dataLabels' => [
                        'name' => [
                            'show' => true,
                        ],
                        'value' => [
                            'show' => false,
                            'offsetY' => -2,
                            'fontSize' => '22px',
                        ],
                    ]
                ]
            ],
            'grid' => [
                'padding' => [
                    'top' => -10,
                ]
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'type' => 'radial',
                    'shade' => 'light',
                    'shadeIntensity' => 0.4,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100],
                ],
            ],
            'stroke' => [
                'dashArray' => 4, 
            ],
            'labels' => [$temp . ' °C'], 
            'colors' => [
                $this->colorPorTemperatura($temp),
            ],
        ];
    }
}
