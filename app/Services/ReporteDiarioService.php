<?php

namespace App\Services;

use App\Models\DesperfectosCamara;
use App\Models\Expediente;
use App\Models\Intervencione;
use Carbon\Carbon;

class ReporteDiarioService
{
    public function generar(?Carbon $dia = null): array
    {
        $dia = $dia ?? Carbon::yesterday();
        $inicio = $dia->copy()->startOfDay();
        $fin = $dia->copy()->endOfDay();

        return [
            'fecha' => $inicio->format('d/m/Y'),

            'total_intervenciones' => Intervencione::whereBetween('created_at', [$inicio, $fin])->count(),

            'total_fallas' => DesperfectosCamara::whereBetween('fecha_desperfecto', [$inicio, $fin])->count(),

            'total_expedientes' => Expediente::whereBetween('created_at', [$inicio, $fin])->count(),

            'intervenciones_por_categoria' => Intervencione::selectRaw('categoria_id, COUNT(*) as total')
                ->whereBetween('created_at', [$inicio, $fin])
                ->groupBy('categoria_id')
                ->with('categoria')
                ->get(),
        ];
    }
}
