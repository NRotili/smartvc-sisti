<?php

namespace App\Filament\Widgets;

use App\Models\SensoresPresione;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PresionAguaCharts extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'presionAguaCharts';

    public ?int $topicId = null;

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Presión de agua';

    protected function colorPorPresion(float $presion, float $presionMinima, float $presionMaxima): string
    {
        return match (true) {
            $presion < $presionMinima => '#3b82f6', // baja (azul)
            $presion < $presionMaxima => '#22c55e', // normal (verde)
            default    => '#ef4444', // alta (rojo)
        };
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {

    
        //obtener sensores de presion y sus valores
        //Buscar por topic_id
        $sensor = SensoresPresione::where('topic_id', $this->topicId)->first();
        $presionMinima = $sensor->presion_minima;
        $presionMaxima = $sensor->presion_maxima;
        //obtener el campo "message" del ultimo dato de presion registrado
        $ultimoDato = $sensor->datosPresiones()->latest()->first();
        $mensaje = json_decode($ultimoDato->message, true);
        $presionActual= round($mensaje['values']['Presion'] / 10, 2);
        $percentage = min(max((($presionActual) / ($presionMaxima + $presionMinima)) * 100, 0), 100);

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
            'labels' => [$presionActual . ' kg'], 
            'colors' => [
                $this->colorPorPresion($presionActual, $presionMinima, $presionMaxima),
            ],
        ];
    }
}
