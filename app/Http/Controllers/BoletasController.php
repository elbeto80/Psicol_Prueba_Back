<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modelos\BoletaModel;
use App\Modelos\CompradoresModel;

use \Validator;
use \Exception;
use \DB;

class BoletasController extends Controller
{
    public function guardarBoleta(Request $request) {
        try {
            $values = $request->input('data');

            // VALIDACIONES DE CAMPOS OBLIGATORIOS POR LARAVEL
            $rules = [
                'evento' => 'required',
                'fecha'  => 'required|date',
            ];
            $input = [
                'evento'   => $values['evento'],
                'fecha'    => $values['fecha'],
            ];

            $validate = Validator::make($input, $rules);
            if($validate->fails()){
                $messages = $validate->messages()->all();
                return ['error' => 1, 'info' => $messages];
            }

            // SI TRAE idBoleta MODIFICA SINO CREA NUEVO
            if( !$values['idBoleta'] ){
                $boleta = new BoletaModel();
            } else {
                $boleta = BoletaModel::findOrFail($values['idBoleta']);
            }
            $boleta->evento    = $values['evento'];
            $boleta->fecha     = $values['fecha'];
            $boleta->comprador = ($values['comprador'] ? $values['comprador'] : null);

            if(!$boleta->save()){
                DB::rollback();
                return ['error' => 1, 'info' => 'No ha sido posible Guardar boleta.'];
            }

            return ['error' => 0,'info'   => 'Boleta guardado correctamente'];

        } catch(Exception $e) {
            error_log($e,0);
            return ['error' => 1,'info'=> (string)$e];
        }
    }

    public function getBoletas(Request $request) {
        try {
            $values = $request->input();

            $boleta    = new BoletaModel();
            $comprador = new CompradoresModel();
            $BO = $boleta->getTable();
            $CO = $comprador->getTable();
            if( !isset($values['verDisponibles']) ) { $values['verDisponibles'] = 0; }

            $boletas = $boleta->select("$BO.id","$BO.evento","$BO.fecha","$BO.comprador","$CO.nombre","$CO.apellido","$CO.id as idComprador")
            ->leftJoin("$CO", "$BO.comprador", "$CO.id")
            ->where(function($query) use ($values, $BO, $CO) {
                if( $values['verDisponibles'] == 1) {
                    $query->whereNull("$CO.id");
                }
                if( $values['verDisponibles'] == 2) {
                    $query->whereNotNull("$CO.id");
                }
            })
            ->orderBy("$BO.evento")
            ->get();

            $response = [
                'boletas'=> $boletas
            ];

            return ['error' => 0, 'info' => $response];
        } catch(Exception $e) {
            error_log($e,0);
            return ['error' => 1,'info'=> (string)$e];
        }
    }

    public function borrarBoleta(Request $request) {
        try {
            $comprador = BoletaModel::findOrFail($request->idComprador);
            if(!$comprador->delete()){
                DB::rollback();
                return ['error' => 1, 'info' => 'No ha sido posible eliminar boleta.'];
            }

            return ['error' => 0, 'info' => 'Boleta borrado correctamente'];
        } catch(Exception $e) {
            error_log($e,0);
            return ['error' => 1,'info'=> (string)$e];
        }
    }
}
