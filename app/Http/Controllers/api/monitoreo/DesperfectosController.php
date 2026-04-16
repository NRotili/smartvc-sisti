<?php

namespace App\Http\Controllers\api\monitoreo;

use App\Http\Controllers\Controller;
use App\Models\Camara;
use App\Models\DesperfectosCamara;
use App\Models\FallasCamara;
use Illuminate\Http\Request;
use Dedoc\Scramble\Attributes\ExcludeRouteFromDocs;

class DesperfectosController extends Controller
{

    #[ExcludeRouteFromDocs]
    public function store(Request $request)
    {
        $data = parse_ini_string($request->getContent(), true);

        $camera = Camara::where('nombre', $data['name'] ?? null)->first();

        if (!$camera) {
            return response()->json(['msg' => 'Cámara no encontrada'], 404);
        }

        if ($camera->maintenance) {
            return response()->json([
                'msg' => 'Dispositivo con mantenimiento activo.',
            ]);
        }

        // Actualizo estado cámara
        $camera->update([
            'status' => 0,
            'recording' => 0,
        ]);

        $fecha = now()->subMinutes(2);

        $fallaExistente = DesperfectosCamara::where('camara_id', $camera->id)
            ->whereNull('hora_solucion')
            ->first();

        if ($fallaExistente) {
            return response()->json([
                'msg' => 'Ya existe una falla sin resolver para esta cámara.',
            ]);
        }

        $tipo_falla = FallasCamara::where('tipo_falla', 'Sin clasificar')->first();

        $falla = DesperfectosCamara::create([
            'camara_id' => $camera->id,
            'fecha_desperfecto' => $fecha->toDateString(),
            'hora_desperfecto' => $fecha->toTimeString(),
            'descripcion' => 'Sin clasificar',
            'tipo_falla_id' => $tipo_falla->id ?? null,
        ]);

        return response()->json(['msg' => 'Falla registrada correctamente', 'falla_id' => $falla->id], 201);
    }

    #[ExcludeRouteFromDocs]
    public function update(Request $request)
    {
        $data = parse_ini_string($request->getContent(), true);

        $camara = Camara::where('nombre', $data['name'] ?? null)->first();

        if (!$camara) {
            return response()->json(['msg' => 'Cámara no encontrada'], 404);
        }

        if ($camara->maintenance) {
            return response()->json([
                'msg' => 'Dispositivo con mantenimiento activo.',
            ]);
        }

        // Validamos que la falla corresponda a la cámara y esté abierta
        $falla = DesperfectosCamara::where('camara_id', $camara->id)
            ->whereNull('hora_solucion')
            ->first();

        if (!$falla) {
            return response()->json([
                'msg' => 'No hay fallas abiertas para esta cámara.',
            ]);
        }

        $camara->update([
            'status' => 1,
            'recording' => 1,
        ]);

        $falla->update([
            'fecha_solucion' => now()->toDateString(),
            'hora_solucion' => now()->toTimeString(),
        ]);

        return response()->json([
            'msg' => 'Falla cerrada correctamente.',
        ], 200);
    }
}
