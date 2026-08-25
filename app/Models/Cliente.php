<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    public function dispositivos()
    {
        return $this->hasMany(Dispositivo::class, 'clientes_id');
    }

    public function reparaciones()
    {
        return $this->hasManyThrough(Reparacion::class, Dispositivo::class, 'clientes_id', 'dispositivos_id');
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'negocios_id');
    }

    public function getInicialesAttribute(): string
    {
        $nombre = trim($this->nombre ?? '');
        if (empty($nombre)) {
            return '?';
        }

        $palabras = preg_split('/\s+/', $nombre);
        if (count($palabras) >= 2) {
            $primera = mb_substr($palabras[0], 0, 1);
            $ultima = mb_substr($palabras[count($palabras) - 1], 0, 1);
            return mb_strtoupper($primera . $ultima);
        }

        return mb_strtoupper(mb_substr($palabras[0], 0, min(2, mb_strlen($palabras[0]))));
    }

    public function getWhatsappUrlAttribute(): string
    {
        $raw = preg_replace('/[^0-9]/', '', $this->telefono ?? '');
        if (empty($raw)) {
            return '#';
        }

        if (str_starts_with($raw, '0')) {
            $raw = substr($raw, 1);
        }

        if (strlen($raw) === 10) {
            $raw = '549' . $raw;
        } elseif (strlen($raw) === 11 && str_starts_with($raw, '15')) {
            $raw = '549' . substr($raw, 2);
        } elseif (str_starts_with($raw, '54') && !str_starts_with($raw, '549') && strlen($raw) === 12) {
            $raw = '549' . substr($raw, 2);
        }

        return 'https://wa.me/' . $raw;
    }

    protected $fillable = [
        'negocios_id',
        'nombre',
        'telefono',
        'email'
    ];
}
