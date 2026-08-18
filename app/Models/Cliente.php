<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    public function dispositivos()
    {
        return $this->hasMany(Dispositivo::class, 'clientes_id');
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'negocios_id');
    }
    protected $fillable = [
        'negocios_id',
        'nombre',
        'telefono',
        'email'
    ];
}
