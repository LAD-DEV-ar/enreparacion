<?php

namespace App\Http\Middleware;

use App\Models\Suscripcion;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IfDoesntHaveASuscription
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();
        $negocio = $usuario->negocio;

        if (! $negocio) {
            return redirect()->route('negocios');
        }

        $suscripcion = $negocio->suscripcion;

        if (! $suscripcion || ! $this->suscripcionActiva($suscripcion)) {
            return redirect()->route('planes.index');
        }

        return $next($request);
    }

    private function suscripcionActiva(Suscripcion $suscripcion): bool
    {
        if (! $suscripcion->tipo || $suscripcion->tipo === 'trial') {
            return (bool) $suscripcion->estado
                && $suscripcion->fin->isFuture();
        }

        return (bool) $suscripcion->estado
            && $suscripcion->mp_status === 'authorized';
    }
}
