<?php

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'cors'], function(){
    Route::get('/getCompradores', 'CompradoresController@getCompradores');
    Route::post('/compradores/save', 'CompradoresController@guardarComprador');
    Route::post('/comprador/delete', 'CompradoresController@borrarComprador');

    Route::get('/getBoletas', 'BoletasController@getBoletas');
    Route::post('/boleta/save', 'BoletasController@guardarBoleta');
    Route::post('/boleta/delete', 'BoletasController@borrarBoleta');
});