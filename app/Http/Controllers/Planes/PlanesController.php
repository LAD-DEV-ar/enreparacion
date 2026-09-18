<?php

namespace App\Http\Controllers\Planes;

use App\Http\Controllers\Controller;
use App\Models\Suscripcion;
use Illuminate\Http\Request;

class PlanesController extends Controller
{
    public function index()
    {
        return view('home.planes');
    }
    public function store(Request $request)
    {
        // Obtener el negocio y validar que el usuario tenga un negocio asociado
        $negocio = auth()->user()->negocio;
        if (!$negocio) {
            return redirect()->route('negocios')
                ->with('info', 'No tienes todavia un negocio asociado o creado en tu cuenta');
        }
        
        Suscripcion::create([
            'negocios_id' => $negocio->id,
            'plan_id' => $request->plan,
            'estado' => true,
            'inicio' => now(),
            'fin' => now()->addMonth(),
            'ultimo_pago' => now(),
            'proxima_facturacion' => now()->addMonth(),
        ]);
        return redirect()->route('dashboard.index')
            ->with('success', '¡Bienvenido! Tu negocio fue registrado y tu plan activado.');
    }
}
