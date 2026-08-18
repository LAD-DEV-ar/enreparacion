<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Negocio extends Model
{
    public function reparaciones(){
        return $this->hasMany(Reparacion::class, 'negocios_id');
    }
    public function usuarios(){
        return $this->hasMany(User::class, 'negocios_id');
    }
    public $fillable = [
        'nombre',
        'direccion',
        'telefono'
    ];
}
