<?php

namespace App\Console\Commands;

use App\Mail\ReporteMensualOperadoresMail;
use App\Services\ReporteMensualOperadoresService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarReporteMensualOperadores extends Command
{
    protected $signature = 'reporte:mensual-operadores {--mes= : Mes a reportar en formato YYYY-MM (por defecto, el mes anterior)}';

    protected $description = 'Envía por email las estadísticas mensuales de los operadores de monitoreo';

    public function handle(ReporteMensualOperadoresService $service): void
    {
        $mes = $this->option('mes')
            ? Carbon::createFromFormat('Y-m', $this->option('mes'))
            : null;

        $data = $service->generar($mes);

        Mail::to(config('reportes.destinatarios_operadores'))
            ->queue(new ReporteMensualOperadoresMail($data));

        $this->info("Reporte mensual de operadores ({$data['mes']}) enviado");
    }
}
