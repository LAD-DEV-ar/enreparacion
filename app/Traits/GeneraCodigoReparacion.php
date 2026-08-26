<?php

namespace App\Traits;

use App\Models\Reparacion;

trait GeneraCodigoReparacion
{
    public function generarCodigoSeguimiento(string $nombreCliente)
    {
        $palabras = explode(' ', trim($nombreCliente));

        if (count($palabras) >= 2) {
            $primera = strtoupper(substr($palabras[0], 0, 1));
            $ultima = strtoupper(substr(end($palabras), 0, 1));
            $iniciales = $primera.$ultima;
        } else {
            $iniciales = strtoupper(substr($palabras[0], 0, 1)).'X';
        }

        do {
            $numero = random_int(1000, 9999);
            $codigo = $iniciales.'-'.$numero;
        } while (Reparacion::where('codigo_seguimiento', $codigo)->exists());

        return $codigo;
    }
}
