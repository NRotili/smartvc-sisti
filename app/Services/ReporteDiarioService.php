<?php

namespace App\Services;

use App\Models\DesperfectosCamara;
use Carbon\Carbon;
use App\Models\Intervencion;
use App\Models\FallaCamara;
use App\Models\Expediente;
use App\Models\Intervencione;

class ReporteDiarioService
{
    public function generar(): array
    {
        $inicio = Carbon::yesterday()->startOfDay();
        $fin = Carbon::yesterday()->endOfDay();

        return [
            'fecha' => $inicio->format('d/m/Y'),

            'total_intervenciones' =>
            Intervencione::whereBetween('created_at', [$inicio, $fin])->count(),

            'total_fallas' =>
            DesperfectosCamara::whereBetween('fecha_desperfecto', [$inicio, $fin])->count(),

            'total_expedientes' =>
            Expediente::whereBetween('created_at', [$inicio, $fin])->count(),

            'intervenciones_por_categoria' =>
            Intervencione::selectRaw('categoria_id, COUNT(*) as total')
                ->whereBetween('created_at', [$inicio, $fin])
                ->groupBy('categoria_id')
                ->with('categoria')
                ->get(),
        ];
    }
}
