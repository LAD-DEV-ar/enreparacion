<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispositivo extends Model
{
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'clientes_id');
    }

    public function reparaciones()
    {
        return $this->hasMany(Reparacion::class, 'dispositivos_id');
    }
    //
    protected $fillable = [
        'clientes_id',
        'marca_y_modelo',
        'imei_o_serie'
    ];
}
