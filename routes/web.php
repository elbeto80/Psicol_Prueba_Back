<?php

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'cors'], function(){
    Route::get('/getCompradores', 'CompradoresController@getCompradores');
    Route::post('/compradores/save', 'CompradoresController@guardarComprador');
    Route::post('/comprador/delete', 'CompradoresController@borrarComprador');
});