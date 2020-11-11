<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class BoletaModel extends Model
{
    protected $table = 'boletas';
    protected $fillable = ['evento','fecha','comprador'];
}
