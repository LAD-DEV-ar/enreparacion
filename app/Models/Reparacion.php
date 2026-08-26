<?php

namespace App\Models;

use App\Traits\FormateaFechaArgentina;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reparacion extends Model
{
    use FormateaFechaArgentina, HasFactory;

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
        return $this->tiempoTranscurridoArgentina($this->created_at);
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
        'codigo_seguimiento',
    ];
}
