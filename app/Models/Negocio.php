<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Negocio extends Model
{
    use HasFactory;

    public function reparaciones()
    {
        return $this->hasMany(Reparacion::class, 'negocios_id');
    }

    public function usuarios()
    {
        return $this->hasMany(User::class, 'negocios_id');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'negocios_id');
    }

    public function plantillasNotificaciones()
    {
        return $this->hasMany(Notificacion::class, 'negocios_id');
    }

    public function notificacionesEnviadas()
    {
        return $this->hasMany(NotificacionCliente::class, 'negocios_id');
    }

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
    ];
}
