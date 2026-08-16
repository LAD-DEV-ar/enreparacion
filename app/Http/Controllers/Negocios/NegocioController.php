<?php

namespace App\Http\Controllers\Negocios;

use App\Http\Controllers\Controller;
use App\Models\Negocio;
use App\Models\User;
use Illuminate\Http\Request;

class NegocioController extends Controller
{
    public function index(){
        return view('negocios.registro-negocios');
    }
    public function store(Request $request){

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:255']
        ], [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'direccion.required' => 'La direccion es obligatoria'
        ]);

        $negocio = Negocio::create([
            'nombre' => $validated['nombre'],
            'direccion' => $validated['direccion'],
            'telefono' => $validated['telefono']
        ]);

        $user = auth()->user();
        $user->negocios_id = $negocio->id;
        $user->save();

        return redirect()->route('dashboard.index')->with('success', '¡Bienvenido, ya registramos tu negocio!');
    }
}
