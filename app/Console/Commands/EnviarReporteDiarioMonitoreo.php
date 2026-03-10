<?php

namespace App\Console\Commands;

use App\Mail\ReporteDiarioMailMonitoreo;
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
    protected $signature = 'reporte:diario';

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
        $data = $service->generar();

        Mail::to(config('reportes.destinatarios'))
            ->queue(new ReporteDiarioMailMonitoreo($data));

        $this->info('Reporte diario enviado');
    }
}
