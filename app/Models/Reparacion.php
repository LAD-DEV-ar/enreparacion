<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reparacion extends Model
{
    public function usuario()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class, 'dispositivos_id');
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'negocios_id');
    }

    public function getClienteAttribute()
    {
        return $this->dispositivo?->cliente;
    }

    public function getTiempoTranscurridoAttribute(): string
    {
        if (!$this->created_at) {
            return 'Ingreso recientemente';
        }
        return 'Ingreso ' . $this->created_at->locale('es')->diffForHumans();
    }

    public function getSaldoPendienteAttribute(): float
    {
        $costo = (float) ($this->costo_estimado ?? 0);
        $sena = (float) ($this->sena ?? 0);
        return max(0, $costo - $sena);
    }

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
