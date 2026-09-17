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
    
        if (!$negocio) {
            return redirect()->route('negocios');
        }
    
        $suscripcion = $negocio->suscripcion;

        if (!$suscripcion || $suscripcion->estado !== 'activa') {
            return redirect()->route('planes.index');
        }
    
        return $next($request);
    }
}
