<?php

namespace App\Http\Controllers\Cuenta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CuentaController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('negocio');

        return view('home.cuenta', compact('user'));
    }

    public function updatePerfil(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max'      => 'El nombre no puede superar los 255 caracteres.',
            'telefono.max'  => 'El teléfono no puede superar los 20 caracteres.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function updateNegocio(Request $request)
    {
        $negocio = Auth::user()->negocio;

        $validated = $request->validate([
            'nombre'    => ['required', 'string', 'max:255'],
            'telefono'  => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ], [
            'nombre.required' => 'El nombre del negocio es obligatorio.',
            'nombre.max'      => 'El nombre no puede superar los 255 caracteres.',
            'telefono.max'    => 'El teléfono no puede superar los 50 caracteres.',
            'direccion.max'   => 'La dirección no puede superar los 255 caracteres.',
        ]);

        $negocio->update($validated);

        return back()->with('success', 'Datos del negocio actualizados correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'Ingresá tu contraseña actual.',
            'password.required'         => 'La nueva contraseña es obligatoria.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'La contraseña actual es incorrecta.'])
                ->withInput();
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Contraseña cambiada correctamente.');
    }
}
