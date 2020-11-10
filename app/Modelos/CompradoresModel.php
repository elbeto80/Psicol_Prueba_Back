<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class CompradoresModel extends Model
{
    protected $table = 'compradores';
    protected $fillable = ['documento','nombre','apellido','telefono','correo'];
}
