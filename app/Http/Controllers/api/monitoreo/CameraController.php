<?php

namespace App\Http\Controllers\api\monitoreo;

use App\Http\Controllers\Controller;
use App\Models\Camara;
use Illuminate\Http\Request;

class CameraController extends Controller
{
    //index
    public function index(){

        $camaras = Camara::select('nombre', 'lat', 'lng', 'tipo_id', 'status')->orderBy('nombre')->get();
        return response()->json(['data' => $camaras, 'message' => 'Camaras obtenidas correctamente'], 200);
    }


}
