<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reparacion extends Model
{
    public function usuario(){
        return $this->belongsTo(User::class);
    }
    public function dispositivo(){
        return $this->belongTo(Dispositivo::class);
    }
    public function negocio(){
        return $this->belongsTo(Negocio::class);
    }
    //
    protected $table = 'reparaciones';
    protected $fillable = [
        'negocios_id',
        'dispositivos_id',
        'users_id',
        'falla_reportada',
        'clave_de_acceso',
        'estado',
        'costo_estimado',
        'sena',
        'notas_internas',
        'codigo_seguimiento'
    ];
}
