<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispositivo extends Model
{
    //
    protected $fillable = [
        'clientes_id',
        'marca_y_modelo',
        'imei_o_serie'
    ];
}
