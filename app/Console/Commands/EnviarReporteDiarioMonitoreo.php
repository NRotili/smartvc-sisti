<?php

namespace App\Console\Commands;

use App\Mail\ReporteDiarioMailMonitoreo;
use App\Models\SuscripcionReporte;
use App\Services\ReporteDiarioService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarReporteDiarioMonitoreo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reporte:diario {--fecha= : Día a reportar en formato YYYY-MM-DD (por defecto, ayer)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía el reporte diario del centro de monitoreo';

    /**
     * Execute the console command.
     */
    public function handle(ReporteDiarioService $service)
    {
        $dia = $this->option('fecha')
            ? \Carbon\Carbon::createFromFormat('Y-m-d', $this->option('fecha'))
            : null;

        $data = $service->generar($dia);

        Mail::bcc(SuscripcionReporte::destinatarios('diario', config('reportes.destinatarios')))
            ->queue(new ReporteDiarioMailMonitoreo($data));

        $this->info("Reporte diario ({$data['fecha']}) enviado");
    }
}
