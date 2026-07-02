<?php

namespace App\Filament\Resources\DesperfectosCamaras\Pages;

use App\Filament\Resources\DesperfectosCamaras\DesperfectosCamaraResource;
use App\Models\Camara;
use App\Models\DesperfectosCamara;
use App\Models\Pdf;
use Carbon\Carbon;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListDesperfectosCamaras extends ListRecords
{
    protected static string $resource = DesperfectosCamaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ActionGroup::make([
                Action::make('reporte_disponibilidad')
                    ->label('Reporte de Disponibilidad')
                    ->icon('heroicon-o-chart-bar')
                    ->modalHeading('Reporte de Disponibilidad de Cámaras')
                    ->modalSubmitActionLabel('Generar PDF')
                    ->form([
                        DateRangePicker::make('periodo')
                            ->label('Período')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $fechaInicio = Carbon::parse($data['periodo']['start'])->startOfDay();
                        $fechaFin    = Carbon::parse($data['periodo']['end'])->endOfDay();
                        $minutosTotal = $fechaInicio->diffInMinutes($fechaFin);

                        $formatMinutos = function (float $min): string {
                            if ($min < 60) {
                                return round($min) . ' min';
                            }
                            $h = floor($min / 60);
                            $m = round(fmod($min, 60));
                            if ($h < 24) {
                                return "{$h}h {$m}m";
                            }
                            $d = floor($h / 24);
                            $hr = $h % 24;
                            return "{$d}d {$hr}h";
                        };

                        $camaras = Camara::orderBy('nombre')->get();

                        $filas = [];
                        $totalFallasGlobal   = 0;
                        $totalMinutosCaido   = 0;

                        foreach ($camaras as $camara) {
                            $fallas = DesperfectosCamara::where('camara_id', $camara->id)
                                ->whereBetween('fecha_desperfecto', [
                                    $fechaInicio->toDateString(),
                                    $fechaFin->toDateString(),
                                ])
                                ->get();

                            $minutosCaido     = 0;
                            $minutosResolucion = 0;
                            $fallasResueltas  = 0;

                            foreach ($fallas as $falla) {
                                $inicio = Carbon::createFromFormat(
                                    'Y-m-d H:i:s',
                                    "{$falla->fecha_desperfecto} {$falla->hora_desperfecto}"
                                );

                                if ($falla->fecha_solucion && $falla->hora_solucion) {
                                    $fin      = Carbon::createFromFormat(
                                        'Y-m-d H:i:s',
                                        "{$falla->fecha_solucion} {$falla->hora_solucion}"
                                    );
                                    $duracion  = $inicio->diffInMinutes($fin);
                                    $minutosCaido      += $duracion;
                                    $minutosResolucion += $duracion;
                                    $fallasResueltas++;
                                } else {
                                    $minutosCaido += $inicio->diffInMinutes($fechaFin);
                                }
                            }

                            $uptime         = $minutosTotal > 0
                                ? max(0, (($minutosTotal - $minutosCaido) / $minutosTotal) * 100)
                                : 100.0;
                            $promResolucion = $fallasResueltas > 0
                                ? $minutosResolucion / $fallasResueltas
                                : null;

                            $totalFallasGlobal += $fallas->count();
                            $totalMinutosCaido += $minutosCaido;

                            $filas[] = [
                                'nombre'         => $camara->nombre,
                                'fallas'         => $fallas->count(),
                                'minutos_caido'  => $minutosCaido,
                                'prom_resolucion'=> $promResolucion,
                                'uptime'         => $uptime,
                            ];
                        }

                        // Ordenar por cantidad de fallas desc, luego alfabético
                        usort($filas, function ($a, $b) {
                            if ($b['fallas'] !== $a['fallas']) {
                                return $b['fallas'] <=> $a['fallas'];
                            }
                            return strcmp($a['nombre'], $b['nombre']);
                        });

                        $totalPosible = $minutosTotal * $camaras->count();
                        $uptimeGlobal = $totalPosible > 0
                            ? max(0, (($totalPosible - $totalMinutosCaido) / $totalPosible) * 100)
                            : 100.0;

                        $pdf = new Pdf();
                        $pdf->AliasNbPages();
                        $pdf->AddPage();

                        // Título
                        $pdf->SetFont('Arial', 'BU', 11);
                        $pdf->Cell(177, 5, utf8_decode('Reporte de Disponibilidad de Cámaras'), 0, 0, 'C');
                        $pdf->Ln(8);

                        // Período
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell(177, 5, utf8_decode(
                            'Período: ' . $fechaInicio->format('d/m/Y') . ' al ' . $fechaFin->format('d/m/Y')
                        ), 0, 0, 'C');
                        $pdf->Ln(9);

                        // Resumen global
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(59, 5, utf8_decode('Total de cámaras: ' . $camaras->count()), 0, 0, 'L');
                        $pdf->Cell(59, 5, utf8_decode('Total de fallas: ' . $totalFallasGlobal), 0, 0, 'C');
                        $pdf->Cell(59, 5, utf8_decode('Disponibilidad global: ' . number_format($uptimeGlobal, 2) . '%'), 0, 0, 'R');
                        $pdf->Ln(10);

                        // Encabezado de tabla
                        $pdf->SetFont('Arial', 'B', 7);
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->Cell(7,  5, '',                            0, 0, 'C', false);
                        $pdf->Cell(55, 5, utf8_decode('Cámara'),         0, 0, 'C', true);
                        $pdf->Cell(18, 5, 'Fallas',                      0, 0, 'C', true);
                        $pdf->Cell(37, 5, utf8_decode('Tiempo caído'),   0, 0, 'C', true);
                        $pdf->Cell(40, 5, utf8_decode('Prom. resolución'),0, 0, 'C', true);
                        $pdf->Cell(20, 5, utf8_decode('Disponib.'),      0, 0, 'C', true);
                        $pdf->Ln(6);

                        // Filas
                        $pdf->SetFont('Arial', '', 7);
                        $pdf->SetTextColor(0, 0, 0);

                        foreach ($filas as $fila) {
                            $pdf->Cell(7,  4, '',                                                                     0,   0, 'C', false);
                            $pdf->Cell(55, 4, utf8_decode($fila['nombre']),                                           'T', 0, 'L', false);
                            $pdf->Cell(18, 4, $fila['fallas'],                                                        'T', 0, 'C', false);
                            $pdf->Cell(37, 4, $fila['minutos_caido'] > 0 ? $formatMinutos($fila['minutos_caido']) : '-', 'T', 0, 'C', false);
                            $pdf->Cell(40, 4, $fila['prom_resolucion'] !== null ? $formatMinutos($fila['prom_resolucion']) : '-', 'T', 0, 'C', false);
                            $pdf->Cell(20, 4, number_format($fila['uptime'], 2) . '%',                                'T', 0, 'C', false);
                            $pdf->Ln(4);
                        }

                        return response()->streamDownload(
                            fn() => print($pdf->Output('', 'S')),
                            'disponibilidad_' . Carbon::now()->timestamp . '.pdf'
                        );
                    })
                    ->authorize(fn() => Auth::user()->can('ReporteDisponibilidad:Reportes')),
            ])
                ->label('Exportar')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary'),
        ];
    }
}
