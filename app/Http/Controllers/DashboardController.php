<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\Reparacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('home.dashboard');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'falla_reportada' => ['required', 'string'],
            'sena' => ['nullable', 'numeric'],
            'marca_y_modelo' => ['required', 'string', 'max:255'],
            'clave_de_acceso' => ['nullable', 'string', 'max:255'],
            'imei_o_serie' => ['nullable', 'string', 'max:255'],
            'valor' => ['nullable', 'numeric'],
        ], [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'falla_reportada.required' => 'La falla reportada es obligatoria.',
            'marca_y_modelo.required' => 'La marca y modelo son obligatorios.',
            'email.email' => 'El correo electrónico no es válido.',
            'sena.numeric' => 'La seña debe ser un valor numérico.',
            'valor.numeric' => 'El valor debe ser un valor numérico.',
        ]);

        $userId = Auth::id();
        $user = User::find($userId);
        

        $cliente = Cliente::create([
            'negocios_id' => $user->negocios_id, // Solo prueba 
            'nombre' => $validated['nombre'],
            'telefono' => $validated['telefono'],
            'email' => $validated['email']
        ]);

        $dispositivo = Dispositivo::create([
            'clientes_id' => $cliente->id,
            'marca_y_modelo' => $validated['marca_y_modelo'],
            'imei_o_serie' => $validated['imei_o_serie'],
        ]);

        Reparacion::create([
            'negocios_id' => $user->negocios_id, // Solo prueba
            'dispositivos_id' => $dispositivo->id, // Solo prueba
            'users_id' => $user->id, // Solo prueba
            'falla_reportada' => $validated['falla_reportada'],
            'clave_de_acceso' => $validated['clave_de_acceso'],
            'estado' => $validated['estado'],
            'costo_estimado' => $validated['estado'],
            'sena' => $validated['sena'],
            'notas_internas' => $validated['notas_internas'],
        ]);

        return redirect()->route('dashboard.index')->with('success', 'Reparación registrada correctamente.');
    }
}
