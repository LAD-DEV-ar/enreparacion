<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reparacion extends Model
{
    //
    protected $table = 'reparaciones';
    protected $fillable = [
        'negocios_id',
        'dispositivos_id',
        'users_id',
        'falla_reportada',
        'patron_desbloqueo',
        'estado',
        'costo_estimado',
        'sena',
        'notas_internas'
    ];
}
