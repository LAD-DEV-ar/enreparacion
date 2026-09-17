<?php

namespace App\Http\Controllers\Negocios;

use App\Http\Controllers\Controller;
use App\Models\Negocio;
use App\Models\Suscripcion;
use Illuminate\Http\Request;

class NegocioController extends Controller
{
    public function index()
    {
        return view('negocios.registro-negocios');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (! empty($user->negocios_id)) {
            return response()->json([
                'error' => 'Ya tienes un negocio vinculado a tu cuenta',
            ], 403);
        }

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
        ], [
            'nombre.required' => 'El nombre del negocio es obligatorio.',
        ]);

        $negocio = Negocio::create([
            'nombre' => $validated['nombre'],
            'direccion' => $validated['direccion'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
        ]);

        $user->negocios_id = $negocio->id;
        $user->save();

        return response()->json([
            'negocio_id' => $negocio->id,
        ]);
    }

    public function suscribir(Request $request)
    {
        $validated = $request->validate([
            'negocios_id' => ['required', 'exists:negocios,id'],
            'plan_id' => ['required', 'exists:planes,id'],
        ]);

        $suscripcion = Suscripcion::create([
            'negocios_id' => $validated['negocios_id'],
            'plan_id' => $validated['plan_id'],
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
