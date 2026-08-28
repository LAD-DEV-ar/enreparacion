<?php

namespace App\Models;

use App\Traits\FormateaFechaArgentina;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionCliente extends Model
{
    use FormateaFechaArgentina, HasFactory;

    protected $table = 'notificacion_clientes';

    protected $fillable = [
        'negocios_id',
        'reparaciones_id',
        'clientes_id',
        'users_id',
        'canal',
        'destinatario',
        'asunto',
        'mensaje',
        'estado_envio',
        'error_mensaje',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'negocios_id');
    }

    public function reparacion()
    {
        return $this->belongsTo(Reparacion::class, 'reparaciones_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'clientes_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function getFechaFormateadaAttribute(): string
    {
        return $this->formatearFechaHoraArgentina($this->created_at);
    }
}
