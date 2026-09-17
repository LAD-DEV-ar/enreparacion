<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    use HasFactory;

    protected $table = 'suscripciones';

    protected $fillable = [
        'negocios_id',
        'plan_id',
        'estado',
        'inicio',
        'fin',
        'ultimo_pago',
        'proxima_facturacion',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'inicio' => 'datetime',
        'fin' => 'datetime',
        'ultimo_pago' => 'datetime',
        'proxima_facturacion' => 'datetime',
    ];

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'negocios_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
