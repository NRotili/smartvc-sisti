<?php

use App\Models\Camara;
use App\Models\DesperfectosCamara;
use App\Models\Expediente;
use App\Models\FallasCamara;
use App\Models\Intervencione;
use App\Models\Pdf;
use App\Models\SensoresPresione;
use App\Notifications\AlertaNotification;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

use function Symfony\Component\Translation\t;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/get-updates', function () {

    return Telegram::getUpdates();
});

Route::get('/send-message', function () {
    Telegram::sendMessage([
        'chat_id' => 'reemplazar con id de chat de service.php ',
        'text' => 'Hello World'
    ]);
});



Route::get('telegram-agua-presion', function () {

    $sensoresPresion = SensoresPresione::all();
    foreach ($sensoresPresion as $sensor) {

        $data = $sensor->datosPresiones()->latest()->first();
        $presion = json_decode($data->message, true);
        $valorDePresion = round($presion['values']['Presion'] / 10, 2);

        if ($presion && ($valorDePresion <= $sensor->presion_minima || $valorDePresion >= $sensor->presion_maxima)) {

            Telegram::sendMessage([
                'chat_id' => config('services.telegram.canal_agua_presion'),
                'text' => "⚠ *ALERTA DE PRESIÓN* ⚠ \n\n*Sensor:* $sensor->nombre \n*Presión:* " . $valorDePresion,
                'parse_mode' => 'Markdown'
            ]);
        }
    }
});

// app/Http/routes.php | app/routes/web.php
//Ruta que recibe por post 2 fechas y exporta pdf
Route::get('/exportar-pdf-concejo', function () {
    $dateinConcejo = request('start');
    $dateoutConcejo = request('end');

    return response()->json([
        'message' => "PDF generado para el período: $dateinConcejo - $dateoutConcejo"
    ]);

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
        $pdf->Cell(20, 4, $flaw->fecha_desperfecto, 'T', 0, 'C', 0);
        $pdf->Cell(10, 4, $flaw->hora_desperfecto, 'T', 0, 'C', 0);
        $pdf->Cell(55, 4, $flaw->camara->nombre, 'T', 0, 'C', 0);
        $pdf->Cell(55, 4, $flaw->fallaCamara->tipo_falla, 'T', 0, 'C', 0);
        $pdf->Cell(20, 4, $flaw->fecha_solucion, 'T', 0, 'C', 0);
        $pdf->Cell(20, 4, $flaw->hora_solucion, 'T', 0, 'C', 0);
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
    $pdf->Cell(10, 5, "Hora", 0, 0, 'C', true);
    $pdf->Cell(140, 5, utf8_decode("Intervención"), 0, 0, 'C', true);
    $pdf->Ln(6);
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetTextColor(0, 0, 0);
    foreach ($interventions as $intervention) {
        $pdf->Cell(7, 4, "", 0, 0, 'C', 0);
        $pdf->Cell(20, 4, $intervention->fecha_hora, 'T', 0, 'C', 0);
        $pdf->MultiCell(140, 4, utf8_decode($intervention->descripcion), 'T', 'J', false);
        $pdf->Ln(1);
    }

    //Creo cámaras
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'BU', 10);
    $pdf->Cell(177, 5, utf8_decode("Cámaras y su estado al día de la fecha"), 0, 0, 'C');
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(50, 5, "", 0, 0, 'C', 0);
    $pdf->Cell(40, 5, "Nombre", 0, 0, 'C', true);
    $pdf->Cell(40, 5, "Descripción", 0, 0, 'C', true);
    $pdf->Cell(40, 5, "Estado", 0, 0, 'C', true);
    $pdf->Ln(6);
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetTextColor(0, 0, 0);
    foreach ($cameras as $camera) {
        $pdf->Cell(50, 4, "", 0, 0, 'C', 0);
        $pdf->Cell(40, 4, $camera->nombre, 'T', 0, 'C', 0);
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

    $pdf->Output(Carbon::now()->timestamp . '.pdf', 'D');
    exit;

    // Aquí puedes agregar la lógica para generar el PDF con los datos entre las fechas $start y $end

    return response()->json([
        'message' => "PDF generado para el período: $start - $end"
    ]);
})->name('exportar.pdf.concejo');
