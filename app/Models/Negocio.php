<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Negocio extends Model
{
    public $fillable = [
        'nombre',
        'direccion',
        'telefono'
    ];
}
