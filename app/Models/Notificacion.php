<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'negocios_id',
        'canal',
        'evento',
        'titulo',
        'mensaje',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'negocios_id');
    }
}
