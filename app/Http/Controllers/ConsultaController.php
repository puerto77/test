<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Zona as Zona;
use App\Models\Log as Log;
use CodeDredd\Soap\Facades\Soap;

class ConsultaController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function consulta(Request $request)
    {
        $zonas = Zona::where('departamento','=', $request->get('departamento'))->
                        where('municipio','=', $request->get('municipio'))->
                        get(['zona','descripcion'])->toJson();

        return $zonas;  
    }

    public function tipoCambio(Request $request)
    {
        
        $response = Soap::baseWsdl('https://www.banguat.gob.gt/variables/ws/TipoCambio.asmx?WSDL')->call('TipoCambioDia');
        $tipoCambio = $response['TipoCambioDiaResult']['CambioDolar']['VarDolar'][0]['referencia'];
        $fecha=$response['TipoCambioDiaResult']['CambioDolar']['VarDolar'][0]['fecha'] ;
        $newLog = new Log();
        $newLog->tipo_cambio = $tipoCambio;
        $newLog->fecha = $fecha;
        $newLog->save();

        return "Tipo de cambio $tipoCambio de $fecha, guardado en Log con éxito";
    }

}
