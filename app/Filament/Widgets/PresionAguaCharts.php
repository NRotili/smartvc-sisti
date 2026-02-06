<?php

namespace App\Filament\Widgets;

use App\Models\SensoresPresione;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PresionAguaCharts extends ApexChartWidget
{
    protected static ?string $chartId = 'presionAguaCharts';
    protected ?string $pollingInterval = '60s';
    protected static bool $isLazy = false;

    public ?string $topicId = null;

    protected static ?string $heading = 'Presión de agua';


    protected function getChartId(): string
    {
        if (!$this->topicId) {
            return 'presion-agua-default';
        }

        // Reemplazar / por - en el topicId
        $topicId = str_replace('/', '-', $this->topicId);
        // Eliminar último - si existe
        $topicId = rtrim($topicId, '-');
        return 'presion-agua-topic-' . $topicId;
    }

    public static function canView(): bool
    {
        return auth()->user()->can('View:PresionAguaCharts');
    }

    protected function getHeading(): null|string|Htmlable|View
    {
        if ($this->topicId) {
            $sensor = SensoresPresione::where('topic_id', $this->topicId)->first();
            if ($sensor) {
                return 'Presión de agua: ' . $sensor->nombre;
            } else {
                return 'Presión de agua: Sensor no encontrado';
            }
        }

        return static::$heading; // Fallback al heading estático si no hay topicId
    }

    protected function colorPorPresion(float $presion, float $min, float $max): string
    {
        return match (true) {
            $presion < $min => '#3b82f6', // frío (azul)
            $presion < $max => '#22c55e', // normal (verde)
            default    => '#ef4444', // crítico (rojo)
        };
    }

    protected function getOptions(): array
    {
        if (! $this->topicId) {
            Debugbar::warning('Widget sin topicId');
            return [];
        }

        $sensor = SensoresPresione::where('topic_id', $this->topicId)->first();
        Debugbar::info('Sensor encontrado: ' . ($sensor ? $sensor->nombre : 'No existe sensor'));

        if (! $sensor) {
            Debugbar::warning('No existe sensor para topic: ' . $this->topicId);
            return [];
        }

        $ultimoDato = $sensor->datosPresiones()->latest()->first();
        Debugbar::info('Último dato de presión: ' . ($ultimoDato ? json_encode($ultimoDato->toArray()) : 'No hay datos'));

        if (! $ultimoDato) {
            Debugbar::warning('Sin datos de presión');
            return [];
        }

        $data = json_decode($ultimoDato->message ?? '{}', true);

        if (! isset($data['values']['Presion'])) {
            Debugbar::warning('Datos inválidos: No se encontró "values.Presion" en el message');
            return [];
        }

        $presionMinima = $sensor->presion_minima;
        $presionMaxima = $sensor->presion_maxima;

        $presionActual = round(
            (float) $data['values']['Presion'] / 10,
            2
        );

        $percentage = min(
            max(($presionActual / max($presionMaxima, 1)) * 100, 0),
            100
        );

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
