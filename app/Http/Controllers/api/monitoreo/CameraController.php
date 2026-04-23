<?php

namespace App\Http\Controllers\api\monitoreo;

use App\Http\Controllers\Controller;
use App\Models\Camara;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Dedoc\Scramble\Attributes\ExcludeRouteFromDocs;

class CameraController extends Controller
{
    #[ExcludeRouteFromDocs]
    public function index()
    {

        $camaras = Camara::select('nombre', 'lat', 'lng', 'tipo_id', 'status')->orderBy('nombre')->get();
        return response()->json(['data' => $camaras, 'message' => 'Camaras obtenidas correctamente'], 200);
    }

    public function getFueraServicio()
    {
        try {
            $camaras = Camara::select('id', 'nombre')
                ->where('status', 0)
                ->orderBy('nombre')
                ->get(); 

            $camarasFueraServicio = [];

            foreach ($camaras as $camara) {
                $fechaFueraServicio = $camara->desperfectos()
                    ->orderBy('created_at', 'desc')
                    ->first();

                //Verificar que exista el desperfecto antes de parsear
                $tiempo = $fechaFueraServicio
                    ? Carbon::parse($fechaFueraServicio->fecha_desperfecto . ' ' . $fechaFueraServicio->hora_desperfecto)
                    : null;

                $camarasFueraServicio[] = [
                    'nombre'       => $camara->nombre,
                    'sinFuncionar' => $tiempo ? $tiempo->diffForHumans() : 'Desconocido',
                ];
            }

            //Se devuelve un JSON con la lista de cámaras fuera de servicio, incluyendo el nombre de cada cámara y el tiempo transcurrido desde que dejaron de funcionar. Si no se puede determinar el tiempo, se muestra "Desconocido". 
            return response()->json([
                'data'    => $camarasFueraServicio,
                'message' => 'OK'
            ], 200);
        } catch (\Exception $e) {
            //Si ocurre un error al obtener las cámaras fuera de servicio, se devuelve un mensaje de error con el detalle del mismo.
            return response()->json([
                'message' => 'Error al obtener las cámaras fuera de servicio',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
