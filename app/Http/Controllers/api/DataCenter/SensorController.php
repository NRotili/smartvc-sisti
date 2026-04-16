<?php

namespace App\Http\Controllers\api\DataCenter;

use App\Http\Controllers\Controller;
use Ndum\Laravel\Snmp;


class SensorController extends Controller
{
    public function getTemperatura()
    {

        $snmp= new Snmp();
        try {
            $snmp->newClient(env('SNMP_HOST'), env('SNMP_VERSION'), env('SNMP_COMMUNITY'));
            $temp = $snmp->getValue(env('SNMP_OID_TEMP_SERVER'));
            $temp = round($temp / 10, 2);

        } catch (\Throwable $th) {
            $temp = 0;
        }

        //Se devuelve la temperatura expresado en grados Celsius con dos decimales. Si ocurre un error al obtener la temperatura, se devuelve 0.
        return response()->json(['temperatura' => $temp], 200);
    }
}
