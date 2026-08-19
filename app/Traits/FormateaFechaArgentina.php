<?php

namespace App\Traits;

use Carbon\Carbon;
use DateTimeInterface;

trait FormateaFechaArgentina
{
    /**
     * Zona horaria oficial de Argentina.
     */
    protected string $zonaHorariaArgentina = 'America/Argentina/Buenos_Aires';

    /**
     * Convierte una fecha/hora (string, DateTime, Carbon) a una instancia de Carbon
     * configurada en la zona horaria de Argentina y en idioma español.
     *
     * @param  DateTimeInterface|string|null  $fecha
     * @return Carbon|null
     */
    public function aCarbonArgentina($fecha = null): ?Carbon
    {
        if (!$fecha) {
            return null;
        }

        if ($fecha instanceof Carbon) {
            return $fecha->copy()->setTimezone($this->zonaHorariaArgentina)->locale('es');
        }

        if ($fecha instanceof DateTimeInterface) {
            return Carbon::instance($fecha)->setTimezone($this->zonaHorariaArgentina)->locale('es');
        }

        try {
            return Carbon::parse($fecha)->setTimezone($this->zonaHorariaArgentina)->locale('es');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Formatea una fecha y hora al horario argentino (por defecto: 'd/m/Y H:i').
     * Ejemplo: '19/08/2026 13:45'
     *
     * @param  DateTimeInterface|string|null  $fecha
     * @param  string  $formato
     * @param  string  $default
     * @return string
     */
    public function formatearFechaHoraArgentina($fecha, string $formato = 'd/m/Y H:i', string $default = '-'): string
    {
        $carbon = $this->aCarbonArgentina($fecha);

        return $carbon ? $carbon->format($formato) : $default;
    }

    /**
     * Formatea solo la fecha en formato argentino (por defecto: 'd/m/Y').
     * Ejemplo: '19/08/2026'
     *
     * @param  DateTimeInterface|string|null  $fecha
     * @param  string  $formato
     * @param  string  $default
     * @return string
     */
    public function formatearSoloFechaArgentina($fecha, string $formato = 'd/m/Y', string $default = '-'): string
    {
        return $this->formatearFechaHoraArgentina($fecha, $formato, $default);
    }

    /**
     * Formatea solo la hora en horario argentino (por defecto: 'H:i' o 'H:i:s').
     * Ejemplo: '13:45'
     *
     * @param  DateTimeInterface|string|null  $fecha
     * @param  string  $formato
     * @param  string  $default
     * @return string
     */
    public function formatearHoraArgentina($fecha, string $formato = 'H:i', string $default = '-'): string
    {
        return $this->formatearFechaHoraArgentina($fecha, $formato, $default);
    }

    /**
     * Devuelve el tiempo relativo transcurrido en español y horario argentino.
     * Ejemplo: 'Ingreso hace 2 horas', 'hace 5 minutos'
     *
     * @param  DateTimeInterface|string|null  $fecha
     * @param  string  $prefijo
     * @param  string  $default
     * @return string
     */
    public function tiempoTranscurridoArgentina($fecha, string $prefijo = 'Ingreso ', string $default = 'Ingreso recientemente'): string
    {
        $carbon = $this->aCarbonArgentina($fecha);

        if (!$carbon) {
            return $default;
        }

        return $prefijo . $carbon->diffForHumans();
    }

    /**
     * Formatea una fecha de forma extendida y legible en español.
     * Ejemplo: '19 de agosto de 2026, 13:45 hs'
     *
     * @param  DateTimeInterface|string|null  $fecha
     * @param  string  $default
     * @return string
     */
    public function formatearFechaHumanaArgentina($fecha, string $default = '-'): string
    {
        $carbon = $this->aCarbonArgentina($fecha);

        if (!$carbon) {
            return $default;
        }

        return $carbon->translatedFormat('d \d\e F \d\e Y, H:i \h\s');
    }
}
