<?php

namespace App\Http\Controllers\Negocios;

use App\Http\Controllers\Controller;
use App\Models\Negocio;
use App\Models\Plan;
use App\Models\Suscripcion;
use Illuminate\Http\Request;

class NegocioController extends Controller
{
    public function index()
    {
        $planes = Plan::all();
        $planes = $planes->all();

        return view('negocios.registro-negocios', compact('planes'));
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

        $trialEndsAt = now()->addDays((int) config('mercadopago.trial_days', 30));

        Suscripcion::create([
            'negocios_id' => $negocio->id,
            'tipo' => 'trial',
            'estado' => true,
            'inicio' => now(),
            'fin' => $trialEndsAt,
            'proxima_facturacion' => $trialEndsAt,
        ]);

        return response()->json([
            'negocio_id' => $negocio->id,
        ]);
    }
}
