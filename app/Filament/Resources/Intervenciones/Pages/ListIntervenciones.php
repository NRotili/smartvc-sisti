<?php

namespace App\Filament\Resources\Intervenciones\Pages;

use App\Filament\Resources\Intervenciones\IntervencioneResource;
use App\Models\Camara;
use App\Models\DesperfectosCamara;
use App\Models\Expediente;
use App\Models\Intervencione;
use App\Models\Pdf;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\ErrorHandler\Debug;

class ListIntervenciones extends ListRecords
{
    protected static string $resource = IntervencioneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Crear Intervención'),
            ActionGroup::make([

                Action::make('concejo')
                    ->label('PDF para Concejo')
                    ->icon('heroicon-o-document')
                    ->modalHeading('Exportar PDF para Concejo')
                    ->modalSubmitActionLabel('Exportar')
                    ->form([
                        DateRangePicker::make('concejo_periodo')
                            ->label('Período')
                            ->required(),
                    ])
                    ->action(function (array $data) {

                        $dateinConcejo  = $data['concejo_periodo']['start'];
                        $dateoutConcejo = $data['concejo_periodo']['end'];

                        $flaws = DesperfectosCamara::whereBetween('fecha_desperfecto', [$dateinConcejo, $dateoutConcejo])
                            ->orderBy('fecha_desperfecto', 'ASC')
                            ->orderBy('hora_desperfecto', 'ASC')
                            ->get();

                        $interventions = Intervencione::whereBetween('fecha_hora', [$dateinConcejo, $dateoutConcejo])
                            ->orderBy('fecha_hora', 'asc')
                            ->get();

                        $cameras = Camara::where('publicada', 1)
                            ->orderBy('nombre', 'ASC')
                            ->get();

                        $files = Expediente::whereBetween('fecha_ingreso', [$dateinConcejo, $dateoutConcejo])
                            ->orderBy('fecha_ingreso', 'ASC')
                            ->get();

                        $pdf = new Pdf();
                        // Creo fallas
                        $pdf->AliasNbPages();
                        $pdf->AddPage();
                        $pdf->SetFont('Arial', 'BU', 10);
                        $pdf->Cell(177, 5, utf8_decode("Desperfectos y fallas"), 0, 0, 'C');
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->Ln(10);
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(7, 5, "", 0, 0, 'C', 0);
                        $pdf->Cell(20, 5, utf8_decode("Día"), 0, 0, 'C', true);
                        $pdf->Cell(10, 5, "Hora", 0, 0, 'C', true);
                        $pdf->Cell(55, 5, utf8_decode("Cámara"), 0, 0, 'C', true);
                        $pdf->Cell(55, 5, utf8_decode("Descripción"), 0, 0, 'C', true);
                        $pdf->Cell(20, 5, utf8_decode("Día sol."), 0, 0, 'C', true);
                        $pdf->Cell(20, 5, utf8_decode("Hora sol."), 0, 0, 'C', true);
                        $pdf->Ln(6);
                        $pdf->SetFont('Arial', '', 7);
                        $pdf->SetTextColor(0, 0, 0);

                        foreach ($flaws as $flaw) {
                            $pdf->Cell(7, 4, "", 0, 0, 'C', 0);
                            $pdf->Cell(20, 4, $flaw->fecha_desperfecto ? Carbon::parse($flaw->fecha_desperfecto)->format('d/m/Y') : '', 'T', 0, 'C', 0);
                            $pdf->Cell(10, 4, $flaw->hora_desperfecto, 'T', 0, 'C', 0);
                            $pdf->Cell(55, 4, $flaw->camara->nombre, 'T', 0, 'C', 0);
                            $pdf->Cell(55, 4, $flaw->fallaCamara?->tipo_falla ?? 'N/A', 'T', 0, 'C', 0);
                            $pdf->Cell(20, 4, $flaw->fecha_solucion ? Carbon::parse($flaw->fecha_solucion)->format('d/m/Y') : '', 'T', 0, 'C', 0);
                            $pdf->Cell(20, 4, $flaw->hora_solucion ?? '', 'T', 0, 'C', 0);
                            $pdf->Ln(4);
                        }

                        //Creo intervenciones
                        $pdf->AddPage();
                        $pdf->SetFont('Arial', 'BU', 10);
                        $pdf->Cell(177, 5, utf8_decode("Intervenciones y solicitudes"), 0, 0, 'C');
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->Ln(10);
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(7, 5, "", 0, 0, 'C', 0);
                        $pdf->Cell(20, 5, "Fecha", 0, 0, 'C', true);
                        $pdf->Cell(5, 5, "", 0, 0, 'C', true);
                        $pdf->Cell(140, 5, utf8_decode("Intervención"), 0, 0, 'C', true);
                        $pdf->Ln(6);
                        $pdf->SetFont('Arial', '', 7);
                        $pdf->SetTextColor(0, 0, 0);
                        foreach ($interventions as $intervention) {
                            $pdf->Cell(7, 4, "", 0, 0, 'C', 0);
                            $pdf->Cell(20, 4, Carbon::parse($intervention->fecha_hora)->format('d/m/Y H:i'), 'T', 0, 'C', 0);
                            $pdf->Cell(5, 4, "", 'T', 0, 'C', 0);

                            //Descripción es html y multicell
                            $pdf->MultiCell(140, 4, utf8_decode(strip_tags($intervention->descripcion)), 'T', 'J', false);
                            $pdf->Ln(1);
                        }

                        //Creo cámaras
                        $pdf->AddPage();
                        $pdf->SetFont('Arial', 'BU', 10);
                        $pdf->Cell(177, 5, utf8_decode("Cámaras y su estado al día de la fecha"), 0, 0, 'C');
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->Ln(10);
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(15, 5, "", 0, 0, 'C', 0);
                        $pdf->Cell(60, 5, "Nombre", 0, 0, 'C', true);
                        $pdf->Cell(60, 5, utf8_decode("Descripción"), 0, 0, 'C', true);
                        $pdf->Cell(40, 5, "Estado", 0, 0, 'C', true);
                        $pdf->Ln(6);
                        $pdf->SetFont('Arial', '', 7);
                        $pdf->SetTextColor(0, 0, 0);
                        foreach ($cameras as $camera) {
                            $pdf->Cell(15, 4, "", 0, 0, 'C', 0);
                            $pdf->Cell(60, 4, $camera->nombre, 'T', 0, 'C', 0);
                            $pdf->Cell(60, 4, utf8_decode($camera->descripcion ?? ''), 'T', 0, 'C', 0);
                            $pdf->Cell(40, 4, ($camera->status == 1 ? 'Funcionando' : 'Fuera de servicio'), 'T', 0, 'C', 0);
                            $pdf->Ln(4);
                        }

                        // Creo solicitudes de registros fílmicos
                        $pdf->AddPage();
                        $pdf->SetFont('Arial', 'BU', 10);
                        $pdf->Cell(177, 5, utf8_decode("Reporte - Solicitud de registros fílmicos"), 0, 0, 'C');
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->Ln(10);
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(2, 5, "", 0, 0, 'C', 0);
                        $pdf->Cell(30, 5, utf8_decode("Ingreso"), 0, 0, 'C', true);
                        $pdf->Cell(30, 5, "Solicitante", 0, 0, 'C', true);
                        $pdf->Cell(30, 5, utf8_decode("Fecha grab."), 0, 0, 'C', true);
                        $pdf->Cell(30, 5, utf8_decode("Rango horario"), 0, 0, 'C', true);
                        $pdf->Cell(40, 5, utf8_decode("Adjunto"), 0, 0, 'C', true);
                        $pdf->Cell(30, 5, utf8_decode("Respondido"), 0, 0, 'C', true);
                        //$pdf->Cell(140,5,utf8_decode("Intervención"),0,0,'C', true);
                        $pdf->Ln(6);
                        $pdf->SetFont('Arial', '', 7);
                        $pdf->SetTextColor(0, 0, 0);
                        foreach ($files as $file) {
                            $pdf->Cell(2, 4, "", 0, 0, 'C', 0);
                            $pdf->Cell(30, 4, $file->fecha_ingreso, 'T', 0, 'C', 0);
                            $pdf->Cell(30, 4, $file->iniciadorExpediente->nombre, 'T', 0, 'C', 0);
                            $pdf->Cell(30, 4, $file->fecha_hora_inicio_exportancion, 'T', 0, 'C', 0);
                            $pdf->Cell(30, 4, $file->fecha_hora_fin_exportacion, 'T', 0, 'C', 0);
                            $pdf->Cell(40, 4, utf8_decode($file->material_adjunto), 'T', 0, 'C', 0);
                            $pdf->Cell(30, 4, $file->fecha_entrega, 'T', 0, 'C', 0);
                            $pdf->Ln(4);
                        }

                        return response()->streamDownload(
                            fn() => print($pdf->Output('', 'S')),
                            'concejo_' . Carbon::now()->timestamp . '.pdf'
                        );
                    })
                    ->authorize(fn() => Auth::user()->can('PdfConcejo:Reportes')),
            ])
                ->label('Exportar')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
        ];
    }
}
