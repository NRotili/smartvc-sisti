<?php

namespace App\Filament\Widgets;

use Barryvdh\Debugbar\Facades\Debugbar;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Notifications\Notification;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Ndum\Laravel\Snmp;
use Symfony\Component\ErrorHandler\Debug;

class ElectricidadDatacenterChart extends ApexChartWidget
{
    use HasWidgetShield;
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?int $sort = 51;
    protected ?string $pollingInterval = '60s';

    protected static ?string $chartId = 'electricidadDatacenterChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Electricidad del DataCenter';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */


    protected function colorPorTension(float $tension): string
    {
        return match (true) {

            $tension > 0 => '#22c55e', // normal (verde)
            default    => '#ef4444', // crítico (rojo)
        };
    }

    protected function getOptions(): array
    {
        $snmp = new Snmp();
        try {
            $snmp->newClient(env('SNMP_HOST'), env('SNMP_VERSION'), env('SNMP_COMMUNITY'));
            $corriente = $snmp->getValue(env('SNMP_OID_ELECTRICIDAD_DATACENTER'));
        } catch (\Throwable $th) {
            $corriente = 0;
            $recipient = auth()->user();
            Debugbar::info("Enviando notificación de error SNMP al usuario ID: " . $recipient->id);
            Notification::make()
                ->title('Error de conexión SNMP')
                ->body('No se pudo obtener la corriente del DataCenter. Verifique la conexión SNMP.')
                ->danger()
                ->sendToDatabase($recipient);
            Debugbar::error("Error al obtener la corriente del servidor: " . $th->getMessage());
        }

        if ($corriente == 1) {
            $percentage = 100;
        } else {
            $percentage = 0;
        }

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
            'labels' => [$corriente == 1 ? 'Con energía' : 'Sin energía'],
            'colors' => [
                $this->colorPorTension($corriente),
            ],
        ];
    }
}
