<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modelos\CompradoresModel;

use \Validator;
use \Exception;
use \DB;

class CompradoresController extends Controller
{
    public function guardarComprador(Request $request) {
        try {
            $values = $request->input('data');

            // VALIDACIONES DE CAMPOS OBLIGATORIOS POR LARAVEL
            $rules = [
                'cedula'   => 'required',
                'nombre'   => 'required',
                'apellido' => 'required',
                'telefono' => 'required',
                'email'    => 'required|email',
            ];
            $input = [
                'cedula'   => $values['cedula'],
                'nombre'   => $values['nombre'],
                'apellido' => $values['apellido'],
                'telefono' => $values['telefono'],
                'email'    => $values['email'],
            ];

            $validate = Validator::make($input, $rules);
            if($validate->fails()){
                $messages = $validate->messages()->all();
                return ['error' => 1, 'info'   => $messages];
            }

            // SI TRAE idTienda MODIFICA SINO CREA NUEVO
            if( !$values['idComprador'] ){
                $comprador = new CompradoresModel();
            } else {
                $comprador = CompradoresModel::findOrFail($values['idComprador']);
            }
            $comprador->cedula   = $values['cedula'];
            $comprador->nombre   = $values['nombre'];
            $comprador->apellido = $values['apellido'];
            $comprador->telefono = $values['telefono'];
            $comprador->email    = $values['email'];

            if(!$comprador->save()){
                DB::rollback();
                return ['error' => 1, 'info' => 'No ha sido posible Guardar tienda.'];
            }

            return ['error' => 0,'info'   => 'Comprador guardado correctamente'];

        } catch(Exception $e) {
            error_log($e,0);
            return ['error' => 1,'info'=> (string)$e];
        }
    }

    public function getCompradores(Request $request) {
        try {
            $values = $request->input();

            $comprador = new CompradoresModel();
            $CO = $comprador->getTable();

            $compradores = $comprador->select("$CO.id","$CO.cedula","$CO.nombre","$CO.apellido","$CO.telefono","$CO.email")
            ->where(function($query) use ($values, $CO) {
                if(!empty($values['campoBuscar'])) {
                    $query->orWhere("$CO.nombre", 'like', "%".$values['campoBuscar']."%");
                }
            })
            ->orderBy("$CO.nombre")
            ->get();

            $response = [
                'compradores'=> $compradores
            ];

            return ['error' => 0, 'info' => $response];
        } catch(Exception $e) {
            error_log($e,0);
            return ['error' => 1,'info'=> (string)$e];
        }
    }

    public function borrarComprador(Request $request) {
        try {
            $comprador = CompradoresModel::findOrFail($request->idComprador);
            if(!$comprador->delete()){
                DB::rollback();
                return ['error' => 1, 'info' => 'No ha sido posible eliminar comprador.'];
            }

            return ['error' => 0, 'info' => 'Comprador borrado correctamente'];
        } catch(Exception $e) {
            error_log($e,0);
            return ['error' => 1,'info'=> (string)$e];
        }
    }
}
